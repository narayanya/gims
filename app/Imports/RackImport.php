<?php

namespace App\Imports;

use App\Models\Rack;
use App\Models\Storage;
use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class RackImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row)
    {
        if (empty(trim($row['name'] ?? ''))) {
            return null;
        }

        // Skip if name already exists
        if (Rack::where('name', trim($row['name']))->exists()) {
            return null;
        }

        $storageId   = Storage::where('name', 'like', trim($row['storage'] ?? ''))->value('id');
        $warehouseId = Warehouse::where('name', 'like', trim($row['warehouse'] ?? ''))->value('id');

        // Derive warehouse from storage if not directly provided
        if (!$warehouseId && $storageId) {
            $warehouseId = Storage::find($storageId)?->warehouse_id;
        }

        return new Rack([
            'name'         => trim($row['name']),
            'code'         => $row['code'] ?? null,
            'storage_id'   => $storageId,
            'warehouse_id' => $warehouseId,
            'description'  => $row['description'] ?? null,
            'status'       => $this->parseStatus($row['status'] ?? 'active'),
        ]);
    }

    private function parseStatus($value): int
    {
        $v = strtolower(trim((string) $value));
        return in_array($v, ['1', 'active', 'yes']) ? 1 : 0;
    }
}
