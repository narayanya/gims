<?php

namespace App\Http\Controllers;

use App\Models\SeedClass;
use Illuminate\Http\Request;

class SeedClassController extends Controller
{
    public function index()
    {
        $seedClasses = SeedClass::orderBy('name')->get();
        return view('master.seed-class.index', compact('seedClasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:seed_classes,name',
            'code' => 'nullable|string|max:50|unique:seed_classes,code',
            'description' => 'nullable|string|max:1000',
        ]);

        SeedClass::create($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('seed-classes.index')->with('success', 'Seed class added successfully.');
    }

    public function update(Request $request, $id)
    {
        $seedClass = SeedClass::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:seed_classes,name,' . $seedClass->id,
            'code' => 'nullable|string|max:50|unique:seed_classes,code,' . $seedClass->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $seedClass->update($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('seed-classes.index')->with('success', 'Seed class updated successfully.');
    }

    public function destroy($id)
    {
        $seedClass = SeedClass::findOrFail($id);
        $seedClass->update(['status' => 0]);
        return redirect()->route('seed-classes.index')->with('success', 'Seed class deactivated.');
    }
}
