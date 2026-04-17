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

    public function index()
    {

        $crops = Crop::with([
    'category',
    'cropCategory',
    'cropType',
    'season',
    'soilType'
])->latest()->paginate(10);

        $cropcategories = CropCategory::all();
        $types = CropType::all();
        $seasons = Season::all();
        $categories = Category::all();
        $soiltypes = SoilType::all();

        return view('master.crop.index', compact(
            'crops',
            'cropcategories',
            'types',
            'seasons',
            'categories',
            'soiltypes'
        ));
    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        $request->validate([
            'crop_name' => 'required',
            'crop_code' => 'required',
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
            'crop_name' => 'required',
            'crop_code' => 'required',
            'category_id' => 'required',
            'crop_category_id' => 'required',
            'crop_type_id' => 'required',
            'season_id' => 'required',
            'soil_type_id' => 'required',
            'is_active' => 'required'
        ]);

        $data = $request->all();

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
        $crop = \App\Models\Crop::select('scientific_name','family_name','genus')
                ->find($id);

        return response()->json($crop);
    }

}