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
use App\Models\Itn;
class WarehouseTransferController extends Controller
{
   public function index(Request $request)
    {
    $query = WarehouseTransfer::with([
        'lot',
        'lot.crop',
        'lot.accession',
        'fromStorage',
        'toStorage',
        'user'
    ]);

    // ✅ DATE FILTER
    if ($request->date_from) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->date_to) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $wTransfers = $query->latest()->paginate(10);
    
    return view('lot-management.warehouse-inter-transfer', [
            'warehouses' => Warehouse::with(['country','state','district','city'])
                ->where('status', 1)
                ->orderBy('name')
                ->get(),
            'storages'   => Storage::where('status', 1)->orderBy('name')->get(['id','storage_id','name']),
            'wTransfers'  => $wTransfers,
        ]);
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'lot_ids' => 'required|array',
            'to_storage' => 'required|exists:storages,id',
        ]);

        if ($request->from_storage == $request->to_storage) {
            return back()->withErrors('Source and destination storage cannot be same');
        }

        $lot = Lot::with('seedQuantities')->findOrFail($request->from_lot_id);
        $lotIds = $request->lot_ids;
        $toStorageId = $request->to_storage;
        $lotQty = (float) $lot->seedQuantities->sum('quantity');

        foreach ($lotIds as $lotId) {

            $lot = \App\Models\Lot::find($lotId);

            if ($lot) {

                // ✅ Save transfer history FIRST
                WarehouseTransfer::create([
                    'lot_id' => $lotId,
                    'crop_id' => $lot->crop_id,
                    'accession_id'        => $lot->accession_id,
                    'quantity' =>$lotQty,
                    'from_warehouse_id' => $request->from_warehouse_id,
                    'to_warehouse_id'   => $request->to_warehouse_id,
                    'from_storage_id' => $request->from_storage,
                    'to_storage_id' => $toStorageId,
                    'remarks'              => $request->remarks,
                    'transferred_by'       => auth()->id(),
                ]);

                // ✅ Then update lot
                $lot->update([
                    'storage_id' => $toStorageId
                ]);
            }
        }

        return back()->with('success', 'warehouse/storage transferred successfully!');
    }

   public function getLotsByWarehouse(Request $request)
    {
        $query = Lot::with(['crop', 'accession', 'storage']);

        // If storage_id is provided, filter by it directly.
        // This is the most accurate way since warehouse_id was removed from lots table.
        if ($request->filled('storage_id')) {
            $query->where('storage_id', $request->storage_id);
        } 
        // If ONLY warehouse_id is provided, we look through the storage relationship
        elseif ($request->filled('warehouse_id')) {
            $query->whereHas('storage', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
        } else {
            return response()->json([]); // Return nothing if no IDs are sent
        }

        
        $lots = $query->get();
        
        return response()->json($lots);
        
    }
    public function getStoragesByWarehouse(Request $request)
    {
        return Storage::where('warehouse_id', $request->warehouse_id)->get();
    }

    public function getWarehouseByStorage(Request $request)
    {
        $storage = Storage::find($request->storage_id);
        return response()->json([
            'warehouse_id' => $storage ? $storage->warehouse_id : null
        ]);
    }

    public function export(Request $request)
    {
        $query = \App\Models\WarehouseTransfer::with(['fromStorage', 'toStorage', 'lot']);

        // same filter apply
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $data = $query->get();

        $filename = "warehouse_transfers_" . now()->format('Ymd') . ".csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // header
            fputcsv($file, [
                'Lot No',
                'From Warehouse',
                'To Warehouse',
                'From Storage',
                'To Storage',
                'Date'
            ]);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->lot->lot_number ?? '',
                    $row->fromWarehouse->name ?? '',
                    $row->toWarehouse->name ?? '',
                    $row->fromStorage->name ?? '',
                    $row->toStorage->name ?? '',
                    $row->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function itn($id)
    {
        $t = WarehouseTransfer::with([
            'lot.crop',
            'lot.accession',
            'fromWarehouse',
            'toWarehouse',
            'fromStorage',
            'toStorage',
            'user'
        ])->findOrFail($id);

        return view('lot-management.itn', compact('t'));
    }

   public function processITN(Request $request)
    {
        $request->validate([
            'transfer_id' => 'required|exists:warehouse_transfers,id',
            'receiver' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'itn_date' => 'required|date',
        ]);

        $transfer = WarehouseTransfer::findOrFail($request->transfer_id);

        // prevent duplicate
        if ($transfer->status === 'completed') {
            return back()->with('error', 'ITN already generated.');
        }

        // auto ITN number
        $itnNumber = $request->itn_number;
        if (!$itnNumber) {
            $itnNumber = 'ITN-' . date('Y') . '-' . str_pad($transfer->id, 5, '0', STR_PAD_LEFT);
        }

        // upload photo
        $photoPath = null;
        if ($request->hasFile('dispatchUpload')) {
            $photoPath = $request->file('dispatchUpload')->store('itn_photos', 'public');
        }

        // ✅ save into ITN table
        Itn::create([
            'transfer_id'        => $transfer->id,
            'itn_number'         => $itnNumber,
            'itn_date'           => $request->itn_date,

            'lot_id'             => $transfer->lot_id,
            'crop_id'            => $transfer->lot->crop->id ?? null,
            'accession_id'       => $transfer->lot->accession->id ?? null,

            'from_warehouse_id'  => $transfer->from_warehouse_id,
            'to_warehouse_id'    => $transfer->to_warehouse_id,
            'from_storage_id'    => $transfer->from_storage_id,
            'to_storage_id'      => $transfer->to_storage_id,

            'quantity'           => $transfer->quantity,

            'receiver'           => $request->receiver,
            'mobile_number'      => $request->mobile_number,
            'email'              => $request->email,
            'instructions'       => $request->instructions,
            'photo'              => $photoPath,

            'created_by'         => auth()->id(),
        ]);

        // ✅ update transfer table
        $transfer->update([
            'status' => '1'
        ]);

        return redirect()->route('warehouse-transfer.index')
            ->with('success', 'ITN Generated Successfully');
    }

    public function printITN($id)
    {
        $itn = Itn::with([
            'lot.crop',
            'lot.accession',
            'fromWarehouse',
            'toWarehouse'
        ])->findOrFail($id);

        return view('lot-management.print', compact('itn'));
    }

}
