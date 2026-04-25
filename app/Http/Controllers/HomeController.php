<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accession;
use App\Models\Crop;
use App\Models\Variety;
use App\Models\Lot;
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

        return view('home', compact(
            'totalAccessions',
            'totalCrops',
            'updatedCrops',
            'notUpdatedCrops',
            'totalLots',
            'totalWarehouses',
            'recentAccessions',
            'latestRequests',
            'storages',
            'storageTimes',
            'lowStockCount',
            'lowStockAccessions',
            'expiringSoon',
        ));
    }
}
