<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\City;
use App\Models\State;
use App\Models\District;
use App\Models\Country;

use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $warehouses = Warehouse::with(['country','state','district','city'])
            ->orderBy('name')
            ->get();

        $query = City::with('state');

        if ($request->filled('search')) {
            $query->where('city_village_name', 'like', '%' . $request->search . '%')
                ->orWhere('city_village_code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        $cities = $query->orderBy('city_village_name')->paginate(20)->withQueryString();

        $states = State::orderBy('state_name')->get();
        $districts = District::orderBy('district_name')->get();
        $countries = Country::orderBy('country_name')->get(); // ✅ FIXED

        return view('master.warehouse.index', compact(
            'warehouses', 'cities', 'states', 'districts', 'countries'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name',
            'code' => 'nullable|string|max:50|unique:warehouses,code',
            'country_id' => 'required',
        'state_id' => 'required',
        'district_id' => 'required',
        'city_id' => 'required',
            'description' => 'nullable|string|max:1000',
            'status' => 'required'
        ]);

        Warehouse::create($request->only('name', 'code', 'description', 'country_id',
    'state_id',
    'district_id',
    'city_id', 'status'));
        return redirect()->route('warehouses.index')->with('success', 'Warehouse added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return response()->json($warehouse);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name,' . $warehouse->id,
            'code' => 'nullable|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $warehouse->update($request->only('name', 'code', 'description', 'country_id',
    'state_id',
    'district_id',
    'city_id', 'status'));
        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully.');
    }
}
