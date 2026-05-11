<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use Illuminate\Http\Request;
use App\Imports\CropImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CropCategory;
use App\Models\CropType;
use App\Models\Season;
use App\Models\Category;
use App\Models\SoilType;

class CropController extends Controller
{

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = Crop::with([
            'category',
            'cropCategory',
            'cropType',
            'season',
            'soilType'
        ]);

        // Search by name, code, or scientific name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('crop_name', 'like', "%{$search}%")
                  ->orWhere('crop_code', 'like', "%{$search}%")
                  ->orWhere('scientific_name', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $crops = $query->latest()->paginate(10)->withQueryString();

        $cropcategories = CropCategory::all();
        $types = CropType::all();
        $seasons = Season::all();
        $categories = Category::all();
        $soiltypes = SoilType::all();
        $units = \App\Models\Unit::all();
        $pouches = \App\Models\Pouch::all();

        return view('master.crop.index', compact(
            'crops',
            'cropcategories',
            'types',
            'seasons',
            'categories',
            'soiltypes',
            'units',
            'pouches'
        ));
    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        $request->validate([
            'crop_name' => 'nullable',
            'crop_code' => 'nullable',
            'category_id' => 'required',
            'crop_category_id' => 'required',
            'crop_type_id' => 'required',
            'season_id' => 'required',
            'soil_type_id' => 'required',
            'is_active' => 'required'
        ]);

        Crop::create($request->all());

        return redirect()->route('crops.index')
            ->with('success', 'Crop added successfully');
    }


    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Crop $crop)
    {
        
        $request->validate([
            'crop_name' => 'nullable',
            'crop_code' => 'nullable',
            'category_id' => 'required',
            'crop_category_id' => 'required',
            'crop_type_id' => 'required',
            'season_id' => 'required',
            'soil_type_id' => 'required',
            'is_active' => 'required',
             'season_start_month_id' => 'required|integer|min:1|max:12',
    'season_end_month_id'   => 'required|integer|min:1|max:12',
], [
    'season_end_month_id.required' => 'End month is required.',
        ]);

       

$startMonth = (int) $request->season_start_month_id;
$endMonth   = (int) $request->season_end_month_id;

// Calculate difference
$monthDiff = $endMonth - $startMonth;

// Handle year crossover (Nov → Feb etc.)
if ($monthDiff < 0) {
    $monthDiff += 12;
}

// Minimum 3 months validation
if ($monthDiff < 3) {
    return back()
        ->withErrors([
            'season_end_month_id' => 'End month must be at least 3 months after start month.'
        ])
        ->withInput();
}
        $data = $request->all();
        $data['unit_id'] = is_array($request->unit_id)
            ? $request->unit_id[0]
            : $request->unit_id;
        // ✅ set update_status

        $data['update_status'] = $request->update_status ?? 0;

        // ✅ ONLY update date when editing
        if ($request->update_status == 1) {
            $data['update_date'] = now();
        }
        
        $crop->update($request->all());

        return redirect()->route('crops.index')
            ->with('success', 'Crop updated successfully');
    }


    /**
     * Edit Crop
     */

    public function edit($id)
    {
        $crop = Crop::findOrFail($id);
        return response()->json($crop);
    }


    /**
     * Delete Crop
     */

    public function destroy($id)
    {

        $crop = Crop::findOrFail($id);

        $crop->delete();

        return redirect()->route('crops.index')
            ->with('success', 'Crop deleted successfully.');
    }


    /**
     * Import Crops
     */

    public function import(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls'
        ]);

        Excel::import(new CropImport, $request->file('file'));

        return redirect()->route('crops.index')
            ->with('success', 'Crops imported successfully!');
    }

    public function getCropDetails($id)
    {
        $crop = \App\Models\Crop::with([
            'category:id,name',
            'cropCategory:id,name',
            'cropType:id,name',
            'season:id,name,start_month,end_month',
            'soilType:id,name'
        ])->find($id);

        if (!$crop) {
            return response()->json(['error' => 'Crop not found'], 404);
        }

        return response()->json($crop);
    }

}