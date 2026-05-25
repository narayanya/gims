<?php

namespace App\Http\Controllers;
use App\Models\Accession;
use Illuminate\Http\Request;
use App\Models\Lot;
use App\Models\Storage;
use App\Models\Crop;
use App\Models\LotRegeneration;

class LotRegenerationController extends Controller
{
    public function index(Request $request)
    {
       
    $query = \App\Models\LotTransfer::with([
        'lot',
        'lot.crop',
        'lot.accession',
        'fromStorage',
        'toStorage',
        'user'
    ]);

    // ✅ DATE FILTER
    if ($request->date_from) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->date_to) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $transfers = $query->latest()->paginate(10);
    
 
    return view('lot-regeneration.index', [
        'crops' => Crop::where('update_status', 1)->orderBy('crop_name')->get(['id','crop_code','crop_name']),
        'accessions' => Accession::where('status', 1)->orderBy('accession_number')->get(['id','crop_id','accession_number']),
        'storages'   => Storage::where('status', 1)->orderBy('name')->get(['id','storage_id','name']),
        'lots'        => Lot::with('accession')
            ->orderBy('created_at', 'desc')
            ->paginate(12),
        'regenerations' => LotRegeneration::with('lot')
                    ->latest()
                    ->get(),

        // ✅ ADD THIS
        'transfers'  => $transfers,
    ]);


    }

    public function getLot($id)
    {
        $lot = Lot::find($id);

        if (!$lot) {
            return response()->json([
                'status' => false
            ]);
        }

        return response()->json([
            'status' => true,
            'lot' => $lot
        ]);
    }


   public function store(Request $request)
    {
        $request->validate([
            'from_lot_id'  => 'required|exists:lots,id',
            'regen_year'   => 'nullable',
            'expiry_date'  => 'nullable|date',
            'recheck_date' => 'nullable|date',
            'reason'       => 'nullable|string',
        ]);

        $lot = Lot::findOrFail($request->from_lot_id);

        LotRegeneration::create([

            'lot_id' => $lot->id,
            'old_expiry_date' => $lot->expiry_date,
            'old_regen_year' => $lot->regen_year,
            'old_regeneration_date' => $lot->regeneration_date,
            'regen_year' => $request->regen_year,
            'expiry_date' => $request->expiry_date,
            'regeneration_date' => $request->recheck_date,
            'reason' => $request->reason,
        ]);

        // optional lot table update
        Lot::where('id', $request->from_lot_id)->update([
            'regen_year'         => $request->regen_year,
            'expiry_date'        => $request->expiry_date,
            'regeneration_date'  => $request->recheck_date,
        ]);

        return back()->with('success', 'Saved Successfully');
    }
}
