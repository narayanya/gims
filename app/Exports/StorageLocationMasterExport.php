<?php

namespace App\Exports;

use App\Models\Rack;
use App\Models\Bin;
use App\Models\Container;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StorageLocationMasterExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new StorageLocationRackSheet(),
            new StorageLocationBinSheet(),
            new StorageLocationContainerSheet(),
        ];
    }
}
