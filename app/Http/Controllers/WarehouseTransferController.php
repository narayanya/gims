<?php

namespace App\Http\Controllers;

use App\Models\Accession;
use App\Models\Lot;
use App\Models\Storage;
use App\Models\Crop;
use App\Models\WarehouseTransfer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseTransferController extends Controller
{
   public function index()
    {
        return view('lot-management.warehouse-inter-transfer', [
            'warehouses' => Warehouse::with(['country','state','district','city'])
                ->where('status', 1)
                ->orderBy('name')
                ->get()
        ]);
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
        ]);

        $warehouse = \App\Models\WarehouseTransfer::create([
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id'   => $request->to_warehouse_id,
            'transferred_by'    => auth()->id(),
        ]);

        return back()->with('success', 'Warehouse transferred successfully ✅');
    }

    public function getLotsByWarehouse(Request $request)
    {
        $lots = \App\Models\Lot::with(['crop','accession'])
            ->where('warehouse_id', $request->warehouse_id)
            ->get();

        return response()->json($lots);
    }


}
