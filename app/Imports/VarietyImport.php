<?php

namespace App\Imports;

use App\Models\Variety;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VarietyImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Variety([
            'crop_id' => $row['crop_id'],
            'name' => $row['name'],
            'code' => $row['code'],
            'description' => $row['description'],
        ]);
    }
}