<?php

namespace App\Http\Controllers;

use App\Models\Accession;
use App\Models\Crop;
use App\Models\Variety;
use App\Models\Warehouse;
use App\Models\StorageLocation;
use App\Models\StorageType;
use App\Models\StorageTime;
use App\Models\StorageCondition;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\CoreCityVillage;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\AccessionImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AccessionsExport;
use App\Models\AccessionPassport;
use App\Models\SeedQuality;
use App\Models\SeedRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AccessionController extends Controller
{
    public function index()
    {
        $query = Accession::with(['crop', 'warehouse', 'storageLocation', 'storageTime', 'capacityUnit'])
            ->orderBy('created_at', 'desc');

        // Apply condition BEFORE paginate
        if (auth()->user()->hasRole(['user', 'researcher'])) {
            $query->where('requester_show', 'yes');
        }

        $accessions = $query->paginate(15);

        $crops = Crop::where([
                ['is_active', 1],
                ['update_status', 1]
            ])->select('id', 'crop_name')->get();

        $warehouses = Warehouse::orderBy('name')->get();
        $storageTime = StorageTime::orderBy('name')->get();
        $storageCondition = StorageCondition::orderBy('name')->get();
        
        return view('accession.accession-list', compact('accessions','crops','warehouses', 'storageTime', 'storageCondition'));
    }
    public function showJson($id)
    {
        $accession = Accession::with([
            'crop',
            'country',
            'state',
            'district',
            'city',
            'storageTime',
            'capacityUnit',
            'images',
        ])->findOrFail($id);

        $totalQty = \App\Models\SeedQuantity::where('accession_id', $id)->sum('quantity_show');

        return response()->json([
            'id'                => $accession->id,
            'accession_number'  => $accession->accession_number,
            'accession_name'    => $accession->accession_name,
            'crop_name'         => $accession->crop?->crop_name,
            'scientific_name'   => $accession->crop?->scientific_name,
            'family_name'       => $accession->crop?->family_name,
            'genus'             => $accession->crop?->genus,
            'collection_number' => $accession->collection_number,
            'collection_date'   => $accession->collection_date?->format('d M Y'),
            'collector_name'    => $accession->collector_name,
            'donor_name'        => $accession->donor_name,
            'collection_site'   => $accession->collection_site,
            'country'           => $accession->country?->country_name,
            'state'             => $accession->state?->state_name,
            'district'          => $accession->district?->district_name,
            'city'              => $accession->city?->city_village_name,
            'unit'              => $accession->capacityUnit?->name,
            'storage_time'      => $accession->storageTime?->name,
            'biological_status' => $accession->biological_status,
            'sample_type'       => $accession->sample_type,
            'status'            => $accession->status == 1 ? 'Active' : 'Inactive',
            'expiry_date'       => $accession->expiry_date?->format('d M Y'),
            'recheck_date'      => $accession->recheck_date?->format('d M Y'),
            'barcode'           => $accession->barcode,
            'notes'             => $accession->notes,
            'photo_url'         => $accession->images->first()
                                    ? asset('storage/accessions/images/' . $accession->images->first()->image_name)
                                    : null,
            'can_view_full'     => auth()->user()->hasPermission('accession.view') || auth()->user()->hasRole('super-admin'),
        ]);
    }

    public function show($id, Request $request)
    {
        $accession = Accession::with([
            'crop',
            'warehouse',
            'country',  
            'state',    
            'district',  
            'city',     
            'passports',
            'seedQualities.researcher'

        ])->findOrFail($id);
        $countries = Country::orderBy('country_name')->get();
        $states = State::where('country_id', $accession->country_id)->orderBy('state_name')->get();
        $districts = District::where('state_id', $accession->state_id)->orderBy('district_name')->get();
        $cities = CoreCityVillage::where('district_id', $accession->district_id)
                    ->orderBy('city_village_name')
                    ->get(['id','city_village_name']);

        
        if ($request->ajax()) {
    return response()->json([
        'id' => $accession->id,
        'accession_number' => $accession->accession_number,
        'accession_name' => $accession->accession_name,
        'collection_number' => $accession->collection_number,
        'collection_date' => $accession->collection_date,
        'donor_name' => $accession->donor_name,
        'collector_name' => $accession->collector_name,
        'status' => $accession->status,

        'photo_url' => $accession->photo 
            ? asset('storage/' . $accession->photo)
            : null,

        'crop' => [
            'crop_name' => $accession->crop?->crop_name,
            'scientific_name' => $accession->crop?->scientific_name,
            'family_name' => $accession->crop?->family_name,
            'genus' => $accession->crop?->genus,
        ],

        'country' => [
            'name' => $accession->country?->country_name
        ],

        'warehouse' => [
            'name' => $accession->warehouse?->name
        ]
    ]);
}

        return view('accession.show', compact('accession' ,'countries', 'states', 'districts', 'cities'));
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('accession.edit')) {
            abort(403, 'You do not have permission to edit accessions.');
        }
        $accession = Accession::with(['crop.season'])->findOrFail($id);
        $crops = Crop::where([
                ['is_active', 1],
                ['update_status', 1]
            ])->select('id', 'crop_name')->get();
        $users = \App\Models\User::orderBy('name')->get();
        $countries = Country::orderBy('country_name')->get();
        $states = State::where('country_id', $accession->country_id)->orderBy('state_name')->get();
        $districts = District::where('state_id', $accession->state_id)->orderBy('district_name')->get();
        $cities = CoreCityVillage::where('district_id', $accession->district_id)
                    ->orderBy('city_village_name')
                    ->get(['id','city_village_name']);
        $units = Unit::all();
        $warehouses = Warehouse::all();
        $storageLocations = StorageLocation::all();
        $storageTypes = StorageType::all();
        $storageTime = \App\Models\StorageTime::orderBy('name')->get();
        $storageConditions = StorageCondition::all();
        
        // ✅ Get related requests
        $accessionRequests = SeedRequest::where('accession_id', $id)->get();

        return view('accession.accessionform', compact(
            'accession', 'crops', 'countries', 'states',
            'districts', 'cities', 'units', 'warehouses', 'storageLocations', 'users',
            'storageTypes', 'storageTime', 'storageConditions', 'accessionRequests'
        ));
    }

    public function update(Request $request,$id)
    {
        if (!auth()->user()->hasPermission('accession.edit')) {
            abort(403, 'You do not have permission to edit accessions.');
        }
        $accession = Accession::findOrFail($id);

        $request->validate([
            'crop_id' => 'required|exists:core_crop,id',
            'sample_id' => 'nullable|string|max:100',
            'regen_year' => 'nullable|integer|min:1|max:100',
        ]);

        //$accession->update($request->all());
        $accession->update(
            $request->except(['passport', '_token', '_method', 'images', 'passport_file'])
        );
        $accession->passports()->delete(); // clear old
        // ❗ DELETE OLD SEED DATA
        $accession->seedQualities()->delete();

        if ($request->has('passport')) {
            foreach ($request->passport as $row) {
                if (!empty($row['sample_name']) || !empty($row['passport_no'])) {
                    $accession->passports()->create($row);
                }
            }
        }

        return redirect()->route('accession.accession-list')
            ->with('success','Accession updated successfully');
    }
    public function create()
    {
        if (!auth()->user()->hasPermission('accession.create')) {
            abort(403, 'You do not have permission to create accessions.');
        }
        $crops = Crop::where([
                ['is_active', 1],
                ['update_status', 1]
            ])->select('id', 'crop_name')->get();

        $units = \App\Models\Unit::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        $storageTime = \App\Models\StorageTime::orderBy('name')->get();
        $countries = \App\Models\Country::orderBy('country_name')->get();
        $states    = collect();
        $districts = collect();
        $cities    = collect();
        $accessionRequests = collect();
        $accession = null; // explicit null for view

        return view('accession.accessionform', compact(
            'crops', 'units', 'users', 'warehouses',
            'storageTime', 'countries', 'states', 'districts', 'cities',
            'accessionRequests', 'accession'
        ));
    }

    public function store(Request $request)
    {
    try {
            $validated = $request->validate([
                // Basic Information
                'acc_source' => 'required|in:internal,external',
                'ext_source' => 'required_if:acc_source,external|nullable|string|max:255',
                'source_document' => 'required_if:acc_source,external|nullable|mimes:pdf,doc,docx,csv|max:5120',
                'sample_id' => 'required|string|max:100',
                'year_of_arrival' => 'nullable',
                'regen_year' => 'nullable|integer|min:1|max:100',
                'requester_show' => 'required|in:yes,no',
                'storage_time' => 'nullable|exists:storage_times,id',
                'accession_name' => 'required|string|max:255',
                'crop_id' => 'required|exists:core_crop,id',
                'scientific_name' => 'nullable|string|max:255',
                
                // Collection Information
                'collection_number' => 'nullable|string|max:100',
                'collection_date' => 'nullable|date',
                'collector_name' => 'nullable|string|max:255',
                'donor_name' => 'nullable|string|max:255',
                'collection_site' => 'nullable|string|max:255',
                'country_id' => 'nullable|exists:core_country,id',
                'state_id' => 'nullable|exists:core_state,id',
                'district_id' => 'nullable|exists:core_district,id',
                'city_id' => 'nullable|exists:core_city_village,id',
                'latitude'  => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'pincode'   => 'nullable|string|max:20',
                
                // Biological/Genetic Information
                'biological_status' => 'nullable|in:Wild,Landrace,Breeding Material,Improved Variety',
                'sample_type' => 'nullable|in:Seed,Plant,Tissue',
                'reproductive_type' => 'nullable|in:Self Pollinated,Cross Pollinated',

                
                // Storage Information
                'warehouse_id'         => 'nullable|exists:warehouses,id',
                'storage_location_id'  => 'nullable|exists:storage_locations,id',
                'storage_time_id'      => 'nullable|exists:storage_times,id',
                'storage_condition_id' => 'nullable|exists:storage_conditions,id',
                'storage_type_id'      => 'nullable|exists:storage_types,id',
                'altitude'             => 'nullable|integer',
                
                
                // Documentation
                'barcode_type' => 'required|in:auto,manual,existing,scan,none',
                'barcode' => 'nullable|string|max:100',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
                'passport_file' => 'nullable|mimes:pdf,doc,docx,csv|max:5120',
                'notes' => 'nullable|string',
                
                // System Fields
                'entry_date' => 'nullable|date',
                'entered_by' => 'nullable|exists:users,id',
                'status' => 'required',
                'recheck_date' => 'required|before:expiry_date',
                'expiry_date' => 'required|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Set defaults
            if (!$request->entry_date) $validated['entry_date'] = now();
            if (!$request->entered_by) $validated['entered_by'] = Auth::id();
            
            $passportData    = $request->input('passport', []);

            unset(
                 
                $validated['images'],
                $validated['passport_file']
            );
            $cropCode = \App\Models\Crop::where('id', $validated['crop_id'])->value('crop_code');
            

            if (!$cropCode) {
                throw new \Exception('Invalid Crop selected');
            }
           // $validated['accession_number'] = Accession::generateAccessionNumber($cropCode);
            // Single create
            $accession = Accession::create(array_merge($validated, [
                'accession_number' => null, // temporary    
                'image_path'         => null,
                'passport_file_path' => null,
                'created_by'         => Auth::id() ?? null,
            ]));

            // 👇 Generate accession_number using ID
            $crop = \App\Models\Crop::find($accession->crop_id);
            $year_of_arrival = $request->year_of_arrival ?? date('Y');

            $accessionNumber = $crop->crop_code . '-' . ($request->year_of_arrival ?? date('Y')) . '-ACC-' . $accession->sample_id . '-' .
    str_pad($accession->id, 5, '0', STR_PAD_LEFT);

            // 👇 Update record
            $accession->update([
                'accession_number' => $accessionNumber
            ]);

            // Image upload (after create to use ID in filename) — max 5
            if ($request->hasFile('images')) {
                $files = array_slice($request->file('images'), 0, 5);
                foreach ($files as $i => $file) {
                    if (!$file->isValid()) continue;
                    $fileName = 'acc_' . str_pad($accession->id, 3, '0', STR_PAD_LEFT) . '_' . ($i + 1) . '_' . now()->format('dmY') . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('accessions/images', $fileName, 'public');
                    \App\Models\AccessionImage::create([
                        'accession_id' => $accession->id,
                        'image_name'   => $fileName,
                        'is_primary'   => $i === 0 ? 1 : 0,
                        'sort_order'   => $i,
                    ]);
                }
                // Keep first image in image_path for backward compat
                $first = $accession->images()->first();
                if ($first) $accession->update(['image_path' => $first->image_name]);
            }

            if ($request->hasFile('passport_file')) {
                $pfName = 'pf_' . str_pad($accession->id, 3, '0', STR_PAD_LEFT) . '_' . now()->format('dmY') . '.' . $request->file('passport_file')->getClientOriginalExtension();
                $request->file('passport_file')->storeAs('accessions/passports', $pfName, 'public');
                $accession->update(['passport_file_path' => $pfName]);
            }

            if ($request->hasFile('source_document')) {
                $docName = 'src_' . str_pad($accession->id, 3, '0', STR_PAD_LEFT) . '_' . now()->format('dmY') . '.' . $request->file('source_document')->getClientOriginalExtension();

                $request->file('source_document')->storeAs('accessions/source_docs', $docName, 'public');

                $accession->update([
                    'source_document_path' => $docName
                ]);
            }

            // Save passport rows
            foreach ($passportData as $row) {
                if (!empty($row['sample_name']) || !empty($row['passport_no'])) {
                    AccessionPassport::create([
                        'accession_id' => $accession->id,
                        'sample_name'  => $row['sample_name'] ?? null,
                        'sample_name_o'  => $row['sample_name_o'] ?? null,
                        'passport_no'  => $row['passport_no'] ?? null,
                        'remarks'      => $row['remarks'] ?? null,
                    ]);
                }
            }


            DB::commit();
            return redirect()->route('accession.accession-list')
                ->with('success', 'Accession created successfully! Barcode: ' . ($accession->barcode ?? 'N/A'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Accession creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create accession: ' . $e->getMessage()]);
        }
    }
    
    public function getStatesByCountry($countryId)
    {
        $states = \App\Models\State::where('country_id', $countryId)
            ->orderBy('state_name')
            ->get(['id', 'state_name']);
        
        return response()->json($states);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls'
        ]);

        Excel::import(new AccessionImport, $request->file('file'));

        return redirect()->route('accession.accession-list')
            ->with('success', 'Accessions imported successfully!');
    }
    public function export()
    {
        return Excel::download(new AccessionsExport, 'accessions.xlsx');
    }
    public function getVarieties($crop_id)
    {
        return Variety::where('crop_id',$crop_id)->get();
    }

    public function getCities($district_id)
    {
        return response()->json(
            CoreCityVillage::where('district_id', $district_id)
                ->orderBy('city_village_name')
                ->get(['id', 'city_village_name', 'latitude', 'longitude', 'pincode'])
        );
    }

    public function getDistricts($state_id)
    {
        return response()->json(
            District::where('state_id', $state_id)
                ->orderBy('district_name')
                ->get(['id', 'district_name'])
        );
    }

  
}
