<?php

namespace App\Imports;

use App\Models\OldDispatchData;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OldDispatchDataImport implements ToCollection, WithHeadingRow
{
    private int   $inserted = 0;
    private array $skipped  = [];

    // ── Helpers ────────────────────────────────────────────────────────────

    private function str(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        return $v === '' ? null : $v;
    }

    private function num(mixed $value): ?int
    {
        $v = trim((string) ($value ?? ''));
        return is_numeric($v) ? (int) $v : null;
    }

    /**
     * Parse dates: d/m/Y, d-m-Y, Y-m-d, m/d/Y, Excel serial,
     * d-M-y (16-Nov-16), d-M-Y (16-Nov-2016), d/M/Y, d M Y etc.
     */
    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Excel serial date
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                    ->format('Y-m-d');
            } catch (\Throwable) {}
        }

        $value = trim((string) $value);

        // Try strtotime first — handles "16-Nov-16", "16-Nov-2016", "Nov 16 2016" etc.
        $ts = strtotime($value);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        // Explicit format list as fallback
        foreach ([
            'd/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y', 'd.m.Y',
            'd-M-y', 'd-M-Y', 'd/M/y', 'd/M/Y',
            'j-M-y', 'j-M-Y', 'j/M/y', 'j/M/Y',
        ] as $fmt) {
            $date = \DateTime::createFromFormat($fmt, $value);
            if ($date && $date->format($fmt) === $value) {
                return $date->format('Y-m-d');
            }
        }

        Log::warning('OldDispatchDataImport: Unrecognized date format', ['value' => $value]);
        return null;
    }

    // ── Main ───────────────────────────────────────────────────────────────

    public function collection(Collection $rows)
    {
        Log::info('OldDispatchDataImport: Starting import', ['total_rows' => $rows->count()]);

        foreach ($rows as $rowIndex => $row) {
            $lineNum = $rowIndex + 2;

            if ($rowIndex === 0) {
                Log::info('OldDispatchDataImport: Column keys', ['keys' => array_keys($row->toArray())]);
            }

            // Skip completely blank rows
            $crop = $this->str($row['crop'] ?? null);
            if ($crop === null) {
                Log::debug('OldDispatchDataImport: Skipping blank row', ['line' => $lineNum]);
                continue;
            }

            try {
                DB::beginTransaction();

                OldDispatchData::create([
                    'crop'             => $crop,
                    'month'            => $this->str($row['month']            ?? null),
                    'year'             => $this->str($row['year']             ?? null),
                    'prefix'           => $this->str($row['prefix']           ?? null),
                    'sample_id'        => $this->str($row['sample_id']        ?? null),
                    'seed_weight'      => $this->str($row['seed_weight']      ?? $row['no_of_seeds_weight'] ?? null),
                    'no_packets'       => $this->num($row['no_packets']       ?? $row['no_of_packets']      ?? null),
                    'remarks'          => $this->str($row['remarks']          ?? null),
                    'concerned_person' => $this->str($row['concerned_person'] ?? null),
                    'location'         => $this->str($row['location']         ?? null),
                    'request_date'     => $this->parseDate($row['request_date']  ?? null),
                    'dispatch_date'    => $this->parseDate($row['dispatch_date'] ?? null),
                    'tracking_id'      => $this->str($row['tracking_id']      ?? null),
                    'courier_service'  => $this->str($row['courier_service']  ?? null),
                ]);

                DB::commit();
                $this->inserted++;

                Log::info('OldDispatchDataImport: Inserted row', ['line' => $lineNum, 'crop' => $crop]);

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->skipped[] = ['row' => $lineNum, 'reason' => $e->getMessage()];
                Log::warning('OldDispatchDataImport: Skipped row', ['line' => $lineNum, 'reason' => $e->getMessage()]);
            }
        }

        Log::info('OldDispatchDataImport: Finished', [
            'inserted' => $this->inserted,
            'skipped'  => count($this->skipped),
        ]);
    }

    public function getInserted(): int   { return $this->inserted; }
    public function getSkipped(): array  { return $this->skipped; }
}
