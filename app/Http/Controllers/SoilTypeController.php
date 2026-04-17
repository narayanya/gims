<?php

namespace App\Http\Controllers;

use App\Models\SoilType;
use Illuminate\Http\Request;

class SoilTypeController extends Controller
{
    public function index()
    {
        $soiltypes = SoilType::latest()->get();
        return view('master.soil-types.index', compact('soiltypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        SoilType::create($request->all());

        return redirect()->route('soil-types.index')
            ->with('success','Soil Type created successfully');
    }

    public function update(Request $request, SoilType $soilType)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $soilType->update($request->all());

        return redirect()->route('soil-types.index')
            ->with('success','Soil Type updated successfully');
    }

    public function destroy(SoilType $soilType)
    {
        $soilType->status = 0; // inactive
        $soilType->save();
        // $soilType->delete();

        return redirect()->route('soil-types.index')
            ->with('success','Soil Type deleted successfully');
    }
}
