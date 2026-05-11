<?php

namespace App\Http\Controllers;

use App\Models\Pouch;
use Illuminate\Http\Request;

class PouchController extends Controller
{
    public function index()
    {
        $pouches = Pouch::orderBy('name')->get();
        return view('master.pouch.index', compact('pouches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255|unique:pouches,name',
            'code'           => 'nullable|string|max:50|unique:pouches,code',
            'length'         => 'nullable|numeric|min:0',
            'width'          => 'nullable|numeric|min:0',
            'height'         => 'nullable|numeric|min:0',
            'dimension_unit' => 'required|in:cm,mm,inch',
            'description'    => 'nullable|string|max:1000',
            'status'         => 'required|in:0,1',
        ]);

        Pouch::create($request->only('name', 'code', 'length', 'width', 'height', 'dimension_unit', 'status', 'description'));
        return redirect()->route('pouches.index')->with('success', 'Pouch added successfully.');
    }

    public function update(Request $request, $id)
    {
        $pouch = Pouch::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:255|unique:pouches,name,' . $pouch->id,
            'code'           => 'nullable|string|max:50|unique:pouches,code,' . $pouch->id,
            'length'         => 'nullable|numeric|min:0',
            'width'          => 'nullable|numeric|min:0',
            'height'         => 'nullable|numeric|min:0',
            'dimension_unit' => 'required|in:cm,mm,inch',
            'description'    => 'nullable|string|max:1000',
            'status'         => 'required|in:0,1',
        ]);

        $pouch->update($request->only('name', 'code', 'length', 'width', 'height', 'dimension_unit', 'status', 'description'));
        return redirect()->route('pouches.index')->with('success', 'Pouch updated successfully.');
    }

    public function destroy($id)
    {
        $pouch = Pouch::findOrFail($id);
        $pouch->update(['status' => 0]);
        return redirect()->route('pouches.index')->with('success', 'Pouch deactivated.');
    }
}
