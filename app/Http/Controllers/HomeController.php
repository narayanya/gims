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
        $updatedCrops = Crop::where('update_status', 1)->count(); // Yes
        $notUpdatedCrops = Crop::where('update_status', 0)->count(); // No
       // $totalVarieties = Variety::count();
        $totalLots = Lot::count();
        $totalWarehouses = Warehouse::count();
        $recentAccessions = Accession::with(['crop', 'storageTime'])
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
        $storageTimes = StorageTime::orderBy('code')
        ->get();

        return view('home', compact(
            'totalAccessions',
            'totalCrops',
            'updatedCrops',
            'notUpdatedCrops',
            //'totalVarieties',
            'totalLots',
            'totalWarehouses',
            'recentAccessions',
            'latestRequests',
            'storages',
            'storageTimes',
        ));
    }
}
