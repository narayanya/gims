<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\OldDispatchData;
use App\Models\SeedRequest;
use App\Models\Itn;
use App\Models\Lot;
use Illuminate\Http\Request;
use App\Models\RequestTransaction;
use App\Imports\OldDispatchDataImport;
use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\alert;

class DispatchController extends Controller
{
    // ✅ List all approved requests (ready for dispatch)
    public function index()
    {
        $dispatches = Dispatch::with(['request', 'accession', 'itn'])->latest()->paginate(10);   
        $requests = SeedRequest::with(['user','crop','unit','accession.lots'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(10);
        $itns = \App\Models\Itn::with([
            'lot',
            'crop',
            'accession',
            'fromWarehouse',
            'toWarehouse'
        ])->latest()->paginate(10);

        return view('dispatch-management.index', compact('requests', 'dispatches' , 'itns'));
    }

    public function store(Request $req, $id)
    {
        $req->validate([
            'lot_id'          => 'required|exists:lots,id',
            'dispatched_at'   => 'nullable|date',
            'mrn_number'      => 'nullable|string|max:100',
            'courier_name'    => 'nullable|string|max:255',
            'contact_person'  => 'nullable|string|max:255',
            'contact_number'  => 'nullable|string|max:50',
            'tracking_number' => 'nullable|string|max:255',
            'quantity'        => 'nullable|numeric|min:0.01',
            'remarks'         => 'nullable|string',
        ]);

        $request = SeedRequest::findOrFail($id);

        $dispatch = Dispatch::create([
            'dispatch_number' => Dispatch::generateDispatchNumber(),
            'request_id'      => $request->id,
            'accession_id'    => $request->accession_id,
            'lot_id'          => $req->lot_id, // ✅ FIXED
            'mrn_number'      => $req->mrn_number ?: 'MRN-' . now()->format('YmdHis'),
            'quantity'        => $req->quantity ?? $request->quantity,
            'courier_name'    => $req->courier_name,
            'contact_person'  => $req->contact_person,
            'contact_number'  => $req->contact_number,
            'tracking_number' => $req->tracking_number,
            'remarks'         => $req->remarks,
            'dispatched_at'   => $req->dispatched_at ?? now(),
        ]);

        $request->update(['status' => 'dispatched']);

        // Deduct dispatched quantity from seed_quantities

        $sqQuery = \App\Models\SeedQuantity::query();

        // If lot based dispatch
        if ($req->lot_id) {

            $sqQuery->where('lot_id', $req->lot_id);

        } else {

            // accession level quantity
            $sqQuery->where('accession_id', $request->accession_id)
                    ->whereNull('lot_id');
        }

        $sq = $sqQuery->latest()->first();

        if ($sq) {

            $dispatchQty = (float) $dispatch->quantity;

            // Current values
            $currentQty      = (float) $sq->quantity;
            $currentShowQty  = (float) $sq->quantity_show;

             // OLD LOT STOCK VALUES
            $oldQty = (float) $sq->quantity;

            $oldShowQty = (float) $sq->quantity_show;

            // Deduct from actual quantity
            $sq->quantity = max(0, $currentQty - $dispatchQty);

            // Deduct from visible quantity
            $sq->quantity_show = max(0, $currentShowQty - $dispatchQty);

            $sq->save();

            // STORE HISTORY
            RequestTransaction::create([

                'request_id' => $request->id,
                'dispatch_id' => $dispatch->id,
                'accession_id' => $request->accession_id,
                'lot_id' => $req->lot_id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'transaction_type' => 'dispatch',
                'quantity' => $dispatchQty,
                // OLD STOCK
                'old_quantity' => $oldQty,
                'old_quantity_show' => $oldShowQty,

                // NEW STOCK
                'new_quantity' => $sq->quantity,
                'new_quantity_show' => $sq->quantity_show,
                'unit_id' => $request->unit_id,
                'reference_no' => $dispatch->dispatch_number,
                'remarks' => $req->remarks,
            ]);
        }

        return redirect()->route('dispatch.print', $dispatch->id);
    }

    public function itnStore(Request $req, $id)
    {   
        $req->validate([
            'dispatched_at'   => 'nullable|date',
            'mrn_number'      => 'nullable|string|max:100',
            'courier_name'    => 'nullable|string|max:255',
            'contact_person'  => 'nullable|string|max:255',
            'contact_number'  => 'nullable|string|max:50',
            'tracking_number' => 'nullable|string|max:255',
            'remarks'         => 'nullable|string',
        ]);

        $itn = Itn::findOrFail($id);

        // Total quantity across all lots in the batch
        $totalQty = \App\Models\WarehouseTransfer::where('batch_id', $itn->batch_id)->sum('quantity');

        $dispatch = Dispatch::create([
            'dispatch_number' => Dispatch::generateDispatchNumber(),
            'itn_id'          => $itn->id,
            'batch_id'        => $itn->batch_id,
            'mrn_number'      => $req->mrn_number ?: 'MRN-' . now()->format('YmdHis'),
            'quantity'        => $totalQty ?: $itn->quantity,
            'courier_name'    => $req->courier_name,
            'contact_person'  => $req->contact_person,
            'contact_number'  => $req->contact_number,
            'tracking_number' => $req->tracking_number,
            'remarks'         => $req->remarks,
            'dispatched_at'   => $req->dispatched_at ?? now(),
        ]);

        $itn->update(['status' => 'dispatched']);

        return redirect()->route('dispatch-management.index')
            ->with('success', 'MRN ' . $dispatch->mrn_number . ' generated — dispatch recorded successfully.');
    }

    // ✅ Mark as dispatched
    public function dispatch($id)
    {
        $request = SeedRequest::findOrFail($id);

        $request->status = 'dispatched';
        $request->dispatched_at = now(); // optional column
        $request->save();

        return redirect()->back()->with('success', 'Dispatched successfully');
    }
    // 👉 Show dispatch form
    public function show($id)
    {
        $request = SeedRequest::with([
            'crop', 'unit', 'user.reportingUser', 'approvedBy',
            'accession.capacityUnit',
        ])->findOrFail($id);

        // Load lots by accession_id
        $lots = collect();
        $seedQuantities = collect();
        if ($request->accession_id) {
            $lots = \App\Models\Lot::with(['storage', 'unit'])
                ->where('accession_id', $request->accession_id)
                ->whereNotNull('lot_number')
                ->get();

            // Group seed quantities by lot_id (null lot_id goes to key '')
            $seedQuantities = \App\Models\SeedQuantity::with('unit')
                ->where('accession_id', $request->accession_id)
                ->get()
                ->groupBy(fn($sq) => $sq->lot_id ?? 'unlinked');
        }

        return view('dispatch-management.show', compact('request', 'lots', 'seedQuantities'));
    }

    public function showITN($id)
    {
        $itn = Itn::with([
            'transfer',
            'fromWarehouse',
            'toWarehouse',
            'fromStorage',
            'toStorage'
        ])->findOrFail($id);

        // Load all lots in this batch
        $batchLots = \App\Models\WarehouseTransfer::with(['lot.crop', 'lot.accession', 'lot.storage'])
            ->where('batch_id', $itn->batch_id)
            ->get();

        $lots = $batchLots; // alias for view compatibility
        $seedQuantities = collect();

        return view('dispatch-management.itn-dispatch', compact('itn', 'lots', 'batchLots', 'seedQuantities'));
    }
    

    public function print($id)
    {
        $dispatch = Dispatch::with([
            'request.crop',
            'request.unit',
            'request.user',
            'request.approvedBy',
            'itn.fromWarehouse',
            'itn.toWarehouse',
            'itn.fromStorage',
            'itn.toStorage',
            'accession'
        ])->findOrFail($id);

        return view('dispatch-management.print', compact('dispatch'));
    }

    public function dispatchReport(Request $request)
    {
        $query = Dispatch::with([
            'request',
            'accession',
            'lot'
        ]);

        // Date Filter
        if ($request->filled('date_from')) {
            $query->whereDate('dispatched_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('dispatched_at', '<=', $request->date_to);
        }

        // Latest First
        $dispatches = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('report.dispatch_report', compact('dispatches'));
    }

    public function oldDispatches(Request $request)
    { 
    
    $query = OldDispatchData::query();
    $chartData = DB::table('old_dispatch_data')
    ->selectRaw("
        YEAR(request_date) as year,

        SUM(
            CASE
                WHEN seed_weight REGEXP '^[0-9]+(\\.[0-9]+)?$'
                THEN CAST(seed_weight AS DECIMAL(10,3))
                ELSE 0
            END
        ) as total_weight,

        SUM(
            CASE
                WHEN seed_weight REGEXP '[0-9]+ Seed'
                THEN CAST(
                    REPLACE(seed_weight,' Seed','')
                    AS UNSIGNED
                )
                ELSE 0
            END
        ) as total_seed_count
    ")
    ->whereNotNull('request_date')
    ->whereYear('request_date', '!=', 2024)
    ->groupByRaw('YEAR(request_date)')
    ->orderByRaw('YEAR(request_date)')
    ->get();
    //dd($chartData);
    $years = $chartData->pluck('year');
    $weights = $chartData->pluck('total_weight');
    $seeds = $chartData->pluck('total_seed_count');

        // Date range filter on dispatch_date
        if ($request->filled('from_date')) {
            $query->whereDate('dispatch_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('dispatch_date', '<=', $request->to_date);
        }

        // Keyword search across key text columns
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('crop',             'like', "%{$term}%")
                  ->orWhere('sample_id',      'like', "%{$term}%")
                  ->orWhere('concerned_person','like', "%{$term}%")
                  ->orWhere('location',       'like', "%{$term}%")
                  ->orWhere('tracking_id',    'like', "%{$term}%")
                  ->orWhere('courier_service','like', "%{$term}%")
                  ->orWhere('remarks',        'like', "%{$term}%")
                  ->orWhere('prefix',         'like', "%{$term}%");
            });
        }

        $dispatches = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('dispatch-management.dispatch-list', compact('dispatches' , 'years', 'weights', 'seeds'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        $importer = new OldDispatchDataImport();
        Excel::import($importer, $request->file('file'));

        $inserted = $importer->getInserted();
        $skipped  = $importer->getSkipped();

        $msg = "Import complete — {$inserted} record(s) inserted.";

        if (!empty($skipped)) {
            $lines   = implode(', ', array_map(fn($s) => "Row {$s['row']}: {$s['reason']}", $skipped));
            return redirect()->back()
                ->with('success', $msg)
                ->with('import_skipped', $lines);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function template()
    {
        return response()->download(public_path('templates/old-dispatch-template.csv'));
    }
    
}