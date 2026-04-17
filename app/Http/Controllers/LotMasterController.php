<?php

namespace App\Http\Controllers;

use App\Models\LotMaster;
use App\Models\LotType;
use Illuminate\Http\Request;

class LotMasterController extends Controller
{
    public function index()
    {
        $lots    = LotMaster::with('lotType')->orderBy('name')->paginate(15);
        $lotType = LotType::where('status', 1)->orderBy('name')->get();
        return view('master.lot.index', compact('lots', 'lotType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:lot_masters,name',
            'code'        => 'nullable|string|max:50|unique:lot_masters,code',
            'lot_type_id' => 'required|exists:lot_types,id',
            'status'      => 'required|in:0,1',
        ]);

        LotMaster::create($request->only('name', 'code', 'lot_type_id', 'description', 'status'));
        return redirect()->route('lots.index')->with('success', 'Lot added successfully.');
    }

    public function update(Request $request, LotMaster $lot)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:lot_masters,name,' . $lot->id,
            'code'        => 'nullable|string|max:50|unique:lot_masters,code,' . $lot->id,
            'lot_type_id' => 'required|exists:lot_types,id',
            'status'      => 'required|in:0,1',
        ]);

        $lot->update($request->only('name', 'code', 'lot_type_id', 'description', 'status'));
        return redirect()->route('lots.index')->with('success', 'Lot updated successfully.');
    }

    public function destroy(LotMaster $lot)
    {
        $lot->update(['status' => 0]);
        return redirect()->route('lots.index')->with('success', 'Lot deactivated.');
    }
}
