<?php

namespace App\Exports;

use App\Models\Crop;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CropExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Crops';
    }

    public function query()
    {
        return Crop::with([
            'category',
            'cropCategory',
            'cropType',
            'season',
            'soilType',
            'unit',
            'pouchStandard',
        ])->orderBy('id');
    }

    public function headings(): array
    {
        return [
            // Identity
            '#',
            'Crop Name',
            'Crop Code',
            'Focus Code',
            'Numeric Code',
            'Vertical ID',
            'Crop Flag',
            'Effective Date',

            // Classification
            'Category',
            'Crop Category',
            'Crop Type',

            // Taxonomy
            'Scientific Name',
            'Common Name',
            'Family Name',
            'Genus',
            'Species',

            // Season & Growth
            'Season',
            'Start Month',
            'End Month',
            'Duration (Days to Maturity)',
            'Sowing Time',
            'Harvest Time',
            'Climate Requirement',

            // Soil & Agronomy
            'Soil Type',
            'Isolation Distance (m)',
            'Expected Yield (qtl/acre)',
            'Regeneration Cut of Year',

            // Seed Info
            'Seed Quantity',
            'Seed Weight (g)',
            'Unit',
            'Pouch Standard',

            // Description
            'Description',

            // Status
            'Is Active',
            'Update Status',
            'Updated Date',
            'Created At',
        ];
    }

    public function map($crop): array
    {
        static $index = 0;
        $index++;

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March',
            4 => 'April',   5 => 'May',       6 => 'June',
            7 => 'July',    8 => 'August',    9 => 'September',
            10 => 'October', 11 => 'November', 12 => 'December',
        ];

        return [
            // Identity
            $index,
            $crop->crop_name,
            $crop->crop_code,
            $crop->focus_code,
            $crop->numeric_code,
            $crop->vertical_id,
            $crop->crop_flag,
            $crop->effective_date ? \Carbon\Carbon::parse($crop->effective_date)->format('d-m-Y') : null,

            // Classification
            $crop->category?->name,
            $crop->cropCategory?->name,
            $crop->cropType?->name,

            // Taxonomy
            $crop->scientific_name,
            $crop->common_name,
            $crop->family_name,
            $crop->genus,
            $crop->species,

            // Season & Growth
            $crop->season?->name,
            $months[$crop->season_start_month_id] ?? $crop->season_start_month_id,
            $months[$crop->season_end_month_id]   ?? $crop->season_end_month_id,
            $crop->duration_days,
            $crop->sowing_time,
            $crop->harvest_time,
            $crop->climate_requirement,

            // Soil & Agronomy
            $crop->soilType?->name,
            $crop->isolation_distance,
            $crop->expected_yield,
            $crop->regeneration_cut_year,

            // Seed Info
            $crop->seed_quantity,
            $crop->seed_weight,
            $crop->unit?->name,
            $crop->pouchStandard?->name,

            // Description
            $crop->description,

            // Status
            $crop->is_active == 1 ? 'Active' : 'Inactive',
            $crop->update_status == 1 ? 'Activated' : 'Deactivated',
            $crop->updated_at?->format('d-m-Y'),
            $crop->created_at?->format('d-m-Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2D6A4F'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
