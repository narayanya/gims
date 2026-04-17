<?php

namespace App\Http\Controllers;

use App\Models\CropCategory;
use Illuminate\Http\Request;

class CropCategoryController extends Controller
{
    public function index()
    {
        $cropCategories = CropCategory::orderBy('name')->get();
        return view('master.crop-category.index', compact('cropCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:crop_categories,name',
            'code'        => 'nullable|string|max:50|unique:crop_categories,code',
            'description' => 'nullable|string|max:1000',
        ]);
        CropCategory::create($request->only('name', 'code', 'description', 'status'));
        return redirect()->route('crop-categories.index')->with('success', 'Crop category added successfully.');
    }

    public function update(Request $request, $id)
    {
        $cropCategory = CropCategory::findOrFail($id);
        $request->validate([
            'name'        => 'required|string|max:255|unique:crop_categories,name,' . $cropCategory->id,
            'code'        => 'nullable|string|max:50|unique:crop_categories,code,' . $cropCategory->id,
            'description' => 'nullable|string|max:1000',
        ]);
        $cropCategory->update($request->only('name', 'code', 'description', 'status'));
        return redirect()->route('crop-categories.index')->with('success', 'Crop category updated successfully.');
    }

    public function destroy($id)
    {
        CropCategory::findOrFail($id)->update(['status' => 0]);
        return redirect()->route('crop-categories.index')->with('success', 'Crop category deactivated.');
    }
}
