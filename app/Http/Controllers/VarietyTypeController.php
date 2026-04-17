<?php

namespace App\Http\Controllers;

use App\Models\VarietyType;
use Illuminate\Http\Request;

class VarietyTypeController extends Controller
{
    public function index()
    {
        $varietyTypes = VarietyType::orderBy('name')->get();
        return view('master.variety-type.index', compact('varietyTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:variety_types,name',
            'code' => 'nullable|string|max:50|unique:variety_types,code',
            'description' => 'nullable|string|max:1000',
        ]);

        VarietyType::create($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('variety-types.index')->with('success', 'Variety type added successfully.');
    }

    public function update(Request $request, $id)
    {
        $varietyType = VarietyType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:variety_types,name,' . $varietyType->id,
            'code' => 'nullable|string|max:50|unique:variety_types,code,' . $varietyType->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $varietyType->update($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('variety-types.index')->with('success', 'Variety type updated successfully.');
    }

    public function destroy($id)
    {
        $varietyType = VarietyType::findOrFail($id);
        $varietyType->update(['status' => 0]);
        return redirect()->route('variety-types.index')->with('success', 'Variety type deactivated.');
    }
}
