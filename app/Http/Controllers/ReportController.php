<?php

namespace App\Http\Controllers;
use App\Models\SeedRequest;
use App\Models\SeedQuantity;
use App\Models\Dispatch;
use App\Models\Accession;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use App\Models\LotTransfer;
use App\Models\Lot;
use App\Models\RequestTransaction;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::now();  
        $lots = LotTransfer::all()->count();
        $totalRequested  = SeedRequest::where('status', 'pending')->sum('quantity') ?? 0;
        $totalAvailable  = SeedQuantity::sum('quantity') ?? 0;
        $totalDispatched = Dispatch::sum('quantity') ?? 0;
        $totalLotQty     = \App\Models\SeedQuantity::sum('quantity') ?? 0; // total across all lots
        

        $days = collect();
        $latestDate = DB::table('dispatches')->max('dispatched_at');
        $startDate = $latestDate
            ? Carbon::parse($latestDate)->startOfDay()->subDays(14)
            : Carbon::today()->subDays(14);

        for ($i = 0; $i < 15; $i++) {
            $date = $startDate->copy()->addDays($i);

            $days->push([
                'date' => $date->format('d M'),

                'arrival' => (float) DB::table('seed_quantities')
                    ->whereDate('created_at', $date)
                    ->sum('quantity'),

                'dispatch' => (float) DB::table('dispatches')
                    ->whereDate('dispatched_at', $date)
                    ->sum('quantity'),

                'request' => (float) DB::table('requests')
                    ->whereDate('request_date', $date)
                    ->sum('quantity'),
            ]);
        }
       
        return view('report.reports', compact('today', 'totalRequested', 'totalAvailable', 'totalDispatched', 'lots', 'totalLotQty'))->with('chartData', $days);
    }

    public function summary()
    {
        $crops = DB::table('core_crop')->where('update_status', '=', 1)->orderBy('crop_name')->get();
        $accessions = Accession::with('crop')->get();
        $lots = DB::table('lots')->get();
        $quantityRequestRecord = DB::table('request_transactions')

        ->leftJoin('accessions', 'request_transactions.accession_id', '=', 'accessions.id')

        ->leftJoin('lots', 'request_transactions.lot_id', '=', 'lots.id')

        ->leftJoin('core_crop', 'accessions.crop_id', '=', 'core_crop.id')
        ->leftJoin('units', 'request_transactions.unit_id', '=', 'units.id')
        ->leftJoin('users', 'request_transactions.created_by', '=', 'users.id')

        ->select(
            'request_transactions.*',
            'accessions.accession_number',
            'lots.lot_number',
            'core_crop.crop_name',
            'units.name',
            'users.name as user_name'
        )

        ->latest('request_transactions.id')

        ->get();
        $summary = DB::table('accessions')
            ->join('core_crop', 'accessions.crop_id', '=', 'core_crop.id')
            ->leftJoin('lots', 'lots.accession_id', '=', 'accessions.id')
            ->leftJoinSub(
            DB::table('seed_quantities')
                ->select(
                    'lot_id',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(quantity_show) as total_quantity_show')
                )
                ->groupBy('lot_id'),
            'sq',
            function ($join) {
                $join->on('sq.lot_id', '=', 'lots.id');
            }
        )

        ->leftJoinSub(
            DB::table('requests')
                ->select(
                    'accession_id',
                    DB::raw('SUM(quantity) as total_requested')
                )
                ->groupBy('accession_id'),
            'rq',
            function ($join) {
                $join->on('rq.accession_id', '=', 'accessions.id');
            }
        )

        ->leftJoinSub(
            DB::table('dispatches')
                ->select(
                    'accession_id',
                    DB::raw('SUM(quantity) as total_dispatched')
                )
                ->groupBy('accession_id'),
            'dp',
            function ($join) {
                $join->on('dp.accession_id', '=', 'accessions.id');
            }
        )

            ->select(
                'core_crop.id as crop_id', // ✅ IMPORTANT (needed for details)
                'core_crop.crop_name',

                DB::raw('COUNT(DISTINCT accessions.id) as total_accessions'),
                DB::raw('COUNT(DISTINCT lots.id) as total_lots'),
                DB::raw('COALESCE(SUM(sq.total_quantity),0) as lot_quantity'),

                DB::raw('COALESCE(SUM(sq.total_quantity),0) as total_quantity'),

                DB::raw('COALESCE(SUM(sq.total_quantity_show),0) as total_quantity_show'),

                DB::raw('COALESCE(SUM(DISTINCT rq.total_requested),0) as total_requested'),
                DB::raw('COALESCE(SUM(DISTINCT dp.total_dispatched),0) as total_dispatched')
            )

            ->groupBy('core_crop.id', 'core_crop.crop_name')
            ->orderBy('core_crop.crop_name')
            ->get();

        // ✅ Attach details for collapse
        foreach ($summary as $row) {

            $row->details = DB::table('accessions')
                ->leftJoin('lots', 'lots.accession_id', '=', 'accessions.id')
                ->leftJoin('seed_quantities', 'seed_quantities.lot_id', '=', 'lots.id')
                ->where('accessions.crop_id', $row->crop_id)
                ->select(
                    'accessions.id as accession_id',
                    'accessions.accession_number',
                    'lots.id as lot_id',
                    'lots.lot_number',
                    DB::raw('COALESCE(seed_quantities.quantity,0) as quantity'),
                    DB::raw('COALESCE(seed_quantities.quantity_show,0) as quantity_show')
                )
                ->orderBy('accessions.accession_number')
                ->get();
        }

        return view('report.summary', compact('summary', 'crops', 'accessions', 'lots', 'quantityRequestRecord'));
    }

    public function accessionHistory($id)
    {
        $accession = \App\Models\Accession::with([
            'crop', 'country', 'state', 'district', 'city',
            'storageTime', 'capacityUnit', 'images',
        ])->findOrFail($id);

        // All lots for this accession
        $lots = \App\Models\Lot::with(['storage', 'section', 'rack', 'bin', 'container', 'seedQuantities.unit'])
            ->where('accession_id', $id)
            ->orderBy('created_at')
            ->get();

        // Seed quantities history
        $seedQuantities = \App\Models\SeedQuantity::where('accession_id', $id)
            ->orderBy('created_at')
            ->get();

        // Lot transfers (movements)
        $transfers = \App\Models\LotTransfer::with(['fromStorage', 'toStorage', 'toSection', 'toRack', 'toBin', 'user'])
            ->whereIn('lot_id', $lots->pluck('id'))
            ->orderBy('created_at')
            ->get();

        // Warehouse transfers
        $warehouseTransfers = \App\Models\WarehouseTransfer::with(['fromWarehouse', 'toWarehouse', 'fromStorage', 'toStorage', 'user'])
            ->where('accession_id', $id)
            ->orderBy('created_at')
            ->get();

        // Seed requests
        $requests = \App\Models\SeedRequest::with(['user'])
            ->where('accession_id', $id)
            ->orderBy('created_at')
            ->get();

        // Dispatches
        $dispatches = \App\Models\Dispatch::with(['itn'])
            ->where('accession_id', $id)
            ->orderBy('created_at')
            ->get();

        // Build unified timeline
        $timeline = collect();

        // Accession created
        $timeline->push([
            'date'  => $accession->created_at,
            'type'  => 'accession',
            'icon'  => 'ri-seedling-line',
            'color' => 'success',
            'title' => 'Accession Created',
            'body'  => "Accession <strong>{$accession->accession_number}</strong> registered.",
        ]);

        // Lots created
        foreach ($lots as $lot) {
            $qty = $lot->seedQuantities->sum('quantity');
            $timeline->push([
                'date'  => $lot->created_at,
                'type'  => 'lot',
                'icon'  => 'ri-stack-line',
                'color' => 'primary',
                'title' => 'Lot Created',
                'body'  => "Lot <strong>{$lot->lot_number}</strong> created with qty <strong>{$qty}</strong> in storage <strong>{$lot->storage?->name}</strong>.",
            ]);
        }

        // Lot transfers
        foreach ($transfers as $t) {
            $timeline->push([
                'date'  => $t->created_at,
                'type'  => 'transfer',
                'icon'  => 'ri-swap-box-line',
                'color' => 'info',
                'title' => 'Lot Transfer',
                'body'  => "Moved from <strong>{$t->fromStorage?->name}</strong> → <strong>{$t->toStorage?->name}</strong>. Qty: <strong>{$t->quantity}</strong>. By: {$t->user?->name}.",
            ]);
        }

        // Warehouse transfers
        foreach ($warehouseTransfers as $wt) {
            $timeline->push([
                'date'  => $wt->created_at,
                'type'  => 'warehouse',
                'icon'  => 'ri-building-line',
                'color' => 'warning',
                'title' => 'Warehouse Transfer',
                'body'  => "Warehouse: <strong>{$wt->fromWarehouse?->name}</strong> → <strong>{$wt->toWarehouse?->name}</strong>. Storage: {$wt->fromStorage?->name} → {$wt->toStorage?->name}.",
            ]);
        }

        // Requests
        foreach ($requests as $r) {
            $timeline->push([
                'date'  => $r->created_at,
                'type'  => 'request',
                'icon'  => 'ri-file-list-3-line',
                'color' => 'secondary',
                'title' => 'Seed Request — ' . ucfirst($r->status),
                'body'  => "Request <strong>{$r->request_number}</strong> by {$r->requester_name}. Qty: {$r->quantity}. Status: <span class='badge bg-" . ($r->status === 'approved' ? 'success' : ($r->status === 'rejected' ? 'danger' : 'warning')) . "'>{$r->status}</span>",
            ]);
        }

        // Dispatches
        foreach ($dispatches as $d) {
            $timeline->push([
                'date'  => $d->created_at,
                'type'  => 'dispatch',
                'icon'  => 'ri-truck-line',
                'color' => 'danger',
                'title' => 'Dispatched — MRN: ' . $d->mrn_number,
                'body'  => "Dispatch <strong>{$d->dispatch_number}</strong>. Qty: {$d->quantity}. Courier: {$d->courier_name}.",
            ]);
        }

        // Sort by date ascending
        $timeline = $timeline->sortBy('date')->values();

        return view('report.accession-history', compact(
            'accession', 'lots', 'seedQuantities', 'transfers',
            'warehouseTransfers', 'requests', 'dispatches', 'timeline'
        ));
    }

    public function lotHistory($id)
    {
        $lot = \App\Models\Lot::with([
            'accession.crop', 'storage',
            'section', 'rack', 'bin', 'container',
            'seedQuantities.unit', 'seedQualities',
        ])->findOrFail($id);

        $seedQuantities    = \App\Models\SeedQuantity::where('lot_id', $id)->orderBy('created_at')->get();
        $qualities         = \App\Models\SeedQuality::where('lot_id', $id)->orderBy('created_at')->get();
        $transfers         = \App\Models\LotTransfer::with(['fromStorage','toStorage','fromSection','toSection','fromRack','toRack','fromBin','toBin','fromContainer','toContainer','user'])->where('lot_id', $id)->orderBy('created_at')->get();
        $warehouseTransfers= \App\Models\WarehouseTransfer::with(['fromWarehouse','toWarehouse','fromStorage','toStorage','user'])->where('lot_id', $id)->orderBy('created_at')->get();
        $requests          = \App\Models\SeedRequest::with(['user'])->where('accession_id', $lot->accession_id)->orderBy('created_at')->get();
        $dispatches        = \App\Models\Dispatch::with(['itn'])->where('lot_id', $id)->orderBy('created_at')->get();

        $timeline = collect();

        // Lot created
        $timeline->push(['date'=>$lot->created_at,'type'=>'created','icon'=>'ri-stack-line','color'=>'success',
            'title'=>'Lot Created',
            'body'=>"Lot <strong>{$lot->lot_number}</strong> created. Qty: <strong>{$seedQuantities->sum('quantity')}</strong>. Storage: <strong>{$lot->storage?->name}</strong>. Arrival: {$lot->arrival_type}."]);

        // Quality tests
        foreach ($qualities as $q) {
            $timeline->push(['date'=>$q->created_at,'type'=>'quality','icon'=>'ri-test-tube-line','color'=>'info',
                'title'=>'Quality Test Recorded',
                'body'=>"Germination: <strong>{$q->germination_percentage}%</strong> | Moisture: {$q->moisture_content}% | Purity: {$q->purity_percentage}% | Health: {$q->seed_health_status}."]);
        }

        // Lot transfers
        foreach ($transfers as $t) {
            $from = implode(' › ', array_filter([$t->fromStorage?->name, $t->fromSection?->name, $t->fromRack?->name, $t->fromBin?->name, $t->fromContainer?->name]));
            $to   = implode(' › ', array_filter([$t->toStorage?->name,   $t->toSection?->name,   $t->toRack?->name,   $t->toBin?->name,   $t->toContainer?->name]));
            $timeline->push(['date'=>$t->created_at,'type'=>'transfer','icon'=>'ri-swap-box-line','color'=>'warning',
                'title'=>'Lot Transfer',
                'body'=>"From: <strong>{$from}</strong> → To: <strong>{$to}</strong><br>Qty: {$t->quantity} | Open: {$t->o_quantity} | Close: {$t->c_quantity} | Balance: {$t->b_quantity} | By: {$t->user?->name}."]);
        }

        // Warehouse transfers
        foreach ($warehouseTransfers as $wt) {
            $timeline->push(['date'=>$wt->created_at,'type'=>'warehouse','icon'=>'ri-building-line','color'=>'primary',
                'title'=>'Warehouse Transfer',
                'body'=>"<strong>{$wt->fromWarehouse?->name}</strong> ({$wt->fromStorage?->name}) → <strong>{$wt->toWarehouse?->name}</strong> ({$wt->toStorage?->name})."]);
        }

        // Requests
        foreach ($requests as $r) {
            $sc = match($r->status) {'approved'=>'success','rejected'=>'danger','dispatched'=>'info','returned'=>'secondary',default=>'warning'};
            $timeline->push(['date'=>$r->created_at,'type'=>'request','icon'=>'ri-file-list-3-line','color'=>'secondary',
                'title'=>'Seed Request — '.ucfirst($r->status),
                'body'=>"Request <strong>{$r->request_number}</strong> by {$r->requester_name}. Qty: {$r->quantity}. <span class='badge bg-{$sc}'>{$r->status}</span>"]);
        }

        // Dispatches
        foreach ($dispatches as $d) {
            $timeline->push(['date'=>$d->created_at,'type'=>'dispatch','icon'=>'ri-truck-line','color'=>'danger',
                'title'=>'Dispatched — MRN: '.$d->mrn_number,
                'body'=>"Dispatch <strong>{$d->dispatch_number}</strong>. Qty: {$d->quantity}. Courier: {$d->courier_name}."]);
        }

        $timeline = $timeline->sortBy('date')->values();

        return view('report.lot-history', compact('lot','seedQuantities','transfers','warehouseTransfers','requests','dispatches','qualities','timeline'));
    }

    public function requestReport(Request $request)
    {
        $query = SeedRequest::with(['user', 'crop', 'unit']);
        

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->latest()->paginate(10);

        return view('report.request_report', compact('requests'));
    }

    // Download CSV
    public function downloadRequestReport(Request $request)
    {
        $fileName = 'request_report.csv';

        $requests = SeedRequest::with(['user', 'crop', 'unit'])->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $columns = [
            'Request No',
            'User',
            'Crop',
            'Quantity',
            'Unit',
            'Status',
            'Date'
        ];

        $callback = function () use ($requests, $columns) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $columns);

            foreach ($requests as $row) {
                fputcsv($file, [
                    $row->request_number,
                    $row->user->name ?? '',
                    $row->crop->name ?? '',
                    $row->quantity,
                    $row->unit->name ?? '',
                    $row->status,
                    $row->created_at,
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
public function expiryReport(Request $request)
{
    $accessions = Accession::with([
            'crop',
            'storageTime',
            'lots'
        ])

        // Expiry Date Filters from lots table
        ->whereHas('lots', function ($q) use ($request) {

            // Default = next 3 months data
            if (!$request->date_from && !$request->date_to) {

                $q->whereBetween('expiry_date', [
                    now()->subMonths(3),
                    now()->addMonths(3)
                ]);
            }

            // From Date
            if ($request->date_from) {

                $q->whereDate('expiry_date', '>=', $request->date_from);
            }

            // To Date
            if ($request->date_to) {

                $q->whereDate('expiry_date', '<=', $request->date_to);
            }

            // Status Filter
            if ($request->status == 'expired') {

                $q->whereDate('expiry_date', '<', now());

            } elseif ($request->status == 'critical') {

                $q->whereBetween('expiry_date', [
                    now(),
                    now()->addDays(3)
                ]);

            } elseif ($request->status == 'soon') {

                $q->whereBetween('expiry_date', [
                    now()->addDays(4),
                    now()->addDays(10)
                ]);

            } elseif ($request->status == 'safe') {

                $q->whereDate('expiry_date', '>', now()->addDays(10));
            }
        })

        // Sorting by lots expiry_date
        ->join('lots', 'accessions.id', '=', 'lots.accession_id')

        ->orderByRaw("
            CASE
                WHEN lots.expiry_date < CURDATE() THEN 0
                WHEN lots.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY) THEN 1
                WHEN lots.expiry_date BETWEEN DATE_ADD(CURDATE(), INTERVAL 4 DAY) AND DATE_ADD(CURDATE(), INTERVAL 10 DAY) THEN 2
                ELSE 3
            END
        ")

        ->orderBy('lots.expiry_date', 'asc')

        ->select('accessions.*')

        ->paginate(15);

    return view('report.expiry-report', compact('accessions'));
}
    
    public function downloadExpiryReport()
    {
        $accessions = Lot::with(['crop', 'storageTime'])
            ->whereNotNull('expiry_date')
            ->orderBy('expiry_date')
            ->get();

        $filename = "expiry_report.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($accessions) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Accession',
                'Crop',
                'Storage',
                'Recheck Date',
                'Expiry Date'
            ]);

            foreach ($accessions as $a) {
                fputcsv($file, [
                    $a->accession_name ?? $a->accession_number,
                    $a->crop->crop_name ?? '',
                    $a->storageTime->code ?? '',
                    $a->recheck_date,
                    $a->expiry_date,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Transaction Reports ───────────────────────────────────────────────

    private function transactionReportQuery(string $type, Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        switch ($type) {
            case 'arrival':
                // Accessions with acc_source = external (arrived from outside)
                $query = Accession::with(['crop', 'warehouse'])
                    ->where('acc_source', 'external');
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('created_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'accessioning':
                $query = Accession::with(['crop', 'warehouse', 'storageType']);
                if ($dateFrom) $query->whereDate('entry_date', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('entry_date', '<=', $dateTo);
                return $query->latest()->get();

            case 'request':
                $query = SeedRequest::with(['user', 'crop',]);
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('created_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'dispatch':
                // Approved/dispatched requests
                $query = SeedRequest::with(['user', 'crop',])
                    ->whereIn('status', ['approved', 'dispatched', 'completed']);
                if ($dateFrom) $query->whereDate('approved_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('approved_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'return':
                $query = SeedRequest::with(['user', 'crop',])
                    ->where('status', 'returned');
                if ($dateFrom) $query->whereDate('updated_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('updated_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'regeneration':
                // Lots created for regeneration (lot type name contains 'regen')
                $query = \App\Models\Lot::with(['accession', 'crop', 'lotType', 'storage'])
                    ->whereHas('lotType', fn($q) => $q->where('name', 'like', '%regen%'))
                    ->orWhere('description', 'like', '%regen%');
                if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('created_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'disposal':
                $query = Accession::with(['crop'])
                    ->where('status', 'depleted');
                if ($dateFrom) $query->whereDate('updated_at', '>=', $dateFrom);
                if ($dateTo)   $query->whereDate('updated_at', '<=', $dateTo);
                return $query->latest()->get();

            case 'change':
                $query = \App\Models\LotTransfer::with([
        'lot.accession.crop',
        'fromStorage',
        'toStorage',
        'toSection',
        'toRack',
        'toBin',
        'toContainer',
        'user'
    ]);
                if ($dateFrom) {
        $query->whereDate('created_at', '>=', $dateFrom);
    }

    if ($dateTo) {
        $query->whereDate('created_at', '<=', $dateTo);
    }

    return $query->latest()->get();
        }
        return collect();
    }

    public function transactionReport(Request $request, string $type)
    {
        $titles = [
            'arrival'       => 'Arrival Transaction',
            'accessioning'  => 'Accessioning Transaction',
            'request'       => 'Request Transaction',
            'dispatch'      => 'Dispatch Transaction',
            'return'        => 'Return Transaction',
            'regeneration'  => 'Regeneration Transaction',
            'disposal'      => 'Disposal Transaction',
            'change'      => 'Lot Inter Change Transaction',
        ];

        if (!array_key_exists($type, $titles)) abort(404);

        $records   = $this->transactionReportQuery($type, $request);
        $title     = $titles[$type];
        $dateFrom  = $request->date_from;
        $dateTo    = $request->date_to;

        return view('report.transaction-report', compact('records', 'title', 'type', 'dateFrom', 'dateTo'));
    }

    public function downloadTransactionReport(Request $request, string $type)
    {
        $titles = [
            'arrival'       => 'Arrival Transaction',
            'accessioning'  => 'Accessioning Transaction',
            'request'       => 'Request Transaction',
            'dispatch'      => 'Dispatch Transaction',
            'return'        => 'Return Transaction',
            'regeneration'  => 'Regeneration Transaction',
            'disposal'      => 'Disposal Transaction',
        ];

        if (!array_key_exists($type, $titles)) abort(404);

        $records  = $this->transactionReportQuery($type, $request);
        $filename = $type . '_transaction_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records, $type) {
            $file = fopen('php://output', 'w');

            // Dynamic columns per type
            if (in_array($type, ['arrival', 'accessioning', 'disposal'])) {
                fputcsv($file, ['Accession No', 'Accession Name', 'Crop', 'Quantity', 'Warehouse', 'Status', 'Date']);
                foreach ($records as $r) {
                    fputcsv($file, [
                        $r->accession_number,
                        $r->accession_name,
                        $r->crop?->crop_name ?? '',
                        $r->quantity,
                        $r->warehouse?->name ?? '',
                        $r->status,
                        $r->created_at?->format('Y-m-d'),
                    ]);
                }
            } elseif (in_array($type, ['request', 'dispatch', 'return'])) {
                fputcsv($file, ['Request No', 'Requester', 'Crop', 'Quantity', 'Status', 'Date']);
                foreach ($records as $r) {
                    fputcsv($file, [
                        $r->request_number,
                        $r->user?->name ?? $r->requester_name ?? '',
                        $r->crop?->crop_name ?? '',
                        $r->quantity,
                        $r->status,
                        $r->created_at?->format('Y-m-d'),
                    ]);
                }
            } elseif ($type === 'regeneration') {
                fputcsv($file, ['Lot Number', 'Accession', 'Crop', 'Lot Type', 'Quantity', 'Storage', 'Status', 'Date']);
                foreach ($records as $r) {
                    fputcsv($file, [
                        $r->lot_number,
                        $r->accession?->accession_number ?? '',
                        $r->core_crop?->crop_name ?? '',
                        $r->lotType?->name ?? '',
                        $r->quantity,
                        $r->storage?->name ?? '',
                        $r->status,
                        $r->created_at?->format('Y-m-d'),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
