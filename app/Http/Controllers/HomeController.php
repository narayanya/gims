<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accession;
use App\Models\Crop;
use App\Models\Dispatch;
use App\Models\Variety;
use App\Models\Lot;
use App\Models\LotTransfer;
use App\Models\SeedQuality;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse;
use App\Models\SeedRequest;
use App\Models\StorageTime;
use App\Models\Storage;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalAccessions = Accession::count();
        $totalCrops = Crop::count();
        $updatedCrops = Crop::where('update_status', 1)->count();
        $notUpdatedCrops = Crop::where('update_status', 0)->count();
        $totalLots = Lot::count();
        $totalWarehouses = Warehouse::count();


        // Low stock threshold
        $lowStockThreshold = 10;

        // Accessions where total available quantity (sum of seed_quantities.quantity_show) is below threshold
        $lowStockAccessions = Accession::with(['crop'])
            ->withSum('seedQuantities as total_available', 'quantity_show')
            ->having('total_available', '<=', $lowStockThreshold)
            ->having('total_available', '>', 0)
            ->orderBy('total_available')
            ->take(10)
            ->get();

        $lowStockCount = Accession::withSum('seedQuantities as total_available', 'quantity_show')
            ->having('total_available', '<=', $lowStockThreshold)
            ->having('total_available', '>', 0)
            ->count();

        // Recent accessions with correct quantity from seed_quantities
        $recentAccessions = Accession::with(['crop', 'storageTime', 'capacityUnit'])
            ->withSum('seedQuantities as total_available', 'quantity_show')
            ->latest('updated_at')
            ->take(10)
            ->get();

        $latestRequests = SeedRequest::with(['crop'])
                    ->where('status', 'pending')
                    ->latest()
                    ->take(5)
                    ->get();
        $dispatchRequests = SeedRequest::with(['crop'])
                    ->where('status', 'approved')
                    ->latest()
                    ->take(3)
                    ->get();

        $storages = Storage::with(['storageType', 'warehouse.state',
            'warehouse.district',
            'warehouse.city'])->latest()->take(4)->get();

        $storageTimes = StorageTime::orderBy('code')->get();

        $expiringSoon = Accession::with('crop')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->take(5)
            ->get();
        
        $recentDispatches = Dispatch::with(['accession.crop', 'itn'])
            ->latest()
            ->take(3)
            ->get();

         $lotTransfers = \App\Models\LotTransfer::with([
                'crop',
                'accession',
                'fromStorage',
                'toStorage',
                'fromRack',
                'toRack',
                'fromBin',
                'toBin',
                'fromContainer',
                'toContainer',
                'transferredBy'
            ])
            ->latest()
            ->take(4)
            ->get();

            $todayAccessionCount = Accession::whereDate('created_at', today())->count();

$todayLotCount = Lot::whereDate('created_at', today())->count();

$todayRequestCount = SeedRequest::whereDate('created_at', today())->count();

$todayDispatchCount = Dispatch::whereDate('created_at', today())->count();

$todayTransferCount = LotTransfer::whereDate('created_at', today())->count();

$monthlyData = [];

    for ($m = 1; $m <= 12; $m++) {

        $monthlyData[] = [
            'month' => date('M', mktime(0,0,0,$m,1)),

            'incoming' => Accession::whereMonth('created_at', $m)->count(),

            'dispatch' => Dispatch::whereMonth('created_at', $m)->count(),

            'transfer' => LotTransfer::whereMonth('created_at', $m)->count(),

            'qc' => SeedQuality::whereMonth('created_at', $m)->count(),
        ];
    }

    $cropStockChart = DB::table('core_crop')

    ->leftJoin('lots', 'core_crop.id', '=', 'lots.crop_id')

    ->leftJoin('seed_quantities', 'lots.id', '=', 'seed_quantities.lot_id')

    ->where('core_crop.update_status', 1)

    ->select(
        'core_crop.crop_name',
        DB::raw('ROUND(COALESCE(SUM(seed_quantities.quantity),0),2) as total_quantity')
    )

    ->groupBy('core_crop.id', 'core_crop.crop_name')

    ->orderByDesc('total_quantity')

    ->get();
// For Most Requested Crop
    $mostRequestedCrops = DB::table('dispatches')

    ->join('lots', 'dispatches.lot_id', '=', 'lots.id')

    ->join('core_crop', 'lots.crop_id', '=', 'core_crop.id')

    ->select(
        'core_crop.crop_name',

        DB::raw('COUNT(dispatches.id) as total_requests'),

        DB::raw('ROUND(SUM(dispatches.quantity),2) as total_quantity')
    )

    ->groupBy('core_crop.id', 'core_crop.crop_name')

    ->orderByDesc('total_quantity')

    ->get();

    $topCrop = $mostRequestedCrops->first();
    
    //For Pending QC Samples

    $pendingQCSamples = DB::table('lots')

        ->leftJoin('seed_qualities', 'lots.id', '=', 'seed_qualities.lot_id')

        ->join('core_crop', 'lots.crop_id', '=', 'core_crop.id')

        ->whereNull('seed_qualities.id')

        ->select(
            'lots.id',
            'lots.lot_number',
            'core_crop.crop_name',
            'lots.created_at'
        )

        ->latest('lots.created_at')

        ->get();

    $pendingQCCount = $pendingQCSamples->count();

    //regunation date  dashboard

    $activeRegenerationCycles = DB::table('accessions')

    ->join('core_crop', 'accessions.crop_id', '=', 'core_crop.id')

    ->whereNotNull('accessions.recheck_date')

    ->whereDate(
        'accessions.recheck_date',
        '<=',
        now()->addMonths(12)
    )

    ->select(
        'accessions.id',
        'accessions.accession_number',
        'accessions.accession_name',
        'accessions.recheck_date',
        'core_crop.crop_name'
    )

    ->orderBy('accessions.recheck_date')

    ->get();
    $activeRegenerationCount = $activeRegenerationCycles->count();

        return view('home', compact(
            'totalAccessions',
            'totalCrops',
            'updatedCrops',
            'notUpdatedCrops',
            'totalLots',
            'totalWarehouses',
            'recentAccessions',
            'latestRequests',
            'dispatchRequests',
            'storages',
            'storageTimes',
            'lowStockCount',
            'lowStockAccessions',
            'expiringSoon',
            'recentDispatches',
            'lotTransfers',
            'todayAccessionCount',
            'todayLotCount',
            'todayRequestCount',
            'todayDispatchCount',
            'todayTransferCount',
            'monthlyData',
            'cropStockChart',
            'topCrop',
            'pendingQCSamples',
            'pendingQCCount',
            'activeRegenerationCount',
            'activeRegenerationCycles'
        ));
    }

    
}
