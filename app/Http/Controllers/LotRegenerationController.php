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
        'lot'        => null,

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
            'lot_id' => 'required|exists:lots,id',
            'type'   => 'required|in:Regeneration,Dispose',
            'date'   => 'required|date',
            'reason' => 'nullable|string',
            'status' => 'required'
        ]);

        LotRegeneration::create([
            'lot_id' => $request->lot_id,
            'type'   => $request->type,
            'date'   => $request->date,
            'reason' => $request->reason,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Saved Successfully');
    }
}
