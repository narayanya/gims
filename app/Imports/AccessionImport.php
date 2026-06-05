<?php

namespace App\Imports;

use App\Models\Accession;
use App\Models\Crop;
use App\Models\StorageTime;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\CoreCityVillage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AccessionImport implements ToCollection, WithHeadingRow
{
    /** Rows successfully inserted */
    private int $inserted = 0;

    /** Rows skipped with reason */
    private array $skipped = [];

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function normalize(mixed $value): string
    {
        return strtolower(trim((string) ($value ?? '')));
    }

    private function str(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        return $v === '' ? null : $v;
    }

    /**
     * Parse dates in common formats: d-m-Y, d/m/Y, Y-m-d, m/d/Y
     * Also handles Excel numeric serial dates.
     */
    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Excel numeric serial date (e.g. 45792)
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                    ->format('Y-m-d');
            } catch (\Throwable) {}
        }

        $value = trim((string) $value);

        foreach (['d-m-Y', 'd/m/Y', 'Y-m-d', 'm/d/Y', 'd.m.Y'] as $fmt) {
            $date = \DateTime::createFromFormat($fmt, $value);
            if ($date && $date->format($fmt) === $value) {
                return $date->format('Y-m-d');
            }
        }

        Log::warning('AccessionImport: Unrecognized date format', ['value' => $value]);
        return null;
    }

    // ── Main ───────────────────────────────────────────────────────────────────

    public function collection(Collection $rows)
    {
        Log::info('AccessionImport: Starting import', ['total_rows' => $rows->count()]);

        foreach ($rows as $rowIndex => $row) {

            $lineNum = $rowIndex + 2; // +2 because row 1 is header

            // Log every row keys once (first row only) so we can see exact column names
            if ($rowIndex === 0) {
                Log::info('AccessionImport: Column keys from file', ['keys' => array_keys($row->toArray())]);
            }

            // ── Skip blank rows ────────────────────────────────────────────
            $sampleId = $this->str($row['sample_id'] ?? null);
            if ($sampleId === null) {
                Log::debug('AccessionImport: Skipping blank row', ['line' => $lineNum]);
                continue;
            }

            // ── Normalize enum fields ──────────────────────────────────────
            $accSource = $this->normalize($row['acc_source'] ?? 'internal');
            if (!in_array($accSource, ['internal', 'external'])) {
                $accSource = 'internal';
            }

            $requesterShow = $this->normalize($row['requester_show'] ?? 'yes');
            if (!in_array($requesterShow, ['yes', 'no'])) {
                $requesterShow = 'yes';
            }

            $barcodeType = $this->normalize($row['barcode_type'] ?? 'auto');
            if (!in_array($barcodeType, ['auto', 'manual', 'existing', 'scan', 'none'])) {
                $barcodeType = 'auto';
            }

            $statusRaw   = $this->normalize($row['status'] ?? '1');
            $statusValue = match (true) {
                in_array($statusRaw, ['active', '1'])   => 1,
                in_array($statusRaw, ['inactive', '0']) => 0,
                default                                  => 1,
            };

            // ── Crop lookup ────────────────────────────────────────────────
            $cropName = $this->normalize($row['crop_name'] ?? '');
            if ($cropName === '') {
                $this->skip($lineNum, 'crop_name is empty');
                continue;
            }

            $crop = Crop::whereRaw('LOWER(crop_name) = ?', [$cropName])->first();
            if (!$crop) {
                $this->skip($lineNum, "Crop not found: '{$row['crop_name']}'");
                continue;
            }

            // ── Location lookups ───────────────────────────────────────────
            $countryName = $this->normalize($row['country_name'] ?? '');
            $country = $countryName
                ? Country::whereRaw('LOWER(country_name) = ?', [$countryName])->first()
                : null;

            $stateName = $this->normalize($row['state_name'] ?? '');
            $state = $stateName
                ? State::whereRaw('LOWER(state_name) = ?', [$stateName])->first()
                : null;

            $districtName = $this->normalize($row['district_name'] ?? '');
            $district = $districtName
                ? District::whereRaw('LOWER(district_name) = ?', [$districtName])->first()
                : null;

            // city_id column: CSV may have 'city_id' (direct FK) OR 'city_name' (lookup)
            $cityId = null;
            if (!empty($row['city_id']) && is_numeric($row['city_id'])) {
                // Direct ID provided
                $cityId = (int) $row['city_id'];
            } elseif (!empty($row['city_name'])) {
                $cityName = $this->normalize($row['city_name']);
                $city = CoreCityVillage::whereRaw('LOWER(city_village_name) = ?', [$cityName])->first();
                $cityId = $city?->id;
            }

            // ── Storage time ───────────────────────────────────────────────
            // Accepts: numeric ID (1/2/3), code (STS/MTS/LTS), or partial name
            $storageTimeId = null;
            $stRaw = trim((string) ($row['storage_time_id'] ?? ''));
            if ($stRaw !== '') {
                if (is_numeric($stRaw)) {
                    // Numeric → match by primary key
                    $storageTimeId = StorageTime::find((int) $stRaw)?->id;
                } else {
                    // Text → match by code first (STS, MTS, LTS), then by name
                    $st = StorageTime::whereRaw('UPPER(code) = ?', [strtoupper($stRaw)])->first()
                       ?? StorageTime::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($stRaw) . '%'])->first();
                    $storageTimeId = $st?->id;
                }

                if (!$storageTimeId) {
                    Log::warning('AccessionImport: storage_time_id not found', [
                        'line' => $lineNum, 'value' => $stRaw,
                    ]);
                }
            }

            // ── Insert ─────────────────────────────────────────────────────
            try {
                $accession = Accession::create([
                    'acc_source'      => $accSource,
                    'ext_source'      => $this->str($row['ext_source'] ?? null),

                    'sample_id'       => $sampleId,
                    'year_of_arrival' => $this->str($row['year_of_arrival'] ?? null) ?? date('Y'),
                    'requester_show'  => $requesterShow,

                    'accession_name'  => $this->str($row['accession_name'] ?? null),
                    'crop_id'         => $crop->id,

                    'collection_number' => $this->str($row['collection_number'] ?? null),
                    'collection_date'   => $this->parseDate($row['collection_date'] ?? null),
                    'collector_name'    => $this->str($row['collector_name'] ?? null),
                    'donor_name'        => $this->str($row['donor_name'] ?? null),
                    'collection_site'   => $this->str($row['collection_site'] ?? null),

                    'country_id'  => $country?->id,
                    'state_id'    => $state?->id,
                    'district_id' => $district?->id,
                    'city_id'     => $cityId,

                    'latitude'  => $this->str($row['latitude'] ?? null),
                    'longitude' => $this->str($row['longitude'] ?? null),
                    'pincode'   => $this->str($row['pincode'] ?? null),

                    'biological_status' => $this->str($row['biological_status'] ?? null),
                    'sample_type'       => $this->str($row['sample_type'] ?? null),
                    'reproductive_type' => $this->str($row['reproductive_type'] ?? null),

                    'storage_time_id' => $storageTimeId,

                    'barcode_type' => $barcodeType,
                    'barcode'      => $this->str($row['barcode'] ?? null),

                    'status' => $statusValue,
                    'notes'  => $this->str($row['notes'] ?? null),

                    'entry_date' => now(),
                    'entered_by' => auth()->id() ?? 1,
                    'created_by' => auth()->id() ?? 1,
                ]);

                $accessionNumber =
                    $crop->crop_code . '-' .
                    ($accession->year_of_arrival ?? date('Y')) .
                    '-ACC-' .
                    $accession->sample_id . '-' .
                    str_pad($accession->id, 5, '0', STR_PAD_LEFT);

                $accession->update(['accession_number' => $accessionNumber]);

                $this->inserted++;

                Log::info('AccessionImport: Inserted', [
                    'line'             => $lineNum,
                    'accession_number' => $accessionNumber,
                ]);

            } catch (\Throwable $e) {
                $this->skip($lineNum, $e->getMessage());
            }
        }

        // Store summary for the controller to show in flash message
        Cache::put(
            'import_results_' . (auth()->id() ?? 0),
            ['inserted' => $this->inserted, 'skipped' => $this->skipped],
            now()->addMinutes(2)
        );

        Log::info('AccessionImport: Finished', [
            'inserted' => $this->inserted,
            'skipped'  => count($this->skipped),
        ]);
    }

    private function skip(int $line, string $reason): void
    {
        $this->skipped[] = ['row' => $line, 'reason' => $reason];
        Log::warning('AccessionImport: Skipped row', ['line' => $line, 'reason' => $reason]);
    }
}
