<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\LotMaster;
use App\Models\Accession;
use App\Models\Storage;
use App\Models\SeedQuality;
use App\Models\SeedQuantity;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;


class LotController extends Controller
{
    private function formData(): array
    {
        return [
            'accessions' => Accession::orderBy('accession_number')->get(['id','accession_number','accession_name','expiry_date']),
            'storages'   => Storage::where('status', 1)->orderBy('name')->get(['id','storage_id','name']),
            //'crops'      => \App\Models\Crop::where('status',1)->where('update_status', 1)->orderBy('crop_name')->get(['id','crop_name']),
            'units'      => Unit::orderBy('name')->get(['id','name','code']),
            'sections'   => \App\Models\Section::where('status',1)->orderBy('name')->get(),
            'racks'      => \App\Models\Rack::where('status',1)->orderBy('name')->get(),
            'bins'       => \App\Models\Bin::where('status',1)->orderBy('name')->get(),
            'containers' => \App\Models\Container::where('status',1)->orderBy('name')->get(),
            'users'      => User::orderBy('name')->get(['id','name']),
            'seedQuantities' => SeedQuantity::orderBy('id')->get(['id','number_of_seeds', 'number_of_bags','per_seed_weight','quantity','capacity_unit_id','quantity_show', 'reference_number','min_quantity','in_seed','out_seed','return_seed']),

        ];
    }

    // ── List ──────────────────────────────────────────────────────────────

    public function managementIndex()
    {
        $lots = Lot::with(['accession','storage', 'lotType', 'seedQuantities', 'seedQuantities.unit'])
                   ->whereNotNull('lot_number')
                   ->orderBy('created_at','desc')
                   ->paginate(15);
        
        return view('lot-management.index', array_merge($this->formData(), [
            'lots'      => $lots,
            
            //'nextLotNo' => Lot::generateLotNumber(),
        ]));
    }

    // ── Create ────────────────────────────────────────────────────────────

    public function managementCreate()
    {
        $lastRef = \App\Models\SeedQuantity::latest('id')->value('reference_number'); 
        return view('lot-management.create', array_merge($this->formData(), [
            'nextLotNo' => Lot::generateLotNumber(
                'REF', 'REJUV', 'PFX', 'SMPID', 0
            ),
            'lot'       => null,
            'lastRef' => $lastRef,
        ]));
    }
    

    public function managementStore(Request $request)
    {
        $request->validate([
            'accession_id' => 'required|exists:accessions,id',
            'storage_id'   => 'required|exists:storages,id',

            'reference_number'   => 'nullable|array',
            'reference_number.*' => [
                'required',
                'distinct', // ✅ no duplicate in same form
                Rule::unique('lots', 'reference_number') // ✅ DB check
                //->where(function ($query) use ($lot) {
                  //  return $query->where('lot_id', '!=', $lot->id);
                //}),
            ],
            //'reference_number.*' => ['nullable','string','distinct','max:255'],

            'number_of_seeds'    => 'nullable|array',
            'number_of_seeds.*'  => 'nullable|numeric|min:0',

            'number_of_bags'    => 'nullable|array',
            'number_of_bags.*'  => 'nullable|numeric|min:0',

            'per_seed_weight'    => 'nullable|array',
            'per_seed_weight.*'  => 'nullable|numeric|min:0',

            'quantity'           => 'required|array',
            'quantity.*'         => 'required|numeric|min:0',

            'unit_id'            => 'nullable|array',
            'unit_id.*'          => 'nullable|exists:units,id',

            'quantity_show'      => 'nullable|array',
            'quantity_show.*'    => 'nullable|numeric|min:0',

            'min_quantity'       => 'nullable|array',
            'min_quantity.*'     => 'nullable|numeric|min:0',

            'status' => 'required',
        ]);

        $storage   = Storage::with('lots')->findOrFail($request->storage_id);
        $accession = Accession::findOrFail($request->accession_id);


        $used      = $storage->lots()->sum('quantity');
        $available = (float)$storage->capacity - $used;

        $qtys = array_values($request->quantity ?? []);
        $refs = array_values($request->reference_number ?? []);
        $units = array_values($request->unit_id ?? []);
        

        $totalQty = array_sum($qtys);

        if ($totalQty > $available) {
            return back()->withInput()
                ->withErrors(['error' => "Not enough storage space! Available: {$available}"]);
        }
        $runningSeq = null;
        foreach ($qtys as $i => $qty) {

            if (!$qty) continue;

            $reference = $refs[$i] ?? 'REF';

            // 👉 Only fetch last sequence ONCE
            if ($runningSeq === null) {

                $base = "{$request->rejuvenation_program}-{$request->prefix}-{$request->sample_id}-";

                $last = Lot::where('lot_number', 'like', $base . '%')
                    ->orderByRaw("CAST(SUBSTRING_INDEX(lot_number, '-', -1) AS UNSIGNED) DESC")
                    ->first();

                if ($last && preg_match('/-(\d{2})$/', $last->lot_number, $m)) {
                    $runningSeq = (int)$m[1];
                } else {
                    $runningSeq = 0;
                }
            }

            // 👉 increment for each row
            $runningSeq++;
            
            $exists = Lot::where('reference_number', $reference)->exists();

            if ($exists) {
                return back()->withInput()->withErrors([
                    'reference_number' => "Reference number {$reference} already exists."
                ]);
            }

            // 👉 Row index for RP
            $rpWithRow = "{$request->rejuvenation_program}/" . ($i + 1);

            $lotNumber = "{$reference}-{$rpWithRow}-{$request->prefix}-{$request->sample_id}-" 
                . str_pad($runningSeq, 2, '0', STR_PAD_LEFT);

            // ✅ Generate per row
            /*$lotNumber = Lot::generateLotNumber(
                $reference ?? 'REF',
                $request->rejuvenation_program ?? 'GEN',
                $request->prefix ?? 'DEF',
                $request->sample_id ?? '0',
                $i++ //
            );*/

            $lot = Lot::create([
                'lot_number'    => $lotNumber,
                'reference_number' => $reference,
                'rejuvenation_program' => $request->rejuvenation_program,
                'prefix'        => $request->prefix,
                'sample_id'     => $request->sample_id,
                'accession_id'  => $request->accession_id,
                'storage_id'    => $request->storage_id,
                'section_id'    => $request->section_id,
                'rack_id'       => $request->rack_id,
                'bin_id'        => $request->bin_id,
                'container_id'  => $request->container_id,
                'quantity'      => $qty,
                'unit_id'       => $request->unit_id[$i] ?? null,
                'expiry_date'   => $request->expiry_date,
                'description'   => $request->description,
                'status'        => $request->status,
                'crop_id'       => $accession->crop_id,
            ]);

            // ✅ Save child data per lot
            
            $this->saveSeedQuantities($request, $lot->id, $accession->id, $i);
            $this->saveSeedQualities($request, $lot->id, $accession->id);
            
        }
        
        
        $storage->increment('current_usage', $totalQty);

        return redirect()->route('lot-management')
            ->with('success', 'Lots created successfully.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────

    public function managementEdit($id)
    {
        $lot = Lot::with(['accession','storage','seedQualities', 'seedQuantities'])->findOrFail($id);
        return view('lot-management.create', array_merge($this->formData(), [
            'nextLotNo' => $lot->lot_number,
            'lot'       => $lot,
        ]));
    }


    public function managementUpdate(Request $request, $id)
    {
        $lot = Lot::findOrFail($id);

        $request->validate([
            'accession_id' => 'required|exists:accessions,id',
            'storage_id'   => 'required|exists:storages,id',
            'quantity' => 'required|array',
            'quantity.*'     => 'required|numeric|min:0.01',
            'status'       => 'required',
        ]);

        $storage   = Storage::with('lots')->findOrFail($request->storage_id);
        $accession = Accession::findOrFail($request->accession_id);

        $oldQty = (float)($lot->quantity ?? 0);
        $newQty = array_sum($request->quantity);

        $used = $storage->lots()->where('id','!=',$lot->id)->sum('quantity');
        $available = (float)$storage->capacity - $used;

        if ($newQty > $available) {
            return back()->withInput()
                ->withErrors(['error' => "Not enough storage space! Available: {$available}"]);
        }

        // ✅ Update lot
        $lot->update([
            'accession_id'  => $request->accession_id,
            'storage_id'    => $request->storage_id,
            'section_id'    => $request->section_id,
            'rack_id'       => $request->rack_id,
            'bin_id'        => $request->bin_id,
            'container_id'  => $request->container_id,
            //'quantity'      => $newQty,
            //'unit_id'       => $request->unit_id,
            'expiry_date'   => $request->expiry_date,
            'description'   => $request->description,
            'status'        => $request->status,
            'crop_id'       => $accession->crop_id,
        ]);

        // 🔥 IMPORTANT: DELETE OLD DATA (LOT BASED)
        SeedQuality::where('lot_id', $lot->id)->delete();
        SeedQuantity::where('lot_id', $lot->id)->delete();

        // ✅ INSERT NEW DATA
        $this->saveSeedQualities($request, $lot->id, $accession->id);
        $this->saveSeedQuantities($request, $lot->id, $accession->id , 0); // 👉 Assuming single quantity row for edit

        // ✅ Update storage usage
        $diff = $newQty - $oldQty;
        if ($diff != 0) {
            $storage->increment('current_usage', $diff);
        }

        return redirect()->route('lot-management')
            ->with('success', 'Lot updated successfully.');
    }

    private function saveSeedQualities(Request $request, $lotId, $accessionId): void
    {
        $germinations = $request->germination_percentage ?? [];

        foreach ($germinations as $i => $germination) {

            $moisture = $request->moisture_content[$i] ?? null;
            $purity   = $request->purity_percentage[$i] ?? null;

            // Skip empty row
            if (
                ($germination === null || $germination === '') &&
                ($moisture === null || $moisture === '') &&
                ($purity === null || $purity === '')
            ) {
                continue;
            }

            $researcher = $request->researcher_id[$i] ?? null;
            $resOther   = $request->researcher_other[$i] ?? null;

            if ($researcher === 'Other') {
                $researcher = null;
            } else {
                $resOther = null;
            }

            SeedQuality::create([
                'lot_id'                 => $lotId,
                'accession_id'           => $accessionId,
                'germination_percentage' => $germination,
                'moisture_content'       => $moisture,
                'purity_percentage'      => $purity,
                'viability_test_date'    => $request->viability_test_date[$i] ?? null,
                'research_date'          => $request->research_date[$i] ?? null,
                'seed_health_status'     => $request->seed_health_status[$i] ?? null,
                'researcher_id'          => $researcher,
                'researcher_other'       => $resOther,
            ]);
        }
    }

    private function saveSeedQuantities(Request $request, $lotId, $accessionId, $i): void
    {
        $qty = $request->quantity[$i] ?? null;

        if (!$qty) return;
    //dd('inside quantity', $i);
        SeedQuantity::create([
            'lot_id'           => $lotId,
            'accession_id'     => $accessionId,
            'reference_number' => $request->reference_number[$i] ?? null,
            'number_of_seeds'  => $request->number_of_seeds[$i] ?? null,
            'number_of_bags'  => $request->number_of_bags[$i] ?? null,
            'per_seed_weight'  => $request->per_seed_weight[$i] ?? null,
            'quantity'         => $qty,
            'capacity_unit_id' => $request->unit_id[$i] ?? null,
            'quantity_show'    => $request->quantity_show[$i] ?? $qty,
            'min_quantity'     => $request->min_quantity[$i] ?? null,
        ]);
    }

    // ── AJAX helpers ──────────────────────────────────────────────────────

    public function getStorageDetails($id)
    {
        $storage = Storage::with(['storageType','storageCondition','storageTime','warehouse','unit','lots'])->findOrFail($id);
        $used      = $storage->lots()->sum('quantity');
        $available = (float)$storage->capacity - $used;
        return response()->json([
            'id'                => $storage->id,
            'storage_id'        => $storage->storage_id,
            'name'              => $storage->name,
            'warehouse'         => $storage->warehouse?->name,
            'storage_type'      => $storage->storageType?->name,
            'storage_condition' => $storage->storageCondition?->name,
            'storage_time'      => $storage->storageTime?->name,
            'capacity'          => $storage->capacity,
            'current_usage'     => $used,
            'available'         => $available,
            'unit'              => $storage->unit?->name,
            'temperature'       => $storage->temperature,
            'humidity'          => $storage->humidity,
        ]);
    }

    public function getAccessionDetails($id)
    {
        $acc = Accession::with(['crop', 'warehouse','capacityUnit'])->findOrFail($id);
        $totalQty = \App\Models\SeedQuantity::where('accession_id', $id)->sum('quantity');
        $sq  = SeedQuality::where('accession_id',$id)->orderByDesc('updated_at')->first();
        return response()->json([
            'id'                     => $acc->id,
            'accession_number'       => $acc->accession_number,
            'accession_name'         => $acc->accession_name,
            'crop'                   => $acc->crop?->crop_name,
            'storage_time'           => $acc->storageTime?->name,
            'scientific_name'        => $acc->scientific_name,
            'quantity' => $totalQty, // raw value
            'quantity_show' => number_format($totalQty, 2), // formatted
            //'quantity'               => $acc->quantity,
            //'quantity_show'          => $acc->quantity_show,
            'unit'                   => $acc->capacityUnit?->name,
            'unit_id'                => $acc->capacity_unit_id,

            //'warehouse'              => $acc->warehouse?->name,
            'status' => $acc->status == 1 ? 'Active' : 'Inactive',
            'collection_date'        => $acc->collection_date?->format('d M Y'),
            'barcode'                => $acc->barcode,
            'expiry_date'            => $acc->expiry_date?->format('d M Y'),
            'biological_status'      => $acc->biological_status,
            'sample_type'            => $acc->sample_type,
            'collection_site'        => $acc->collection_site,
        ]);
    }

    public function getAccessionsByStorage($id)
    {
        $storage = Storage::findOrFail($id);

        $accessions = Accession::where('storage_time_id', $storage->storage_time_id)
            ->select('id', 'accession_number')
            ->get();

        return response()->json($accessions);
    }
    public function getQuantityDetails($lotId)
    {
        $q = SeedQuantity::with('unit')
            ->where('lot_id', $lotId)
            ->latest()
            ->first();

        if (!$q) {
            return response()->json([]);
        }

        return response()->json([
            'quantity'         => $q->quantity,
            'quantity_show'    => $q->quantity_show,
            'number_of_seeds'  => $q->number_of_seeds,
            'number_of_bags'  => $q->number_of_bags,
            'per_seed_weight'  => $q->per_seed_weight,
            'capacity_unit_id' => $q->capacity_unit_id,
            'unit' => $q->unit 
            ? $q->unit->name . ' (' . $q->unit->code . ')'
            : null,
            'quantity_updated' => $q->updated_at,
        ]);
    }

    public function getQualityDetails($lotId)
    {
        $q = SeedQuality::where('lot_id', $lotId)
            ->latest()
            ->first();

        if (!$q) {
            return response()->json([]);
        }

        return response()->json([
            'germination_percent' => $q->germination_percentage,
            'moisture_content'    => $q->moisture_content,
            'purity_percent'      => $q->purity_percentage,
            'seed_health_status'  => $q->seed_health_status,
            'viability_test_date' => $q->viability_test_date,
            'researcher' => $q->researcher?->name ?? $q->researcher_other,
            'research_date'       => $q->research_date,
        ]);
    }

    // ── Legacy Lot Master delegates ───────────────────────────────────────

    public function index()        { return app(LotMasterController::class)->index(); }
    public function store(Request $r) { return app(LotMasterController::class)->store($r); }
    public function update(Request $r, $id) {
        return app(LotMasterController::class)->update($r, \App\Models\LotMaster::findOrFail($id));
    }
    public function destroy($id) {
        return app(LotMasterController::class)->destroy(\App\Models\LotMaster::findOrFail($id));
    }

    public function interTransfer()
    {
        $storages = Storage::all();  
        $sections = \App\Models\Section::all();  
        $racks = \App\Models\Rack::all();  
            $bins       = \App\Models\Bin::where('status',1)->orderBy('name')->get();
            $containers = \App\Models\Container::where('status',1)->orderBy('name')->get();

        return view('lot-management.inter-transfer', compact('storages', 'sections', 'racks', 'bins', 'containers')
        );

    }

    public function getLotByNumber(Request $request)
    {
        $lot = \App\Models\Lot::with([
            'crop',
            'accession',
            'storage',
            'section',
            'rack',
            'bin',
            'container',
            'seedQuantities.unit'
        ])
        ->where('lot_number', $request->lot_number)
        ->first();

        if (!$lot) {
            return response()->json(['status' => false]);
        }

        $qty = $lot->seedQuantities->sum('quantity');

        return response()->json([
            'status' => true,
            'lot' => [
                'id' => $lot->id,
                'lot_number' => $lot->lot_number,
                'storage_id' => $lot->storage_id,
                'crop' => $lot->crop->crop_name ?? '',
                'accession' => $lot->accession->accession_number ?? '',
                'quantity' => $qty,
                'unit' => $lot->seedQuantities->first()->unit->name ?? '',
                'section' => $lot->section->name ?? '',
                'rack' => $lot->rack->name ?? '',
                'bin' => $lot->bin->name ?? '',
                'container' => $lot->container->name ?? '',
            ]
        ]);
    }

}
