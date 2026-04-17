<?php

namespace App\Http\Controllers;

use App\Models\StorageCondition;
use Illuminate\Http\Request;

class StorageConditionController extends Controller
{
    public function index()
    {
        $conditions = StorageCondition::orderBy('name')->paginate(15);
        return view('master.storage-condition.index', compact('conditions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255|unique:storage_conditions,name',
            'code'         => 'nullable|string|max:20|unique:storage_conditions,code',
            'temp_min'     => 'nullable|numeric',
            'temp_max'     => 'nullable|numeric|gte:temp_min',
            'humidity_min' => 'nullable|numeric|min:0|max:100',
            'humidity_max' => 'nullable|numeric|min:0|max:100|gte:humidity_min',
            'description'  => 'nullable|string|max:1000',
            'status'       => 'required|in:0,1',
        ]);

        StorageCondition::create($request->only(
            'name', 'code', 'temp_min', 'temp_max',
            'humidity_min', 'humidity_max', 'description', 'status'
        ));

        return redirect()->route('storage-conditions.index')->with('success', 'Storage condition added successfully.');
    }

    public function edit($id)
    {
        return response()->json(StorageCondition::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $condition = StorageCondition::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255|unique:storage_conditions,name,' . $id,
            'code'         => 'nullable|string|max:20|unique:storage_conditions,code,' . $id,
            'temp_min'     => 'nullable|numeric',
            'temp_max'     => 'nullable|numeric|gte:temp_min',
            'humidity_min' => 'nullable|numeric|min:0|max:100',
            'humidity_max' => 'nullable|numeric|min:0|max:100|gte:humidity_min',
            'description'  => 'nullable|string|max:1000',
            'status'       => 'required|in:0,1',
        ]);

        $condition->update($request->only(
            'name', 'code', 'temp_min', 'temp_max',
            'humidity_min', 'humidity_max', 'description', 'status'
        ));

        return redirect()->route('storage-conditions.index')->with('success', 'Storage condition updated successfully.');
    }

    public function destroy($id)
    {
        $condition = StorageCondition::findOrFail($id);
        $condition->status = 0; // inactive
        $condition->save();
        // $condition->delete();
        return redirect()->route('storage-conditions.index')->with('success', 'Storage condition deleted successfully.');
    }
}
