<?php

namespace App\Http\Controllers;

use App\Models\Accession;
use App\Models\Lot;
use App\Models\Storage;
use App\Models\Crop;
use App\Models\SeedQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LotTransferController extends Controller
{
   public function index(Request $request)
{
     $query = \App\Models\LotTransfer::with([
        'lot',
        'lot.crop',
        'lot.accession',
        'fromStorage',
        'toStorage',
        'toSection',
        'toRack',
        'toBin',
        'toContainer',
        'user'
    ]);

    // ✅ DATE FILTER
    if ($request->date_from) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->date_to) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $transfers = $query->latest()->paginate(10);
 
    return view('lot-management.inter-transfer', [
        'crops' => Crop::where('update_status', 1)->orderBy('crop_name')->get(['id','crop_code','crop_name']),
        'accessions' => Accession::where('status', 1)->orderBy('accession_number')->get(['id','crop_id','accession_number']),
        'storages'   => Storage::where('status', 1)->orderBy('name')->get(['id','storage_id','name']),
        'sections'   => \App\Models\Section::where('status',1)->orderBy('name')->get(),
        'racks'      => \App\Models\Rack::where('status',1)->orderBy('name')->get(),
        'bins'       => \App\Models\Bin::where('status',1)->orderBy('name')->get(),
        'containers' => \App\Models\Container::where('status',1)->orderBy('name')->get(),
        'lot'        => null,

        // ✅ ADD THIS
        'transfers'  => $transfers,
    ]);
}


public function transfer(Request $request)
    {
        $request->validate([
            'from_lot_id'   => 'required|exists:lots,id',
            'to_storage_id' => 'required|exists:storages,id',
            'section_id'    => 'nullable|exists:sections,id',
            'rack_id'       => 'nullable|exists:racks,id',
            'bin_id'        => 'nullable|exists:bins,id',
            'container_id'  => 'nullable|exists:containers,id',
            'remarks'       => 'nullable|string|max:255',
        ]);

        $lot = Lot::with('seedQuantities')->findOrFail($request->from_lot_id);
        $fromStorage = Storage::findOrFail($lot->storage_id);
        $toStorage   = Storage::findOrFail($request->to_storage_id);

        // ❌ Prevent same storage
        if ($fromStorage->id == $toStorage->id) {
            return back()->withInput()->withErrors([
                'error' => 'Source and destination storage cannot be the same.'
            ]);
        }

        // ✅ Lot Quantity
        $lotQty = (float) $lot->seedQuantities->sum('quantity');

        // ✅ REAL USAGE (same as UI)
        $fromUsed = \App\Models\SeedQuantity::whereHas('lot', function ($q) use ($fromStorage) {
            $q->where('storage_id', $fromStorage->id);
        })->sum('quantity');

        $toUsed = \App\Models\SeedQuantity::whereHas('lot', function ($q) use ($toStorage) {
            $q->where('storage_id', $toStorage->id);
        })->sum('quantity');

        // ✅ BEFORE TRANSFER (MATCHES UI 🔥)
        $favailableCapacity = $fromStorage->capacity - $fromUsed;
        $availableCapacity  = $toStorage->capacity - $toUsed;

        // ❌ Capacity check (TO)
        if ($lotQty > $availableCapacity) {
            return back()->withInput()->withErrors([
                'error' => "Not enough space in destination. Available: {$availableCapacity}"
            ]);
        }

        // ❌ Safety check (FROM)
        if ($lotQty > $fromUsed) {
            return back()->withInput()->withErrors([
                'error' => "Invalid quantity in source storage."
            ]);
        }

        // ✅ AFTER TRANSFER
        $fbalanceCapacity = $favailableCapacity + $lotQty; // FROM → space increases
        $balanceCapacity  = $availableCapacity - $lotQty;  // TO → space decreases

        DB::beginTransaction();

        try {

            // ✅ LOG TRANSFER (before updating lot)
            \App\Models\LotTransfer::create([
                'lot_id'              => $lot->id,
                'crop_id'             => $lot->crop_id,
                'accession_id'        => $lot->accession_id,

                'from_storage_id'     => $fromStorage->id,
                'to_storage_id'       => $toStorage->id,

                'from_section_id'     => $lot->section_id,
                'to_section_id'       => $request->section_id,

                'from_rack_id'        => $lot->rack_id,
                'to_rack_id'          => $request->rack_id,

                'from_bin_id'         => $lot->bin_id,
                'to_bin_id'           => $request->bin_id,

                'from_container_id'   => $lot->container_id,
                'to_container_id'     => $request->container_id,

                // ✅ FROM
                'f_available_capacity' => $favailableCapacity,
                'f_quantity'           => $lotQty,
                'f_balance_capacity'   => $fbalanceCapacity,

                // ✅ TO
                'available_capacity'   => $availableCapacity,
                'quantity'             => $lotQty,
                'balance_capacity'     => $balanceCapacity,

                'remarks'              => $request->remarks,
                'transferred_by'       => auth()->id(),
            ]);

            // 2. Update storage usage ✅
            $fromStorage->decrement('current_usage', $lotQty);
            $toStorage->increment('current_usage', $lotQty);

            // ✅ MOVE LOT (this updates storage logically)
            $lot->update([
                'storage_id'   => $toStorage->id,
                'section_id'   => $request->section_id,
                'rack_id'      => $request->rack_id,
                'bin_id'       => $request->bin_id,
                'container_id' => $request->container_id,
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Transfer failed: ' . $e->getMessage()
            ]);
        }

        return redirect()->route('lot-transfer.index')
            ->with('success', "Lot {$lot->lot_number} transferred successfully.");
    }

    // AJAX: get lots for a storage
   public function getStorageLots($storageId)
    {
        $storage = Storage::with([
            'lots.accession.crop',
            'lots.seedQuantities.unit',
            'lots.section',
            'lots.rack',
            'lots.bin',
            'lots.container',
            'warehouse',
            'type',
            'condition',
            'time',
        ])->findOrFail($storageId);

        // ✅ Correct usage calculation (IMPORTANT)
        $used = \App\Models\SeedQuantity::whereHas('lot', function ($q) use ($storageId) {
            $q->where('storage_id', $storageId);
        })->sum('quantity');

        $available = (float)$storage->capacity - $used;

        return response()->json([
            'storage' => [
                'name' => $storage->name,
                'warehouse' => $storage->warehouse->name ?? null,
                'storage_type' => $storage->type->name ?? null,
                'storage_condition' => $storage->condition->name ?? null,
                'storage_time' => $storage->time->name ?? null,
                'temperature' => $storage->temperature,
                'humidity' => $storage->humidity,
                'capacity' => $storage->capacity,
                'unit' => $storage->unit,
            ],
            'available' => $available,
            'lots' => $storage->lots
        ]);
    }

    // AJAX: get storage hierarchy (sections/racks/bins/containers)
    public function getStorageHierarchy($storageId)
    {
        return response()->json([
            'sections'   => \App\Models\Section::where('status',1)->orderBy('name')->get(['id','name']),
            'racks'      => \App\Models\Rack::where('status',1)->orderBy('name')->get(['id','name','section_id']),
            'bins'       => \App\Models\Bin::where('status',1)->orderBy('name')->get(['id','name','rack_id']),
            'containers' => \App\Models\Container::where('status',1)->orderBy('name')->get(['id','name']),
        ]);
    }
    public function getAccessions($cropId)
    {
        $accessions = \App\Models\Accession::where('crop_id', $cropId)
            ->where('status', 1)
            ->orderBy('accession_number')
            ->get(['id', 'accession_number']);

        return response()->json($accessions);
    }
    public function getAccessionStorages($accessionId)
    {
        $storages = \App\Models\Storage::whereHas('lots', function ($q) use ($accessionId) {
            $q->where('accession_id', $accessionId);
        })
        ->where('status', 1)
        ->orderBy('name')
        ->get(['id', 'name']);

        return response()->json($storages);
    }

    public function export(Request $request)
    {
        $query = \App\Models\LotTransfer::with(['fromStorage', 'toStorage', 'lot']);

        // same filter apply
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $data = $query->get();

        $filename = "lot_transfers_" . now()->format('Ymd') . ".csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // header
            fputcsv($file, [
                'Lot No',
                'From Storage',
                'To Storage',
                'Quantity',
                'Date'
            ]);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->lot->lot_number ?? '',
                    $row->fromStorage->name ?? '',
                    $row->toStorage->name ?? '',
                    $row->quantity,
                    $row->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
