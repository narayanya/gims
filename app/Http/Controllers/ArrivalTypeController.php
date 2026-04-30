<?php

namespace App\Http\Controllers;

use App\Models\ArrivalType;
use Illuminate\Http\Request;

class ArrivalTypeController extends Controller
{
    public function index()
    {
        $arrivalTypes = ArrivalType::orderBy('name')->get();
        return view('master.arrival-type.index', compact('arrivalTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:arrival_types,name',
            'code'        => 'nullable|string|max:50|unique:arrival_types,code',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:0,1',
        ]);

        ArrivalType::create($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('arrival-types.index')->with('success', 'Arrival Type added successfully.');
    }

    public function update(Request $request, $id)
    {
        $arrivalType = ArrivalType::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255|unique:arrival_types,name,' . $arrivalType->id,
            'code'        => 'nullable|string|max:50|unique:arrival_types,code,' . $arrivalType->id,
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:0,1',
        ]);

        $arrivalType->update($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('arrival-types.index')->with('success', 'Arrival Type updated successfully.');
    }

    public function destroy($id)
    {
        $arrivalType = ArrivalType::findOrFail($id);
        $arrivalType->update(['status' => 0]);
        return redirect()->route('arrival-types.index')->with('success', 'Arrival Type deactivated.');
    }
}
