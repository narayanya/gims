<?php

namespace App\Imports;

use App\Models\Container;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ContainerImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row)
    {
        if (empty(trim($row['name'] ?? ''))) {
            return null;
        }

        // Skip if name already exists
        if (Container::where('name', trim($row['name']))->exists()) {
            return null;
        }

        $unitId = Unit::where('name', 'like', trim($row['unit'] ?? ''))->value('id');

        return new Container([
            'name'           => trim($row['name']),
            'code'           => $row['code'] ?? null,
            'container_type' => $row['container_type'] ?? null,
            'capacity'       => is_numeric($row['capacity_no_of_pouches'] ?? null) ? $row['capacity_no_of_pouches'] : null,
            'unit_id'        => $unitId,
            'length'         => is_numeric($row['length'] ?? null) ? $row['length'] : null,
            'width'          => is_numeric($row['width'] ?? null) ? $row['width'] : null,
            'height'         => is_numeric($row['height'] ?? null) ? $row['height'] : null,
            'dimension_unit' => $row['dimension_unit'] ?? null,
            'description'    => $row['description'] ?? null,
            'status'         => $this->parseStatus($row['status'] ?? 'active'),
        ]);
    }

    private function parseStatus($value): int
    {
        $v = strtolower(trim((string) $value));
        return in_array($v, ['1', 'active', 'yes']) ? 1 : 0;
    }
}
