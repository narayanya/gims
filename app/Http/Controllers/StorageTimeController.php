<?php

namespace App\Http\Controllers;

use App\Models\StorageTime;
use Illuminate\Http\Request;

class StorageTimeController extends Controller
{
    public function index()
    {
        $storageTimes = StorageTime::orderBy('name')->paginate(15);
        return view('master.storage-time.index', compact('storageTimes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255|unique:storage_times,name',
            'code'           => 'nullable|string|max:20|unique:storage_times,code',
            'duration_value' => 'nullable|integer|min:1',
            'duration_unit'  => 'nullable|in:days,months,years',
            'description'    => 'nullable|string|max:1000',
            'status'         => 'required|in:0,1',
        ]);

        StorageTime::create($request->only('name', 'code', 'duration_value', 'duration_unit', 'description', 'status'));

        return redirect()->route('storage-times.index')->with('success', 'Storage time added successfully.');
    }

    public function edit($id)
    {
        return response()->json(StorageTime::findOrFail($id));
    }

    public function update(Request $request, StorageTime $storage_time)
    {
        $request->validate([
            'name'           => 'required|string|max:255|unique:storage_times,name,' . $storage_time->id,
            'code'           => 'nullable|string|max:20|unique:storage_times,code,' . $storage_time->id,
            'duration_value' => 'nullable|integer|min:1',
            'duration_unit'  => 'nullable|in:days,months,years',
            'description'    => 'nullable|string|max:1000',
            'status'         => 'required|in:0,1',
        ]);

        $storage_time->update($request->only('name', 'code', 'duration_value', 'duration_unit', 'description', 'status'));

        return redirect()->route('storage-times.index')->with('success', 'Storage time updated successfully.');
    }

    public function destroy(StorageTime $storage_time)
    {
        $storage_time->status = 0;
        $storage_time->save();
        return redirect()->route('storage-times.index')->with('success', 'Storage time deactivated successfully.');
    }
}
