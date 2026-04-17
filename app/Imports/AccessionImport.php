<?php

namespace App\Imports;

use App\Models\Accession;
use App\Models\Crop;
use App\Models\Variety;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AccessionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $crop = Crop::where('name', $row['crop'])->first();
        $variety = Variety::where('name', $row['variety'])->first();

        return new Accession([
            'accession_number' => $row['accession_number'],
            'crop_id' => $crop ? $crop->id : null,
            'variety_id' => $variety ? $variety->id : null,
            'source' => $row['source'],
            'origin_country' => $row['origin_country'],
            'collection_date' => $row['collection_date'],
            'remarks' => $row['remarks'],
        ]);
    }
}