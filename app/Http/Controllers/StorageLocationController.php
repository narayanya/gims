<?php

namespace App\Http\Controllers;

use App\Models\StorageLocation;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StorageLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = StorageLocation::with('warehouse')->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        return view('master.storage-location.index', compact('locations', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:storage_locations,name',
            'code' => 'nullable|string|max:50|unique:storage_locations,code',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description' => 'nullable|string|max:1000',
        ]);

        StorageLocation::create($request->only('name', 'code', 'warehouse_id', 'description'));
        return redirect()->route('storage-locations.index')->with('success', 'Storage Location added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $location = StorageLocation::findOrFail($id);
        return response()->json($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $location = StorageLocation::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:storage_locations,name,' . $location->id,
            'code' => 'nullable|string|max:50|unique:storage_locations,code,' . $location->id,
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $location->update($request->only('name', 'code', 'warehouse_id', 'description'));
        return redirect()->route('storage-locations.index')->with('success', 'Storage Location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $location = StorageLocation::findOrFail($id);
        $location->status = 0; // inactive
        $location->save();
        // $location->delete();

        return redirect()->route('storage-locations.index')->with('success', 'Storage Location deleted successfully.');
    }
}
