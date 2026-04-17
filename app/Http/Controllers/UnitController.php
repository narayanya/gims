<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $units = Unit::orderBy('name')->get();
        return view('master.unit.index', compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'code' => 'nullable|string|max:50|unique:units,code',
            'description' => 'nullable|string|max:1000',
        ]);

        Unit::create($request->only('name', 'code', 'description'));
        return redirect()->route('units.index')->with('success', 'Unit added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json($unit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'code' => 'nullable|string|max:50|unique:units,code,' . $unit->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $unit->update($request->only('name', 'code', 'description'));
        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }
}
