<?php

namespace App\Imports;

use App\Models\Bin;
use App\Models\Rack;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class BinImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row)
    {
        if (empty(trim($row['name'] ?? ''))) {
            return null;
        }

        // Skip if name already exists
        if (Bin::where('name', trim($row['name']))->exists()) {
            return null;
        }

        $rackId = Rack::where('name', 'like', trim($row['rack'] ?? ''))->value('id');

        return new Bin([
            'name'        => trim($row['name']),
            'code'        => $row['code'] ?? null,
            'rack_id'     => $rackId,
            'description' => $row['description'] ?? null,
            'status'      => $this->parseStatus($row['status'] ?? 'active'),
        ]);
    }

    private function parseStatus($value): int
    {
        $v = strtolower(trim((string) $value));
        return in_array($v, ['1', 'active', 'yes']) ? 1 : 0;
    }
}
