<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\SeedRequest;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    // ✅ List all approved requests (ready for dispatch)
    public function index()
    {
        $dispatches = Dispatch::with(['request', 'accession'])->latest()->get();   
        $requests = SeedRequest::with(['user','crop','unit','accession'])
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('dispatch-management.index', compact('requests', 'dispatches'));
    }

    public function store(Request $req, $id)
    {
        $req->validate([
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

        // Deduct dispatched quantity from accession's quantity_show
        if ($request->accession_id) {
            $accession = \App\Models\Accession::find($request->accession_id);
            if ($accession) {
                $newQty = max(0, (float)$accession->quantity_show - (float)$dispatch->quantity);
                $accession->update(['quantity_show' => $newQty]);
            }
        }

        return redirect()->route('dispatch.print', $dispatch->id);
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
        $request = SeedRequest::with(['crop','unit','user','accession'])
            ->findOrFail($id);

        return view('dispatch-management.show', compact('request'));
    }
    

    public function print($id)
    {
        $dispatch = Dispatch::with([
            'request.crop', 'request.unit',
            'request.user', 'request.approvedBy', 'accession'
        ])->findOrFail($id);

        return view('dispatch-management.print', compact('dispatch'));
    }
}