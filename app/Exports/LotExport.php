<?php

namespace App\Exports;

use App\Models\Lot;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LotExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Lots';
    }

    public function query()
    {
        return Lot::with([
            'accession.crop',
            'accession.country',
            'accession.state',
            'accession.district',
            'crop',
            'storage',
            'section',
            'rack',
            'bin',
            'container',
            'unit',
            'seedQuantities.unit',
            'seedQuality',
        ])->whereNotNull('lot_number')
          ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            // Lot Info
            '#',
            'Lot Number',
            'Arrival Type',
            'Reference Number',
            'Rejuvenation Program',
            'Prefix',
            'Sample ID',
            'Batch Number',
            'Status',

            // Accession
            'Accession Number',
            'Accession Name',

            // Crop
            'Crop Name',
            'Crop Code',
            'Scientific Name',

            // Storage
            'Storage Name',
            'Section',
            'Rack',
            'Bin',
            'Container',

            // Seed Quantity (aggregated)
            'Total Quantity',
            'Quantity (Display)',
            'Number of Seeds',
            'Number of Bags',
            'Per Seed Weight (g)',
            'Min Quantity',
            'Unit',

            // Seed Quality (latest)
            'Germination (%)',
            'Moisture Content (%)',
            'Purity (%)',
            'Chlorophyll (%)',
            'Water Level (%)',
            'Seed Health Status',
            'Viability Test Date',
            'Research Date',

            // Collection (from accession)
            'Collection Site',
            'Country',
            'State',
            'District',

            // Dates
            'Expiry Date',
            'Created At',
        ];
    }

    public function map($lot): array
    {
        static $index = 0;
        $index++;

        // Aggregate seed quantities
        $quantities   = $lot->seedQuantities;
        $totalQty     = $quantities->sum('quantity');
        $totalQtyShow = $quantities->sum('quantity_show');
        $totalSeeds   = $quantities->sum('number_of_seeds');
        $totalBags    = $quantities->sum('number_of_bags');
        $perSeedWt    = $quantities->first()?->per_seed_weight;
        $minQty       = $quantities->min('min_quantity');
        $unitName     = $quantities->first()?->unit?->name ?? $lot->unit?->name;

        // Latest seed quality
        $quality = $lot->seedQuality;

        return [
            // Lot Info
            $index,
            $lot->lot_number,
            $lot->arrival_type,
            $lot->reference_number,
            $lot->rejuvenation_program,
            $lot->prefix,
            $lot->sample_id,
            $lot->batch_number,
            $lot->status,

            // Accession
            $lot->accession?->accession_number,
            $lot->accession?->accession_name,

            // Crop
            $lot->crop?->crop_name ?? $lot->accession?->crop?->crop_name,
            $lot->crop?->crop_code ?? $lot->accession?->crop?->crop_code,
            $lot->crop?->scientific_name ?? $lot->accession?->crop?->scientific_name,

            // Storage
            $lot->storage?->name,
            $lot->section?->name,
            $lot->rack?->name,
            $lot->bin?->name,
            $lot->container?->name,

            // Seed Quantity
            $totalQty,
            $totalQtyShow,
            $totalSeeds,
            $totalBags,
            $perSeedWt,
            $minQty,
            $unitName,

            // Seed Quality
            $quality?->germination_percentage,
            $quality?->moisture_content,
            $quality?->purity_percentage,
            $quality?->chlorophyll_percentage,
            $quality?->water_level_percentage,
            $quality?->seed_health_status,
            $quality?->viability_test_date,
            $quality?->research_date,

            // Collection (from accession)
            $lot->accession?->collection_site,
            $lot->accession?->country?->country_name,
            $lot->accession?->state?->state_name,
            $lot->accession?->district?->district_name,

            // Dates
            $lot->expiry_date ? \Carbon\Carbon::parse($lot->expiry_date)->format('d-m-Y') : null,
            $lot->created_at?->format('d-m-Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row — green background, white bold text, centered
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
