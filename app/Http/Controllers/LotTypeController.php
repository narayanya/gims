<?php

namespace App\Http\Controllers;

use App\Models\LotType;
use Illuminate\Http\Request;

class LotTypeController extends Controller
{
    public function index()
    {
        $lotTypes = LotType::orderBy('name')->paginate(15);
        return view('master.lot-type.index', compact('lotTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:lot_types,name',
            'code'        => 'nullable|string|max:20|unique:lot_types,code',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:0,1',
        ]);

        LotType::create($request->only('name', 'code', 'description', 'status'));

        return redirect()->route('lot-types.index')->with('success', 'Lot management type added successfully.');
    }

    public function edit($id)
    {
        return response()->json(LotType::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $lotType = LotType::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255|unique:lot_types,name,' . $id,
            'code'        => 'nullable|string|max:20|unique:lot_types,code,' . $id,
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:0,1',
        ]);

        $lotType->update($request->only('name', 'code', 'description', 'status'));

        return redirect()->route('lot-types.index')->with('success', 'Lot management type updated successfully.');
    }

    public function destroy($id)
    {
        LotType::findOrFail($id)->delete();
        return redirect()->route('lot-types.index')->with('success', 'Lot management type deleted successfully.');
    }
}
