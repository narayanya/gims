<?php

namespace App\Http\Controllers;

use App\Models\Variety;
use App\Models\Crop;
use App\Models\VarietyType;
use App\Models\SeedClass;
use Illuminate\Http\Request;
use App\Imports\VarietyImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Country;
use App\Models\State;
use App\Models\District;

class VarietyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
        {
            $varieties = Variety::with('crop')->latest()->get();

            $crops = Crop::where([
                ['is_active', 1],
                ['update_status', 1]
            ])->select('id', 'crop_name')->get();
            $varietyTypes = VarietyType::all();
            $seedClasses = SeedClass::all();
            $countries = Country::all();
            $states = State::all();
            $districts = District::all();

            return view('master.variety.index', compact(
                'varieties',
                'crops',
                'varietyTypes',
                'seedClasses',
                'countries',
                'states' ,
                'districts'
            ));
        }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'variety_name' => 'required',
        'variety_type_id'=>'required',
        'variety_code' => 'nullable|unique:varieties,variety_code',
        'crop_id' => 'required',
    ]);

    Variety::create([
        'crop_id' => $request->crop_id,
        'variety_name' => $request->variety_name,
        'variety_code' => $request->variety_code,
        'variety_type_id' => $request->variety_type_id,
        'breeder_name' => $request->breeder_name,
        'release_year' => $request->release_year,
        'release_authority' => $request->release_authority,
        'description' => $request->description,

        'source' => $request->source,
        'country_id' => $request->country_id,
        'state_id' => $request->state_id,
        'district_id' => $request->district_id,
        
        'maturity_duration' => $request->maturity_duration,
        'plant_height' => $request->plant_height,
        'grain_type' => $request->grain_type,
        'seed_color' => $request->seed_color,
        'yield_potential' => $request->yield_potential,

        'germination_percent' => $request->germination_percent,
        'purity_percent' => $request->purity_percent,
        'moisture_percent' => $request->moisture_percent,
        'test_weight' => $request->test_weight,

        'disease_resistance' => $request->disease_resistance,
        'pest_resistance' => $request->pest_resistance,
        'drought_tolerance' => $request->drought_tolerance,
        'flood_tolerance' => $request->flood_tolerance,
        'salinity_tolerance' => $request->salinity_tolerance,

        'isolation_distance' => $request->isolation_distance,
        'seed_class_id' => $request->seed_class_id,
        'production_region' => $request->production_region,
        'storage_life' => $request->storage_life,

        'variety_status' => $request->variety_status
    ]);

    return redirect()->back()->with('success','Variety created successfully');
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $variety = Variety::findOrFail($id);
        return response()->json($variety);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {

    $variety = Variety::findOrFail($id);

    $request->validate([
    'variety_name'=>'required|max:255',
    'variety_code'=>'nullable|max:50|unique:varieties,variety_code,'.$variety->id,
    'crop_id'=>'required|exists:crops,id',
    'variety_type'=>'required'
    ]);

    $variety->update($request->all());

    return redirect()->route('varieties.index')
    ->with('success','Variety updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $variety = Variety::findOrFail($id);
        $variety->delete();
        return redirect()->route('varieties.index')->with('success', 'Variety deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls'
        ]);

        Excel::import(new VarietyImport, $request->file('file'));

        return redirect()->route('varieties.index')
            ->with('success', 'Varieties imported successfully!');
    }
}
