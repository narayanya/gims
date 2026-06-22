<?php

namespace App\Exports;

use App\Models\Accession;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AccessionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Accessions';
    }

    public function query()
    {
        return Accession::with([
            'crop',
            'country',
            'state',
            'district',
            'city',
            'warehouse',
            'storageLocation',
            'storageType',
            'storageTime',
            'storageCondition',
            'capacityUnit',
        ])->orderBy('id');
    }

    public function headings(): array
    {
        return [
            // Basic
            '#',
            'Accession Number',
            'Sample ID',
            'Year of Arrival',
            'Source Type- Internal',
            'External Source',
            'Requester Show',
            'Status',

            // Crop
            'Crop Name',
            'Crop Code',
            'Scientific Name',

            // Collection
            'Collection Number',
            'Collection Date',
            'Collector Name',
            'Donor Name',
            'Collection Site',
            'Country',
            'State',
            'District',
            'City / Village',
            'Latitude',
            'Longitude',
            'Pincode',

            // Biological
            'Biological Status',
            'Sample Type',
            'Reproductive Type',

            // Storage
            'Storage Time',

            // Documentation
            'Barcode Type',
            'Barcode',
            'Notes',

            // Dates
            'Entry Date',
            'Created At',
        ];
    }

    public function map($accession): array
    {
        static $index = 0;
        $index++;

        return [
            // Basic
            $index,
            $accession->accession_number,
            $accession->sample_id,
            $accession->year_of_arrival,
            $accession->acc_source,
            $accession->ext_source,
            $accession->requester_show,
            $accession->status == 1 ? 'Active' : 'Inactive',

            // Crop
            $accession->crop?->crop_name,
            $accession->crop?->crop_code,
            $accession->crop?->scientific_name,

            // Collection
            $accession->collection_number,
            $accession->collection_date?->format('d-m-Y'),
            $accession->collector_name,
            $accession->donor_name,
            $accession->collection_site,
            $accession->country?->country_name,
            $accession->state?->state_name,
            $accession->district?->district_name,
            $accession->city?->city_village_name,
            $accession->latitude,
            $accession->longitude,
            $accession->pincode,

            // Biological
            $accession->biological_status,
            $accession->sample_type,
            $accession->reproductive_type,

            // Storage
            
            $accession->storageTime?->name,

            // Documentation
            $accession->barcode_type,
            $accession->barcode,
            $accession->notes,

            // Dates
            $accession->entry_date?->format('d-m-Y'),
            $accession->created_at?->format('d-m-Y H:i'),
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
