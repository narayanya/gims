<?php

namespace App\Imports;

use App\Models\Lot;
use App\Models\SeedQuality;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LotQualityImport implements ToCollection, WithHeadingRow
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
     * Parse dates: d/m/Y, d-m-Y, Y-m-d, Excel serial.
     */
    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            $intVal = (int) $value;
            if ($intVal >= 1900 && $intVal <= 2100 && strlen((string) $intVal) === 4) {
                return $intVal . '-01-01';
            }
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                    ->format('Y-m-d');
            } catch (\Throwable) {}
        }

        $value = trim((string) $value);

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y', 'd.m.Y'] as $fmt) {
            $date = \DateTime::createFromFormat($fmt, $value);
            if ($date && $date->format($fmt) === $value) {
                return $date->format('Y-m-d');
            }
        }

        Log::warning('LotQualityImport: Unrecognized date format', ['value' => $value]);
        return null;
    }

    /**
     * Resolve researcher: numeric ID (verified), name lookup, or free-text.
     */
    private function resolveResearcher(mixed $value): array
    {
        $v = $this->str($value);
        if ($v === null) {
            return ['researcher_id' => null, 'researcher_other' => null];
        }

        if (is_numeric($v)) {
            $exists = User::where('id', (int) $v)->exists();
            if ($exists) {
                return ['researcher_id' => (int) $v, 'researcher_other' => null];
            }
            return ['researcher_id' => null, 'researcher_other' => $v];
        }

        $user = User::whereRaw('LOWER(name) = ?', [strtolower($v)])->first();
        if ($user) {
            return ['researcher_id' => $user->id, 'researcher_other' => null];
        }

        return ['researcher_id' => null, 'researcher_other' => $v];
    }

    // ── Main ───────────────────────────────────────────────────────────────

    public function collection(Collection $rows)
    {
        Log::info('LotQualityImport: Starting import', ['total_rows' => $rows->count()]);

        foreach ($rows as $rowIndex => $row) {
            $lineNum = $rowIndex + 2;

            if ($rowIndex === 0) {
                Log::info('LotQualityImport: Column keys', ['keys' => array_keys($row->toArray())]);
            }

            // ── Required: lot_number ───────────────────────────────────
            $lotNumber = $this->str($row['lot_number'] ?? null);
            if ($lotNumber === null) {
                Log::debug('LotQualityImport: Skipping blank row', ['line' => $lineNum]);
                continue;
            }

            $lot = Lot::where('lot_number', $lotNumber)->first();
            if (!$lot) {
                $this->skip($lineNum, "Lot not found: '{$lotNumber}'");
                continue;
            }

            // ── Require at least one quality value ─────────────────────
            $germ     = $this->num($row['germination_percent']    ?? $row['germination_percentage']    ?? null);
            $mois     = $this->num($row['moisture_content']       ?? null);
            $purity   = $this->num($row['purity_percent']         ?? $row['purity_percentage']         ?? null);
            $chloro   = $this->num($row['chlorophyll_percentage'] ?? $row['chlorophyll_percent']       ?? null);
            $waterLvl = $this->num($row['water_level_percentage'] ?? $row['water_level_percent']       ?? null);

            if ($germ === null && $mois === null && $purity === null
                && $chloro === null && $waterLvl === null)
            {
                $this->skip($lineNum, "No quality values provided for lot '{$lotNumber}'");
                continue;
            }

            $researcher = $this->resolveResearcher($row['researcher_id'] ?? null);

            // ── Insert ─────────────────────────────────────────────────
            try {
                SeedQuality::create([
                    'lot_id'                 => $lot->id,
                    'accession_id'           => $lot->accession_id,
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

                $this->inserted++;
                Log::info('LotQualityImport: Inserted', ['line' => $lineNum, 'lot_number' => $lotNumber]);

            } catch (\Throwable $e) {
                $this->skip($lineNum, $e->getMessage());
            }
        }

        Cache::put(
            'lot_quality_import_results_' . (auth()->id() ?? 0),
            ['inserted' => $this->inserted, 'skipped' => $this->skipped],
            now()->addMinutes(5)
        );

        Log::info('LotQualityImport: Finished', [
            'inserted' => $this->inserted,
            'skipped'  => count($this->skipped),
        ]);
    }

    private function skip(int $line, string $reason): void
    {
        $this->skipped[] = ['row' => $line, 'reason' => $reason];
        Log::warning('LotQualityImport: Skipped row', ['line' => $line, 'reason' => $reason]);
    }
}
