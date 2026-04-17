<?php

namespace App\Http\Controllers;

use App\Models\StorageType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = StorageType::orderBy('name')->get();
        return view('master.storage-types.index', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:storage_types,name',
            'description' => 'nullable|string|max:1000',
        ]);

        StorageType::create([
        'name' => $request->name,
        'description' => $request->description,
        'status' => 'active',
    ]);
        return redirect()->route('storage-types.index')->with('success', 'Storage type added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $type = StorageType::findOrFail($id);
        return response()->json($type); // We'll use AJAX to populate the modal
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $type = StorageType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:storage_types,name,' . $type->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $type->update($request->only('name', 'description'));
        return redirect()->route('storage-types.index')->with('success', 'Storage type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $type = StorageType::findOrFail($id);
        $type->status = 0; // inactive // or 0 if you use integer
        $type->save();
        //$type->delete();
        return redirect()->route('storage-types.index')->with('success', 'Storage type deleted successfully.');
    }
}
