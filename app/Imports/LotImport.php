<?php

namespace App\Imports;

use App\Models\Lot;
use App\Models\Accession;
use App\Models\Storage;
use App\Models\SeedQuantity;
use App\Models\SeedQuality;
use App\Models\Unit;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\Container;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotImport implements ToCollection, WithHeadingRow
{
    private int   $inserted = 0;
    private array $skipped  = [];

    // ── Helpers ────────────────────────────────────────────────────────────

    private function str(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        return $v === '' ? null : $v;
    }

    private function num(mixed $value): ?float
    {
        $v = trim((string) ($value ?? ''));
        return is_numeric($v) ? (float) $v : null;
    }

    /**
     * Parse dates: d/m/Y, d-m-Y, Y-m-d, m/d/Y, Excel serial.
     */
    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Excel serial date (e.g. 46025)
        if (is_numeric($value)) {
            $intVal = (int) $value;
            // 4-digit year only
            if ($intVal >= 1900 && $intVal <= 2100 && strlen((string) $intVal) === 4) {
                return $intVal . '-01-01';
            }
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                    ->format('Y-m-d');
            } catch (\Throwable) {}
        }

        $value = trim((string) $value);

        if (preg_match('/^\d{4}$/', $value)) {
            return $value . '-01-01';
        }

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y', 'd.m.Y'] as $fmt) {
            $date = \DateTime::createFromFormat($fmt, $value);
            if ($date && $date->format($fmt) === $value) {
                return $date->format('Y-m-d');
            }
        }

        Log::warning('LotImport: Unrecognized date format', ['value' => $value]);
        return null;
    }

    /**
     * Resolve researcher: accepts numeric ID or name string.
     * Returns user ID or null. Non-matched names go to researcher_other.
     */
    private function resolveResearcher(mixed $value): array
    {
        $v = $this->str($value);
        if ($v === null) {
            return ['researcher_id' => null, 'researcher_other' => null];
        }

        if (is_numeric($v)) {
            // Verify the user actually exists before using as FK
            $exists = User::where('id', (int) $v)->exists();
            if ($exists) {
                return ['researcher_id' => (int) $v, 'researcher_other' => null];
            }
            // ID not found — store as free-text
            return ['researcher_id' => null, 'researcher_other' => $v];
        }

        // Try to match by name
        $user = User::whereRaw('LOWER(name) = ?', [strtolower($v)])->first();
        if ($user) {
            return ['researcher_id' => $user->id, 'researcher_other' => null];
        }

        // Store as free-text
        return ['researcher_id' => null, 'researcher_other' => $v];
    }

    // ── Main ───────────────────────────────────────────────────────────────

    public function collection(Collection $rows)
    {
        Log::info('LotImport: Starting import', ['total_rows' => $rows->count()]);

        // Pre-load existing lot numbers for fast duplicate check
        $existingLots = Lot::pluck('lot_number')->flip()->toArray();
        $batchLots    = [];

        // Pre-load existing reference numbers (non-null) from lots table
        $existingRefs = Lot::whereNotNull('reference_number')
            ->pluck('reference_number')
            ->flip()
            ->toArray();
        $batchRefs = [];

        foreach ($rows as $rowIndex => $row) {
            $lineNum = $rowIndex + 2;

            // Log column keys once to help debugging
            if ($rowIndex === 0) {
                Log::info('LotImport: Column keys detected', ['keys' => array_keys($row->toArray())]);
            }

            // ── Skip blank rows ────────────────────────────────────────
            $accessionNumber = $this->str($row['accession_number'] ?? null);
            if ($accessionNumber === null) {
                Log::debug('LotImport: Skipping blank row', ['line' => $lineNum]);
                continue;
            }

            // ── Accession lookup ───────────────────────────────────────
            $accession = Accession::where('accession_number', $accessionNumber)->first();
            if (!$accession) {
                $this->skip($lineNum, "Accession not found: '{$accessionNumber}'");
                continue;
            }

            // ── Storage lookup: storage_id column first, then storage_name ──
            // The Excel column is named "storage_id" but may hold the storage
            // code (e.g. "STR-001") OR the storage name (e.g. "Central seed Vault").
            $storageColVal  = $this->str($row['storage_id']   ?? null);
            $storageNameVal = $this->str($row['storage_name'] ?? null);

            $storage = null;

            if ($storageColVal !== null) {
                // Try storage_id code first, then name
                $storage = Storage::where('storage_id', $storageColVal)
                    ->orWhere('name', $storageColVal)
                    ->first();
            }

            if (!$storage && $storageNameVal !== null) {
                $storage = Storage::where('name', $storageNameVal)
                    ->orWhere('storage_id', $storageNameVal)
                    ->first();
            }

            if (!$storage) {
                $ref = $storageColVal ?? $storageNameVal ?? '(empty)';
                $this->skip($lineNum, "Storage not found: '{$ref}'");
                continue;
            }

            // ── Quantity ───────────────────────────────────────────────
            $quantity = $this->num($row['quantity'] ?? null);
            if ($quantity === null || $quantity <= 0) {
                $this->skip($lineNum, 'quantity is missing or zero');
                continue;
            }

            // ── reference_number uniqueness ────────────────────────────
            // reference_number must be unique across all lots (not null)
            $referenceNumber = $this->str($row['reference_number'] ?? null);
            if ($referenceNumber !== null) {
                if (isset($existingRefs[$referenceNumber]) || isset($batchRefs[$referenceNumber])) {
                    $this->skip($lineNum, "Duplicate reference_number: '{$referenceNumber}' already exists");
                    continue;
                }
            }

            // ── Arrival type (needed for lot number generation below) ──
            $arrivalType = $this->str($row['arrival_type'] ?? null) ?? 'Accession Arrival';

            // ── lot_number (optional — auto-generate if blank) ─────────
            $lotNumber = $this->str($row['lot_number'] ?? null);

            if ($lotNumber) {
                if (isset($existingLots[$lotNumber]) || isset($batchLots[$lotNumber])) {
                    $this->skip($lineNum, "Duplicate lot_number: '{$lotNumber}'");
                    continue;
                }
            } else {
                // ── Auto-generate using EXACT same logic as LotController::managementStore ──
                $sampleId    = $accession->sample_id;
                $ref         = $referenceNumber;          // already resolved above
                $rowNum      = 1;                         // each import row = 1 lot
                $rejuvProg   = $this->str($row['rejuvenation_program'] ?? null);
                $prefix      = $this->str($row['prefix'] ?? null);

                switch ($arrivalType) {
                    case 'Rejuvenation':
                        $base = "{$ref}-{$rejuvProg}-{$prefix}-{$sampleId}-";
                        break;
                    case 'Accession Arrival':
                        $base = "{$ref}-AccA-{$sampleId}-";
                        break;
                    case 'Return From Field':
                        $base = "{$ref}-{$rejuvProg}-{$prefix}-{$sampleId}-";
                        break;
                    default:
                        $base = "{$ref}-{$sampleId}-";
                }

                $last = Lot::where('lot_number', 'like', $base . '%')
                    ->orderByRaw("CAST(SUBSTRING_INDEX(lot_number, '-', -1) AS UNSIGNED) DESC")
                    ->first();

                $seq = ($last && preg_match('/-(\d{2})$/', $last->lot_number, $m))
                    ? (int) $m[1] + 1
                    : 1;
                $seqPad = str_pad($seq, 2, '0', STR_PAD_LEFT);

                switch ($arrivalType) {
                    case 'Rejuvenation':
                        $lotNumber = "{$ref}-{$rejuvProg}/{$rowNum}-{$prefix}-{$sampleId}-{$seqPad}";
                        break;
                    case 'Accession Arrival':
                        $lotNumber = "{$ref}-AccA/{$rowNum}-{$sampleId}-{$seqPad}";
                        break;
                    case 'Return From Field':
                        $lotNumber = "{$ref}-{$rejuvProg}/{$rowNum}-{$prefix}-{$sampleId}-{$seqPad}-RF";
                        break;
                    default:
                        $lotNumber = "{$ref}-{$rowNum}-{$sampleId}-{$seqPad}";
                }
            }

            // ── Optional FK lookups ────────────────────────────────────
            $unitId = null;
            $unitRef = $this->str($row['unit'] ?? null);
            if ($unitRef) {
                $unit   = Unit::whereRaw('LOWER(name) = ?', [strtolower($unitRef)])
                    ->orWhereRaw('LOWER(code) = ?', [strtolower($unitRef)])
                    ->first();
                $unitId = $unit?->id;
            }

            $rackId = null;
            $rackName = $this->str($row['rack'] ?? null);
            if ($rackName) {
                $rackId = Rack::whereRaw('LOWER(name) = ?', [strtolower($rackName)])->first()?->id;
            }

            $binId = null;
            $binName = $this->str($row['bin'] ?? null);
            if ($binName) {
                $binId = Bin::whereRaw('LOWER(name) = ?', [strtolower($binName)])->first()?->id;
            }

            $containerId = null;
            $containerName = $this->str($row['container'] ?? null);
            if ($containerName) {
                $containerId = Container::whereRaw('LOWER(name) = ?', [strtolower($containerName)])->first()?->id;
            }

            // ── regen_year: store as-is (can be a small number like 4, not a calendar year) ──
            $regenYear = $this->str($row['regen_year'] ?? null);

            // ── Quality fields ─────────────────────────────────────────
            // Excel headers use: germination_percent, chlorophyll_percentage,
            // water_level_percentage — handle both naming variants.
            $germ      = $this->num($row['germination_percent']    ?? $row['germination_percentage']    ?? null);
            $mois      = $this->num($row['moisture_content']       ?? null);
            $purity    = $this->num($row['purity_percent']         ?? $row['purity_percentage']         ?? null);
            $chloro    = $this->num($row['chlorophyll_percentage'] ?? $row['chlorophyll_percent']       ?? null);
            $waterLvl  = $this->num($row['water_level_percentage'] ?? $row['water_level_percent']       ?? null);

            $researcher = $this->resolveResearcher($row['researcher_id'] ?? null);

            // ── Insert ─────────────────────────────────────────────────
            try {
                DB::beginTransaction();

                $lot = Lot::create([
                    'lot_number'           => $lotNumber,
                    'arrival_type'         => $arrivalType,
                    'reference_number'     => $referenceNumber,
                    'rejuvenation_program' => in_array($arrivalType, ['Rejuvenation', 'Return From Field'])
                                                ? $this->str($row['rejuvenation_program'] ?? null)
                                                : null,
                    'prefix'               => in_array($arrivalType, ['Rejuvenation', 'Return From Field'])
                                                ? $this->str($row['prefix'] ?? null)
                                                : null,
                    'sample_id'            => $accession->sample_id,
                    'accession_id'         => $accession->id,
                    'crop_id'              => $accession->crop_id,
                    'storage_id'           => $storage->id,
                    'rack_id'              => $rackId,
                    'bin_id'               => $binId,
                    'container_id'         => $containerId,
                    'unit_id'              => $unitId,
                    'expiry_date'          => $this->parseDate($row['expiry_date']       ?? null),
                    'regeneration_date'    => $this->parseDate($row['regeneration_date'] ?? null),
                    'regen_year'           => $regenYear,
                    'description'          => $this->str($row['description'] ?? null),
                    'status'               => $this->str($row['status'] ?? null) ?? 'active',
                ]);

                SeedQuantity::create([
                    'lot_id'           => $lot->id,
                    'accession_id'     => $accession->id,
                    'reference_number' => $referenceNumber,
                    'number_of_seeds'  => $this->num($row['number_of_seeds']  ?? null),
                    'number_of_bags'   => $this->num($row['number_of_bags']   ?? null),
                    'per_seed_weight'  => $this->num($row['per_seed_weight']  ?? null),
                    'quantity'         => $quantity,
                    'capacity_unit_id' => $unitId,
                    'quantity_show'    => $this->num($row['quantity_show']    ?? null) ?? $quantity,
                    'min_quantity'     => $this->num($row['min_quantity']     ?? null),
                ]);

                // Quality row — save if any quality value is present
                if ($germ !== null || $mois !== null || $purity !== null
                    || $chloro !== null || $waterLvl !== null)
                {
                    SeedQuality::create([
                        'lot_id'                 => $lot->id,
                        'accession_id'           => $accession->id,
                        'germination_percentage' => $germ,
                        'moisture_content'       => $mois,
                        'purity_percentage'      => $purity,
                        'chlorophyll_percentage' => $chloro,
                        'water_level_percentage' => $waterLvl,
                        'seed_health_status'     => $this->str($row['seed_health_status'] ?? null),
                        'viability_test_date'    => $this->parseDate($row['viability_test_date'] ?? null),
                        'researcher_id'          => $researcher['researcher_id'],
                        'researcher_other'       => $researcher['researcher_other'],
                        'research_date'          => $this->parseDate($row['research_date'] ?? null),
                    ]);
                }

                // Recalculate storage current_usage
                $totalUsed = SeedQuantity::whereIn(
                    'lot_id', $storage->lots()->pluck('id')
                )->sum('quantity');
                $storage->update(['current_usage' => $totalUsed]);

                DB::commit();

                $existingLots[$lotNumber] = true;
                $batchLots[$lotNumber]    = true;
                if ($referenceNumber !== null) {
                    $existingRefs[$referenceNumber] = true;
                    $batchRefs[$referenceNumber]    = true;
                }
                $this->inserted++;

                Log::info('LotImport: Inserted', [
                    'line'       => $lineNum,
                    'lot_number' => $lotNumber,
                ]);

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->skip($lineNum, $e->getMessage());
            }
        }

        Cache::put(
            'lot_import_results_' . (auth()->id() ?? 0),
            ['inserted' => $this->inserted, 'skipped' => $this->skipped],
            now()->addMinutes(5)
        );

        Log::info('LotImport: Finished', [
            'inserted' => $this->inserted,
            'skipped'  => count($this->skipped),
        ]);
    }

    private function skip(int $line, string $reason): void
    {
        $this->skipped[] = ['row' => $line, 'reason' => $reason];
        Log::warning('LotImport: Skipped row', ['line' => $line, 'reason' => $reason]);
    }
}
