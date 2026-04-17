<?php

namespace App\Http\Controllers;

use App\Models\CropType;
use Illuminate\Http\Request;

class CropTypeController extends Controller
{
    public function index()
    {
        $cropTypes = CropType::orderBy('name')->get();
        return view('master.crop-type.index', compact('cropTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:crop_types,name',
            'code' => 'nullable|string|max:50|unique:crop_types,code',
            'description' => 'nullable|string|max:1000',
        ]);

        CropType::create($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('crop-types.index')->with('success', 'Crop type added successfully.');
    }

    public function update(Request $request, $id)
    {
        $cropType = CropType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:crop_types,name,' . $cropType->id,
            'code' => 'nullable|string|max:50|unique:crop_types,code,' . $cropType->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $cropType->update($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('crop-types.index')->with('success', 'Crop type updated successfully.');
    }

    public function destroy($id)
    {
        $cropType = CropType::findOrFail($id);
        $cropType->update(['status' => 0]);
        return redirect()->route('crop-types.index')->with('success', 'Crop type deactivated.');
    }
}
