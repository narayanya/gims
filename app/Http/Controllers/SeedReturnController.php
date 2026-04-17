<?php

namespace App\Http\Controllers;

use App\Models\SeedClass;
use App\Models\SeedRequest;
use App\Models\SeedReturn;
use Illuminate\Http\Request;

class SeedReturnController extends Controller
{
    public function store(Request $req, $id)
    {
        $seedRequest = SeedRequest::with('accession')->findOrFail($id);

        // ✅ Save in seed_returns table
        $return = SeedReturn::create([
            'request_id'       => $id,
            'accession_id'     => $seedRequest->accession_id,
            'return_type'      => $req->return_type, // full | partial | regeneration
            'return_quantity'  => $req->return_quantity,
            'return_date'      => $req->return_date,
            'remarks'          => $req->return_remarks,
            'germination_rate' => $req->germination_rate,
            'moisture_rate'    => $req->moisture_rate,
        ]);

        // ==============================
        // 🔥 REGENERATION LOGIC
        // ==============================
        if ($req->return_type === 'regeneration') {

            // Update request status
            $seedRequest->update([
                'status' => 'regeneration'
            ]);

            // Add returned quantity back to accession
            if ($seedRequest->accession && $req->return_quantity) {
                $seedRequest->accession->increment('quantity_show', $req->return_quantity);
            }

            // OPTIONAL: create regeneration record (future)
            /*
            Regeneration::create([
                'request_id' => $id,
                'quantity' => $req->return_quantity,
                'germination_rate' => $req->germination_rate,
            ]);
            */

        }

        // ==============================
        // EXISTING LOGIC (optional keep)
        // ==============================
        elseif ($req->return_type === 'full') {
            $seedRequest->update(['status' => 'returned']);

            if ($seedRequest->accession) {
                $seedRequest->accession->increment('quantity_show', $seedRequest->quantity);
            }
        }

        elseif ($req->return_type === 'partial') {
            $seedRequest->update(['status' => 'partially_returned']);

            if ($seedRequest->accession) {
                $seedRequest->accession->increment('quantity_show', $req->return_quantity);
            }
        }

        return back()->with('success', 'Regeneration saved successfully');
    }
}
