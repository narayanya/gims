<?php

namespace App\Imports;

use App\Models\Crop;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CropImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Crop([
            'name' => $row['name'],
            'code' => $row['code'],
            'description' => $row['description'],
        ]);
    }
}