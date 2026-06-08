<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StorageType;
use App\Models\Unit;
use App\Models\StorageTime;
use App\Models\StorageCondition;
use App\Models\StorageLocation;
use App\Models\Warehouse;


class StorageController extends Controller
{
    /**
     * Reusable form data
     */
    private function formData()
    {
        return [
            'users'            => User::all(),
            'storageTypes'     => StorageType::orderBy('name')->get(),
            'units'            => Unit::where('status', 1)->orderBy('name')->get(),
            'storageTime'      => StorageTime::orderBy('name')->get(),
            'storageCondition' => StorageCondition::orderBy('name')->get(),
            'storageWarehouse' => Warehouse::with(['state','district','city'])->orderBy('name')->get(),
        ];
    }

    /**
     * Display a listing of storage locations.
     */
    public function index()
    {
        $storages = Storage::with(['manager', 'storageType', 'unit', 'storageTime', 'storageCondition', 'storageWarehouse', 'lots'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('storage-management.index', compact('storages'));
    }

    /**
     * Show the form for creating a new storage location.
     */
    public function create()
    {
        return view('storage-management.create', $this->formData());
    }

    /**
     * Store a newly created storage location.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'storage_time_id' => 'required|exists:storage_times,id',
            'storage_condition_id' => 'required|exists:storage_conditions,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|exists:storage_types,id',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'unit' => 'required|exists:units,id',
            'temperature' => 'nullable|string|max:50',
            'humidity' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|string|in:active,inactive,maintenance',
            'image' => 'required|image|max:2048',
        ]);

        $capacity = $request->capacity;
        $unitId   = $request->unit;

        // Get unit name (assuming relation)
        $unit = \App\Models\Unit::find($unitId);

        $capacityInGrams = null;

        if ($capacity && $unit) {
            if (strtolower($unit->code) == 'kg') {
                $capacityInGrams = $capacity * 1000;
            } elseif (strtolower($unit->code) == 'g') {
                $capacityInGrams = $capacity;
            }
        }

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('storages', 'public')
            : null;
        
        $storageTime = \App\Models\StorageTime::find($request->storage_time_id);
        $gramUnitId = \App\Models\Unit::where('code', 'g')->value('id');

            if (!$storageTime) {
                return back()->with('error', 'Invalid storage time selected.');
            }

            $storageTimeCode = $storageTime->code; // make sure column exists
            

        try {
            DB::beginTransaction();

            Storage::create([
                'storage_id' => Storage::generateStorageId($storageTimeCode),
                'name' => $request->name,
                'storage_type_id' => $request->type,
                
                'capacity' => $capacityInGrams,
                'unit_id'  => $gramUnitId, 
                'storage_time_id' => $request->storage_time_id, 
                'storage_condition_id' => $request->storage_condition_id,
                'warehouse_id' => $request->warehouse_id,
                'current_usage' => 0,
                'temperature' => $request->temperature,
                'humidity' => $request->humidity,
                'description' => $request->description,
                'image' => $imagePath,
                'status' => $request->status,
            ]);

            DB::commit();

            return redirect()->route('storage-management.index')
                ->with('success', 'Storage location created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Failed to create storage location.');
        }
    }

    /**
     * Display the specified storage location.
     */
    public function show(Storage $storage)
    {
        $storage->load(['manager', 'storageType', 'unit','storageTime', 'storageCondition', 'warehouse.state',
        'warehouse.district',
        'warehouse.city']);

        return view('storage-management.show', compact('storage'));
    }

    /**
     * Show the form for editing the specified storage location.
     */
    public function edit(Storage $storage)
    {
        return view('storage-management.edit',
            array_merge(['storage' => $storage], $this->formData())
        );
    }

    /**
     * Update the specified storage location.
     */
    public function update(Request $request, Storage $storage)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'storage_time_id' => 'required|exists:storage_times,id',
            'storage_condition_id' => 'required|exists:storage_conditions,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|exists:storage_types,id',
            'capacity' => 'nullable|numeric|min:0',
            'unit' => 'required|exists:units,id',
            'temperature' => 'nullable|string|max:50',
            'humidity' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|string|in:active,inactive,maintenance',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // ✅ GET UNIT
            $unit = \App\Models\Unit::find($request->unit);
            $gramUnitId = \App\Models\Unit::where('code', 'g')->value('id');

            // ✅ CONVERT TO GRAMS
            $capacityInGrams = null;

            if ($request->capacity && $unit) {
                if (strtolower($unit->code) == 'kg') {
                    $capacityInGrams = $request->capacity * 1000;
                } elseif (strtolower($unit->code) == 'g') {
                    $capacityInGrams = $request->capacity;
                }
            }

            $updateData = [
                'name'                 => $request->name,
                'storage_type_id'      => $request->type,
                'capacity'             => $capacityInGrams,
                'unit_id'  => $gramUnitId, 
                'storage_time_id'      => $request->storage_time_id,
                'storage_condition_id' => $request->storage_condition_id,
                'warehouse_id'         => $request->warehouse_id,
                'temperature'          => $request->temperature,
                'humidity'             => $request->humidity,
                'description'          => $request->description,
                'status'               => $request->status,
            ];

            if ($request->hasFile('image')) {
                $updateData['image'] = $request->file('image')->store('storages', 'public');
            }

            $storage->update($updateData);

            DB::commit();

            return redirect()->route('storage-management.index')
                ->with('success', 'Storage location updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update storage location: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified storage location.
     */
    public function destroy(Storage $storage)
    {
        try {
            $storage->delete();

            return redirect()->route('storage-management.index')
                ->with('success', 'Storage location deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete storage location.');
        }
    }

    public function storageReport()
    {
         $storages = Storage::with([
        'storageWarehouse',
        'storageTime',
        'lots.rack',
        'lots.bin',
        'lots.container',
        'lots.seedQuantities'
    ])->get();

        foreach ($storages as $storage) {
            $storage->filled_quantity = $storage->lots->sum(function ($lot) {
                return optional($lot->seedQuantity)->quantity ?? 0;
            });
        }

        return view('report.storage_report', compact('storages'));
    }
}