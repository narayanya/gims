<?php

namespace App\Imports;

use App\Models\Crop;
use App\Models\Category;
use App\Models\CropCategory;
use App\Models\CropType;
use App\Models\Season;
use App\Models\SoilType;
use App\Models\Pouch;
use App\Models\Unit;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Throwable;

class CropImport implements ToModel, WithHeadingRow
{
    
    public function __construct()
    {
        HeadingRowFormatter::default('none');
    }
    public function model(array $row)
    {
        // Skip completely empty rows
        if (empty(trim($row['Crop Name'] ?? ''))) {
            return null;
        }

        // Normalize update_status: 'yes'/'no'/1/0 → 1/0
        $updateStatus = $row['update_status'] ?? 0;
        if (is_string($updateStatus)) {
            $updateStatus = strtolower(trim($updateStatus)) === 'yes' ? 1 : 0;
        }
      

        return new Crop([
            'crop_name'       => $row['Crop Name'] ?? null,
            'scientific_name' => $row['Scientific Name'] ?? null,
            'common_name'     => $row['Common Name'] ?? null,

            'crop_code'       => $row['Code'] ?? null,

            'vertical_id'     => $row['Vertical ID'] ?? null,

            'numeric_code'    => $row['Numeric Code'] ?? null,

            'effective_date'  => $row['Effective Date'] ?? null,

            'focus_code'      => $row['Focus Code'] ?? null,

            'family_name'     => $row['Family Name'] ?? null,

            'genus'           => $row['Genus'] ?? null,

            'species'         => $row['Species'] ?? null,
            // CSV DATA
            
            'description'            => $row['Description'] ?? null,
            
            'duration_days'          => $row['Duration Days to Maturity'] ?? null,
            'sowing_time'            => $row['Sowing Time'] ?? null,
            'harvest_time'           => $row['Harvest Time'] ?? null,
            'climate_requirement'    => $row['Climate Requirement'] ?? null,
            'isolation_distance'     => $row['Isolation Distance Meters'] ?? null,
            'expected_yield'         => $row['Expected Yield qtlacre'] ?? null,
            'seed_quantity'          => $row['Number of Seed/Quantity'] ?? null,
            'seed_weight'            => $row['Average Seed Weightg'] ?? null,
            'regeneration_cut_year'  => $row['Regenaration Cut of Year'] ?? null,

            // RELATION TABLE IDS
            'category_id' => Category::where(
                'name', 'like', trim($row['Category'] ?? '')
            )->value('id'),

            'crop_category_id' => CropCategory::where(
                'name', 'like', trim($row['Crop Category'] ?? '')
            )->value('id'),

            'crop_type_id' => CropType::where(
                'name', 'like', trim($row['Crop Type'] ?? '')
            )->value('id'),

            'season_id' => Season::where(
                'name', 'like', trim($row['Season'] ?? '')
            )->value('id'),

            'soil_type_id' => SoilType::where(
                'name', 'like', trim($row['Soil Type'] ?? '')
            )->value('id'),

            'pouch_standard_id' => Pouch::where(
                'name', 'like', trim($row['Pouch Standard'] ?? '')
            )->value('id'),

            'unit_id' => Unit::where(
                'name', 'like', trim($row['Unit'] ?? '')
            )->value('id'),

            // MONTH IDS
            'season_start_month_id' => $this->getMonthId($row['Start Month'] ?? null),
            'season_end_month_id'   => $this->getMonthId($row['End Month'] ?? null),

            // AUTO FILL DEFAULT VALUES
           
            'crop_flag'      => 'ACTIVE',

            // STATUS
            'is_active'    => 1,
            'update_status' => $updateStatus,
        ]);
    }

    private function generateCropCode()
    {
        $maxId = Crop::max('id') ?? 0;

        return 'CRP' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
    }

    private function getMonthId($month)
    {
        if (!$month) {
            return null;
        }

        $months = [
            'january' => 1,
            'february' => 2,
            'march' => 3,
            'april' => 4,
            'may' => 5,
            'june' => 6,
            'july' => 7,
            'august' => 8,
            'september' => 9,
            'october' => 10,
            'november' => 11,
            'december' => 12,
        ];

        return $months[strtolower(trim($month))] ?? null;
    }
}