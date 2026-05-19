<?php

namespace App\Exports;

use App\Models\Container;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StorageLocationContainerSheet implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Containers';
    }

    public function query()
    {
        return Container::with('unit')->orderBy('name');
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Code',
            'Container Type',
            'Capacity (No. of Pouches)',
            'Unit',
            'Length',
            'Width',
            'Height',
            'Dimension Unit',
            'Description',
            'Status',
            'Created At',
        ];
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $row->name,
            $row->code,
            $row->container_type,
            $row->capacity,
            $row->unit?->name,
            $row->length,
            $row->width,
            $row->height,
            $row->dimension_unit,
            $row->description,
            $row->status ? 'Active' : 'Inactive',
            $row->created_at?->format('d-m-Y H:i'),
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
