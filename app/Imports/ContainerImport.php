<?php

namespace App\Imports;

use App\Models\Container;
use App\Models\Rack;
use App\Models\Bin;
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
        $rackId = null;
        $binId  = null;

        // Resolve rack by name (case-insensitive)
        if (!empty(trim($row['rack'] ?? ''))) {
            $rackId = Rack::where('name', 'like', trim($row['rack']))->value('id');
        }

        // Resolve bin by name; if a rack was resolved, scope to that rack
        if (!empty(trim($row['bin'] ?? ''))) {
            $binQuery = Bin::where('name', 'like', trim($row['bin']));
            if ($rackId) {
                $binQuery->where('rack_id', $rackId);
            }
            $binId = $binQuery->value('id');

            // If bin resolved but no rack given, derive rack from bin
            if ($binId && !$rackId) {
                $rackId = Bin::where('id', $binId)->value('rack_id');
            }
        }

        return new Container([
            'name'           => trim($row['name']),
            'code'           => $row['code'] ?? null,
            'container_type' => $row['container_type'] ?? null,
            'capacity'       => is_numeric($row['capacity_no_of_pouches'] ?? null) ? $row['capacity_no_of_pouches'] : null,
            'unit_id'        => $unitId,
            'rack_id'        => $rackId,
            'bin_id'         => $binId,
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
