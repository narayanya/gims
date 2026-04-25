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
        'fromWarehouse',
        'toWarehouse',
        'user'
    ]);

    // ✅ DATE FILTER
    if ($request->date_from) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->date_to) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Deduplicate: show only one row per batch (the first transfer in each batch)
    $allTransfers = $query->latest()->get();

    $seenBatches = [];
    $grouped = $allTransfers->filter(function ($t) use (&$seenBatches) {
        if ($t->batch_id && in_array($t->batch_id, $seenBatches)) {
            return false;
        }
        if ($t->batch_id) {
            $seenBatches[] = $t->batch_id;
        }
        return true;
    })->values();

    // Manual pagination
    $page     = $request->get('page', 1);
    $perPage  = 10;
    $wTransfers = new \Illuminate\Pagination\LengthAwarePaginator(
        $grouped->forPage($page, $perPage),
        $grouped->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );
    
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
            'lot_ids'          => 'required|array',
            'lot_ids.*'        => 'exists:lots,id',
            'to_storage'       => 'required|exists:storages,id',
            'from_warehouse_id'=> 'required|exists:warehouses,id',
            'to_warehouse_id'  => 'required|exists:warehouses,id',
        ]);

        if ($request->from_storage == $request->to_storage) {
            return back()->withErrors('Source and destination storage cannot be the same.');
        }

        $lotIds      = $request->lot_ids;
        $toStorageId = $request->to_storage;

        // One batch_id for all lots in this transfer
        $batchId = 'WT-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

        foreach ($lotIds as $lotId) {
            $lot = Lot::with('seedQuantities')->find($lotId);
            if (!$lot) continue;

            $lotQty = (float) $lot->seedQuantities->sum('quantity');

            WarehouseTransfer::create([
                'batch_id'          => $batchId,
                'lot_id'            => $lotId,
                'crop_id'           => $lot->crop_id,
                'accession_id'      => $lot->accession_id,
                'quantity'          => $lotQty,
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'from_storage_id'   => $request->from_storage,
                'to_storage_id'     => $toStorageId,
                'remarks'           => $request->remarks,
                'transferred_by'    => auth()->id(),
                'status'            => '0',
            ]);

            $lot->update(['storage_id' => $toStorageId]);
        }

        return back()->with('success', count($lotIds) . ' lot(s) transferred successfully! Batch: ' . $batchId);
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

        // Load all lots in the same batch
        $batchLots = WarehouseTransfer::with(['lot.crop', 'lot.accession'])
            ->where('batch_id', $t->batch_id)
            ->get();

        return view('lot-management.itn', compact('t', 'batchLots'));
    }

   public function processITN(Request $request)
    {
        $request->validate([
            'transfer_id'  => 'required|exists:warehouse_transfers,id',
            'receiver'     => 'required|string|max:255',
            'mobile_number'=> 'required|string|max:20',
            'itn_date'     => 'required|date',
        ]);

        $transfer = WarehouseTransfer::findOrFail($request->transfer_id);

        // Prevent duplicate ITN for this batch
        if (Itn::where('batch_id', $transfer->batch_id)->exists()) {
            return back()->with('error', 'ITN already generated for this batch.');
        }

        $itnNumber = $request->itn_number
            ?: 'ITN-' . date('Y') . '-' . str_pad($transfer->id, 5, '0', STR_PAD_LEFT);

        $photoPath = null;
        if ($request->hasFile('dispatchUpload')) {
            $photoPath = $request->file('dispatchUpload')->store('itn_photos', 'public');
        }

        // Total quantity across all lots in the batch
        $totalQty = WarehouseTransfer::where('batch_id', $transfer->batch_id)->sum('quantity');

        // One ITN for the whole batch (no single lot_id/crop_id/accession_id)
        Itn::create([
            'transfer_id'       => $transfer->id,
            'batch_id'          => $transfer->batch_id,
            'itn_number'        => $itnNumber,
            'itn_date'          => $request->itn_date,
            'from_warehouse_id' => $transfer->from_warehouse_id,
            'to_warehouse_id'   => $transfer->to_warehouse_id,
            'from_storage_id'   => $transfer->from_storage_id,
            'to_storage_id'     => $transfer->to_storage_id,
            'quantity'          => $totalQty,
            'receiver'          => $request->receiver,
            'mobile_number'     => $request->mobile_number,
            'email'             => $request->email,
            'instructions'      => $request->instructions,
            'photo'             => $photoPath,
            'created_by'        => auth()->id(),
        ]);

        // Mark all transfers in the batch as completed
        WarehouseTransfer::where('batch_id', $transfer->batch_id)
            ->update(['status' => '1']);

        return redirect()->route('dispatch.itn.show', $itn->id)
            ->with('success', 'ITN Generated — now confirm dispatch to generate MRN.');
    }

    public function printITN($id)
    {
        $itn = Itn::with([
            'fromWarehouse',
            'toWarehouse',
            'fromStorage',
            'toStorage',
        ])->findOrFail($id);

        // Load all lots in this batch
        $batchLots = WarehouseTransfer::with(['lot.crop', 'lot.accession'])
            ->where('batch_id', $itn->batch_id)
            ->get();

        return view('lot-management.print', compact('itn', 'batchLots'));
    }

}
