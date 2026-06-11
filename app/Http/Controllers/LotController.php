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
use App\Models\Crop;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\Container;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LotExport;
use App\Models\Dispatch;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;


class LotController extends Controller
{
    private function formData(): array
    {
        return [
            'accessions' => Accession::orderBy('accession_number')->get(['id','accession_number','accession_name']),
            'storages'   => Storage::where('status', 1)->orderBy('name')->get(['id','storage_id','name','warehouse_id']),
            'units'      => Unit::where('status',1)->orderBy('name')->get(['id','name','code']),
            'crops'      => Crop::where('update_status', 1)->orderBy('crop_name')->get(['id','crop_name']),
            'sections'   => \App\Models\Section::where('status',1)->orderBy('name')->get(['id','name','storage_id']),
            'racks'      => Rack::where('status',1)->orderBy('name')->get(['id','name','storage_id','warehouse_id']),
            'bins'       => Bin::where('status',1)->orderBy('name')->get(['id','name','rack_id']),
            'containers' => Container::where('status',1)->orderBy('name')->get(['id','name']),
            'warehouses' => Warehouse::where('status',1)->orderBy('name')->get(['id','name']),
            'users'      => User::orderBy('name')->get(['id','name']),
            'dispatches' => Dispatch::with(['request', 'accession', 'itn'])->latest()->paginate(10), 
            'seedQuantities' => SeedQuantity::orderBy('id')->get(['id','number_of_seeds', 'number_of_bags','per_seed_weight','quantity','capacity_unit_id','quantity_show', 'reference_number','min_quantity','in_seed','out_seed','return_seed']),

        ];
    }

    // ── List ──────────────────────────────────────────────────────────────

    public function managementIndex()
    {
        $accessions = Accession::with([
            'crop.season'
        ])->get();    
        $lots = Lot::with(['accession','storage', 'lotType', 'seedQuantities', 'seedQuantities.unit'])
                   ->whereNotNull('lot_number')
                   ->orderBy('created_at','desc')
                   ->paginate(15);
        
        return view('lot-management.index', array_merge($this->formData(), [
            'lots'      => $lots,
            'accessions' => $accessions,
           
        ]));
    }

    // ── Create ────────────────────────────────────────────────────────────

    public function managementCreate()
    {
        $accessions = Accession::with([
            'crop.season'
        ])->get();    
        $lastRef = Lot::latest('id')->value('reference_number'); 
        return view('lot-management.create', array_merge($this->formData(), [
            'nextLotNo' => Lot::generateLotNumber(
                'REF', 'REJUV', 'PFX', 'SMPID', 0
            ),
            'lot'       => null,
            'lastRef' => $lastRef,
            'accessions' => $accessions,
        ]));
    }
    

    public function managementStore(Request $request)
    {

        \Illuminate\Support\Facades\Log::info('LOT STORE DEBUG', [
            'arrival_type'         => $request->arrival_type,
            'rejuvenation_program' => $request->rejuvenation_program,
            'rff_lot_number'       => $request->rff_lot_number,
        ]);
        // Validation — reference_number only required/unique for Rejuvenation
        $refRules = $request->arrival_type === 'Rejuvenation'
            ? ['required', 'distinct', Rule::unique('lots', 'reference_number')]
            : ['nullable', 'string', 'max:255'];


        $request->validate([
            'arrival_type'       => 'required|string',
            'accession_id'       => 'required|exists:accessions,id',
            'storage_id'         => 'required|exists:storages,id',
            'reference_number'   => 'nullable|array',
            'reference_number.*' => $refRules,
            'number_of_seeds'    => 'nullable|array',
            'number_of_seeds.*'  => 'nullable|numeric|min:0',
            'number_of_bags'     => 'nullable|array',
            'number_of_bags.*'   => 'nullable|numeric|min:0',
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
            'status'             => 'required',
        ]);

        $storage   = Storage::findOrFail($request->storage_id);
        $accession = Accession::findOrFail($request->accession_id);

        // Sum quantity from seed_quantities (lots table has no quantity column)
        $used      = SeedQuantity::whereIn('lot_id', $storage->lots()->pluck('id'))->sum('quantity');
        $available = (float)$storage->capacity - $used;

        $qtys = array_values($request->quantity ?? []);
        $refs = array_values($request->reference_number ?? []);

        $totalQty = array_sum($qtys);

        if ($totalQty > $available) {
            return back()->withInput()
                ->withErrors(['error' => "Not enough storage space! Available: {$available}"]);
        }

        DB::transaction(function () use ($request, $storage, $accession, $qtys, $refs, $totalQty) {

            $runningSeq = null;
            $sampleId   = $request->sample_id ?: $accession->sample_id;

            foreach ($qtys as $i => $qty) {

                if (!$qty) continue;

                $reference   = $refs[$i] ?? null;
                $arrivalType = $request->arrival_type;
                $rowNum      = $i + 1;

                switch ($arrivalType) {
                    case 'Rejuvenation':
                        $middleSegment = "{$request->rejuvenation_program}/{$rowNum}-{$request->prefix}";
                        $base          = "{$reference}-{$request->rejuvenation_program}-{$request->prefix}-{$sampleId}-";
                        break;
                    case 'Accession Arrival':
                        $middleSegment = "AccA/{$rowNum}";
                        $base          = "AccA-{$sampleId}-";
                        break;
                    case 'Return From Field':
                        $middleSegment = "RTN/{$rowNum}";
                        $base          = "RTN-{$sampleId}-";
                        break;
                    default:
                        $middleSegment = "{$rowNum}";
                        $base          = "{$sampleId}-";
                }

                // Fetch running sequence once per batch
                if ($runningSeq === null) {
                    $last = Lot::where('lot_number', 'like', $base . '%')
                        ->orderByRaw("CAST(SUBSTRING_INDEX(lot_number, '-', -1) AS UNSIGNED) DESC")
                        ->first();

                    $runningSeq = ($last && preg_match('/-(\d{2})$/', $last->lot_number, $m))
                        ? (int)$m[1]
                        : 0;
                }

                $runningSeq++;
                $seq = str_pad($runningSeq, 2, '0', STR_PAD_LEFT);

                // Build lot number per type
                switch ($arrivalType) {
                    case 'Rejuvenation':
                        $lotNumber = "{$reference}-{$request->rejuvenation_program}/{$rowNum}-{$request->prefix}-{$sampleId}-{$seq}";
                        break;
                    case 'Accession Arrival':
                        $lotNumber = "{$reference}-AccA/{$rowNum}-{$sampleId}-{$seq}";
                        break;
                    case 'Return From Field':
                        $lotNumber = "{$reference}-{$request->rejuvenation_program}/{$rowNum}-{$request->prefix}-{$sampleId}-{$seq}-RF";
                        break;
                    default:
                        $lotNumber = "{$reference}-{$rowNum}-{$sampleId}-{$seq}";
                }

                $newLot = Lot::create([
                    'lot_number'           => $lotNumber,
                    'arrival_type'         => $arrivalType,
                    'dispatch_id'           =>$request->dispatch_id,
                    'reference_number'     => $reference ?: null,
                    'rejuvenation_program' => in_array($arrivalType, ['Rejuvenation', 'Return From Field'])
                                                ? $request->rejuvenation_program
                                                : null,
                    'prefix'               => in_array($arrivalType, ['Rejuvenation', 'Return From Field'])
                                        ? $request->prefix
                                        : null,
                    'sample_id'            => $sampleId,
                    'accession_id'         => $request->accession_id,
                    'storage_id'           => $request->storage_id,
                    'rack_id'              => $request->rack_id,
                    'bin_id'               => $request->bin_id,
                    'container_id'         => $request->container_id,
                    'unit_id'              => $request->unit_id[$i] ?? null,

                     // store accession dates
                    'expiry_date'       => $request->expiry_date,
                    'regeneration_date' => $request->recheck_date,
                    'regen_year'        => $request->regen_year,
                    'description'          => $request->description,
                    'status'               => $request->status,
                    'crop_id'              => $accession->crop_id,

                ]);

                if (!$newLot->id) {
                    throw new \Exception("Lot creation failed for row {$rowNum}");
                }

                // Save seed quantities for this specific row index
                $this->saveSeedQuantities($request, $newLot->id, $accession->id, $i);

                // Save seed qualities — only the row matching this lot's index
                $this->saveSeedQualitiesForRow($request, $newLot->id, $accession->id, $i);
            }

            // Recalculate storage current_usage from seed_quantities
            $totalUsed = SeedQuantity::whereIn('lot_id', $storage->lots()->pluck('id'))->sum('quantity');
            $storage->update(['current_usage' => $totalUsed]);
        });

        return redirect()->route('lot-management')
            ->with('success', 'Lots created successfully.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────

    public function managementEdit($id)
    {
        $lot = Lot::with(['accession','storage','seedQualities', 'seedQuantities'])->findOrFail($id);
        $lastRef = \App\Models\SeedQuantity::latest('id')->value('reference_number');
        $dispatches = Dispatch::with(['request', 'lot'])
                    ->orderBy('id', 'desc')
                    ->get();
        return view('lot-management.create', array_merge($this->formData(), [
            'nextLotNo' => $lot->lot_number,
            'lot'       => $lot,
            'lastRef'   => $lastRef,
            'dispatches'=> $dispatches,
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

        $storage   = Storage::findOrFail($request->storage_id);
        $accession = Accession::findOrFail($request->accession_id);

        $oldQty = SeedQuantity::where('lot_id', $lot->id)->sum('quantity');
        $newQty = array_sum($request->quantity);

        // Sum from seed_quantities excluding current lot
        $used      = SeedQuantity::whereIn('lot_id',
            $storage->lots()->where('id', '!=', $lot->id)->pluck('id')
        )->sum('quantity');
        $available = (float)$storage->capacity - $used;

        if ($newQty > $available) {
            return back()->withInput()
                ->withErrors(['error' => "Not enough storage space! Available: {$available}"]);
        }

        // ✅ Update lot
        $lot->update([
            'accession_id'  => $request->accession_id,
            'storage_id'    => $request->storage_id,
            'rack_id'       => $request->rack_id,
            'bin_id'        => $request->bin_id,
            'container_id'  => $request->container_id,
            //'quantity'      => $newQty,
            //'unit_id'       => $request->unit_id,
            'expiry_date'   => $request->expiry_date,
            'recheck_date' => $request->regeneration_date,
            'regen_year'        => $request->regen_year,
            'description'   => $request->description,
            'status'        => $request->status,
            'crop_id'       => $accession->crop_id,
            // ADD THESE
            'arrival_type'        => $request->arrival_type,
            'dispatch_id'         => $request->dispatch_id,
            'request_id'          => $request->request_id,
            'rejuvenation_program'=> $request->rejuvenation_program,
        ]);

        // 🔥 IMPORTANT: DELETE OLD DATA (LOT BASED)
        SeedQuality::where('lot_id', $lot->id)->delete();
        SeedQuantity::where('lot_id', $lot->id)->delete();

        // ✅ INSERT NEW DATA — loop all quantity rows
        $this->saveSeedQualities($request, $lot->id, $accession->id);

        $qtys = $request->quantity ?? [];
        foreach ($request->quantity as $i => $qty) {
            
            $this->saveSeedQuantities($request, $lot->id, $accession->id, $i);
        }

        // Recalculate storage current_usage from seed_quantities
        $totalUsed = SeedQuantity::whereIn('lot_id', $storage->lots()->pluck('id'))->sum('quantity');
        $storage->update(['current_usage' => $totalUsed]);

        return redirect()->route('lot-management')
            ->with('success', 'Lot updated successfully.');
    }

    private function saveSeedQualities(Request $request, $lotId, $accessionId): void
    {
        $germinations = $request->germination_percentage ?? [];

        foreach ($germinations as $i => $germination) {
            $this->saveSeedQualitiesForRow($request, $lotId, $accessionId, $i);
        }
    }

    private function saveSeedQualitiesForRow(Request $request, $lotId, $accessionId, $i): void
    
{
    $germination = $request->germination_percentage[$i] ?? null;
    $moisture    = $request->moisture_content[$i] ?? null;
    $purity      = $request->purity_percentage[$i] ?? null;
    $chlorophyll = $request->chlorophyll_percentage[$i] ?? null;
    $waterLevel  = $request->water_level_percentage[$i] ?? null;

    if (
        ($germination === null || $germination === '') &&
        ($moisture === null || $moisture === '') &&
        ($purity === null || $purity === '') &&
        ($chlorophyll === null || $chlorophyll === '') &&
        ($waterLevel === null || $waterLevel === '')
    ) {
        return;
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
        'chlorophyll_percentage' => $chlorophyll,
        'water_level_percentage' => $waterLevel,
        'viability_test_date'    => $request->viability_test_date[$i] ?? null,
        'research_date'          => $request->research_date[$i] ?? null,
        'seed_health_status'     => $request->seed_health_status[$i] ?? null,
        'researcher_id'          => $researcher,
        'researcher_other'       => $resOther,
    ]);
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
        $used      = SeedQuantity::whereIn('lot_id', $storage->lots->pluck('id'))->sum('quantity');
        $available = (float)$storage->capacity - $used;

        // Unit conversion map to grams for filtering
        $unitCode = strtolower($storage->unit?->code ?? '');
        $capacityInGrams = match($unitCode) {
            'kg'  => (float)$storage->capacity * 1000,
            'mg'  => (float)$storage->capacity / 1000,
            'ton' => (float)$storage->capacity * 1000000,
            default => (float)$storage->capacity, // assume grams
        };

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
            'unit_id'           => $storage->unit_id,
            'unit_code'         => $unitCode,
            'capacity_in_grams' => $capacityInGrams,
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
            'sample_id'              => $acc->sample_id,
            'accession_number'       => $acc->accession_number,
            'accession_name'         => $acc->accession_name,
            'crop'                   => $acc->crop?->crop_name,
            'storage_time'           => $acc->storageTime?->name,
            'scientific_name'        => $acc->crop?->scientific_name,
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
            'recheck_date'           => $acc->recheck_date?->format('d M Y'),
            'regen_year'              => $acc->crop?->regeneration_cut_year,
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
        $rows = SeedQuantity::with('unit')
            ->where('lot_id', $lotId)
            ->orderBy('id')
            ->get()
            ->map(fn($q) => [
                'reference_number' => $q->reference_number,
                'number_of_seeds'  => $q->number_of_seeds,
                'number_of_bags'   => $q->number_of_bags,
                'per_seed_weight'  => $q->per_seed_weight,
                'quantity'         => $q->quantity,
                'quantity_show'    => $q->quantity_show,
                'min_quantity'     => $q->min_quantity,
                'unit'             => $q->unit ? $q->unit->name . ' (' . $q->unit->code . ')' : null,
                'quantity_updated' => $q->updated_at,
            ]);

        return response()->json($rows);
    }

    public function getQualityDetails($lotId)
    {
        $rows = SeedQuality::where('lot_id', $lotId)
            ->orderBy('id')
            ->get()
            ->map(fn($q) => [
                'germination_percent' => $q->germination_percentage,
                'moisture_content'    => $q->moisture_content,
                'purity_percent'      => $q->purity_percentage,
                'seed_health_status'  => $q->seed_health_status,
                'viability_test_date' => $q->viability_test_date,
                'researcher'          => $q->researcher?->name ?? $q->researcher_other,
                'research_date'       => $q->research_date,
            ]);

        return response()->json($rows);
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

    public function export()
    {
        return Excel::download(new LotExport, 'lot.xlsx');
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

    public function getQuality($id)
    {
        return response()->json(
            SeedQuality::where('lot_id', $id)
                ->latest()   // optional
                ->get()      // ✅ MUST be get()
        );
    }

    public function qualityControl()
    {
        $crops      = Crop::where('update_status', 1)->orderBy('crop_name')->get(['id','crop_name']);
        $accessions = Accession::orderBy('accession_number')->get(['id','accession_number']);
        $storages   = Storage::orderBy('name')->get(['id','name','storage_id']);
        $users      = User::orderBy('name')->get(['id','name']);

        return view('lot-management.quality-control', compact('crops', 'accessions', 'storages', 'users'));
    }
    
    public function qualityHistoryControl()
    {
        $qualityHistories = SeedQuality::with([
            'lot',
            'researcher'
        ])
        ->latest()
        ->paginate(10);

        return view(
            'lot-management.quality-history-control',
            compact('qualityHistories')
        );
    }

    /**
     * Return all seed quality rows for a given lot (AJAX).
     */
    public function getLotQualities($lotId)
    {
        $lot = Lot::with(['accession','storage','seedQuantities.unit','seedQualities.researcher'])->findOrFail($lotId);

        $qualities = $lot->seedQualities->map(fn($q) => [
            'id'                     => $q->id,
            'germination_percentage' => $q->germination_percentage,
            'moisture_content'       => $q->moisture_content,
            'purity_percentage'      => $q->purity_percentage,
            'chlorophyll_percentage' => $q->chlorophyll_percentage,
            'water_level_percentage' => $q->water_level_percentage,
            'viability_test_date'    => $q->viability_test_date,
            'seed_health_status'     => $q->seed_health_status,
            'researcher_id'          => $q->researcher_id,
            'researcher_name'        => $q->researcher?->name,
            'researcher_other'       => $q->researcher_other,
            'research_date'          => $q->research_date,
            'created_at'            => $q->created_at,
            'updated_at'            => $q->updated_at,
        ]);

        $qty = $lot->seedQuantities->first();

        return response()->json([
            'lot' => [
                'id'             => $lot->id,
                'lot_number'     => $lot->lot_number,
                'accession'      => $lot->accession?->accession_number,
                'accession_name' => $lot->accession?->accession_name,
                'crop'           => $lot->accession?->crop?->crop_name ?? $lot->crop?->crop_name,
                'storage'        => $lot->storage?->name,
                'quantity'       => $qty?->quantity,
                'quantity_show'  => $qty?->quantity_show,
                'unit'           => $qty?->unit?->name,
                'rack'           => $lot->rack?->name,
                'bin'            => $lot->bin?->name,
                'container'      => $lot->container?->name,
                'expiry_date'    => $lot->accession?->expiry_date?->format('d M Y'),
            ],
            'qualities' => $qualities,
        ]);
    }

    /**
     * Save / replace seed quality rows for a lot.
     */
    public function qualityControlSave(Request $request, $lotId)
    {
        $lot = Lot::findOrFail($lotId);

        $request->validate([
            'germination_percentage'   => 'nullable|array',
            'germination_percentage.*' => 'nullable|numeric|min:0|max:100',
            'moisture_content'         => 'nullable|array',
            'moisture_content.*'       => 'nullable|numeric|min:0|max:100',
            'purity_percentage'        => 'nullable|array',
            'purity_percentage.*'      => 'nullable|numeric|min:0|max:100',
            'chlorophyll_percentage'   => 'nullable|array',
            'chlorophyll_percentage.*' => 'nullable|numeric|min:0|max:100',
            'water_level_percentage'   => 'nullable|array',
            'water_level_percentage.*' => 'nullable|numeric|min:0|max:100',
            'viability_test_date'      => 'nullable|array',
            'viability_test_date.*'    => 'nullable|date',
            'research_date'            => 'nullable|array',
            'research_date.*'          => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $lot) {
            // Only INSERT new rows — existing records are read-only and not submitted
            $germinations = $request->germination_percentage ?? [];

            foreach ($germinations as $i => $germination) {
                $moisture    = $request->moisture_content[$i] ?? null;
                $purity      = $request->purity_percentage[$i] ?? null;
                $chlorophyll = $request->chlorophyll_percentage[$i] ?? null;
                $waterLevel  = $request->water_level_percentage[$i] ?? null;

                // Skip completely empty rows
                if (
                    ($germination === null || $germination === '') &&
                    ($moisture    === null || $moisture    === '') &&
                    ($purity      === null || $purity      === '') &&
                    ($chlorophyll === null || $chlorophyll === '') &&
                    ($waterLevel  === null || $waterLevel  === '')
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
                    'lot_id'                 => $lot->id,
                    'accession_id'           => $lot->accession_id,
                    'germination_percentage' => $germination ?: null,
                    'moisture_content'       => $moisture ?: null,
                    'purity_percentage'      => $purity ?: null,
                    'chlorophyll_percentage' => $chlorophyll ?: null,
                    'water_level_percentage' => $waterLevel ?: null,
                    'viability_test_date'    => $request->viability_test_date[$i] ?? null,
                    'research_date'          => $request->research_date[$i] ?? null,
                    'seed_health_status'     => $request->seed_health_status[$i] ?? null,
                    'researcher_id'          => $researcher,
                    'researcher_other'       => $resOther,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Seed quality saved successfully.']);
    }

   public function getLotDetails(Request $request)
    {
        $lotNumber = $request->lot_number;

        $lot = Lot::where('lot_number', $lotNumber)->first();

        if (!$lot) {
            return response()->json([
                'status' => false
            ]);
        }

        return response()->json([
            'status' => true,
            'lot' => [
                'lot_number'            => $lot->lot_number,
                'rejuvenation_program' => $lot->rejuvenation_program,
                'prefix'               => $lot->prefix,
                'sample_id'            => $lot->sample_id,
            ]
        ]);
    }

    public function dispose(Request $request, $id)
    {
        $request->validate([

            'dispose_date'   => 'required|date',
            'dispose_type'   => 'required',
            'dispose_reason' => 'required',

        ]);

        $lot = Lot::findOrFail($id);

        $lot->update([

            'status'          => 'Disposed',
            'dispose_date'    => $request->dispose_date,
            'dispose_type'    => $request->dispose_type,
            'dispose_reason'  => $request->dispose_reason,

        ]);

        return back()->with(
            'success',
            'Lot disposed successfully.'
        );
    }
    public function publicShow($id)
    {
        $lot = Lot::with([
            'accession.crop',
            'storage',
            'section',
            'rack',
            'bin',
            'container',
            'seedQuantities.unit',
        ])->findOrFail($id);

        return view('lot-management.public_show', compact('lot'));
    }

    public function qrprintAll()
    {
        $lots = Lot::with([
            'accession.crop',
            'storage',
            'lotType'
        ])
        ->whereNotNull('lot_number')
        ->orderBy('lot_number')
        ->get();

        return view('lot-management.qrprint-all', compact('lots'));
    }


}
