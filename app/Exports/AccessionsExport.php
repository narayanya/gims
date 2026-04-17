<?php

namespace App\Exports;

use App\Models\Accession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccessionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Accession::with(['crop','variety'])->get()->map(function ($a) {
            return [
                'accession_number' => $a->accession_number,
                'crop' => $a->crop->crop_name ?? '',
                'variety' => $a->variety->name ?? '',
                'source' => $a->source,
                'origin_country' => $a->origin_country,
                'collection_date' => $a->collection_date,
                'remarks' => $a->remarks,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Accession Number',
            'Crop',
            'Variety',
            'Source',
            'Origin Country',
            'Collection Date',
            'Remarks',
        ];
    }
}