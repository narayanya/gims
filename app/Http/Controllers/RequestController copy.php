<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\SeedRequest;
use App\Models\Crop;
use App\Models\Unit;
use App\Models\Accession;
use App\Models\Notification;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    
    public function index(HttpRequest $request)
    {
        $query = SeedRequest::with(['user', 'crop', 'unit', 'approvedBy']);

        // Crop Filter
        if ($request->crop) {
            $query->where('crop_id', $request->crop);
        }

        // User Filter
        if ($request->user) {
            $query->where('user_id', $request->user);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        $crops = Crop::where([
                ['is_active', 1],
                ['update_status', 1]
            ])->select('id', 'crop_name')->get(); 
        $units = Unit::all();
        $users = User::all();   // IMPORTANT (for user filter)

        return view('requests.index', compact('requests', 'crops', 'units', 'users'));
    }

    public function return(HttpRequest $request, $id)
    {
        $req = SeedRequest::findOrFail($id);
        $req->status          = 'returned';
        $req->return_quantity = $request->return_quantity;
        $req->return_remarks  = $request->return_remarks;
        $req->return_date     = $request->return_date ?? now()->toDateString();
        $req->save();

        Notification::create([
            'user_id' => $req->user_id,
            'title'   => 'Request Returned',
            'message' => 'Seed request ' . $req->request_number . ' has been marked as returned.',
        ]);

        return redirect()->back()->with('success', 'Request marked as returned.');
    }

    public function receive(HttpRequest $request, $id)
    {
        $req = SeedRequest::findOrFail($id);
        $req->status          = 'received';
        $req->receive_status  = 'received';
        $req->receive_remarks = $request->receive_remarks;
        $req->receive_date    = $request->receive_date ?? now()->toDateString();
        $req->save();

        Notification::create([
            'user_id' => $req->user_id,
            'title'   => 'Request Received',
            'message' => 'Your seed request ' . $req->request_number . ' has been marked as received.',
        ]);

        return redirect()->back()->with('success', 'Request marked as received.');
    }

    public function approve(HttpRequest $request, $id)
        {
            $req = SeedRequest::findOrFail($id);
            $user = Auth::user();
            $req->status = 'approved';
            $req->remarks = $request->remarks;
            $req->approved_by = $user->id;
            $req->approved_at = now();

            $req->save();
        
            Notification::create([
                'user_id' => $req->user_id,
                'title' => 'Request Approved',
                'message' => 'Your seed request '.$req->request_number.' has been approved. Dispatch will happen soon.'
            ]);

            return redirect()->back()->with('success','Request Approved Successfully');
        }

    public function create(Request $request)
        {
            $crops = Crop::where([
                ['is_active', 1],
                ['update_status', 1]
            ])->select('id', 'crop_name')->get();
            
            $units = Unit::all();
            $users = User::all();
           $accessions = Accession::where('requester_show', 'yes')->where('status', 1)->get();
           // ✅ Get accession_id from URL
            $preselectedAccessionId = $request->accession_id;

            return view('requests.create', compact('crops', 'units', 'users', 'accessions', 'preselectedAccessionId'))->with('seedRequest', null);;
        }

    public function store(HttpRequest $httpRequest)
        {
            $validated = $httpRequest->validate([
                'crop_id' => 'required|exists:core_crop,id',
                'accession_id' => 'required|exists:accessions,id',
                'quantity' => 'required|numeric|min:0.01',
                'unit_id' => 'required|exists:units,id',
                'user_id' => 'nullable|exists:users,id',
                'purpose' => 'nullable|string',
                'purpose_details' => 'nullable|string',
                'request_through' => 'nullable|string',
                'request_date' => 'required|date',
                'required_date' => 'nullable|date|after_or_equal:request_date',
                'notes' => 'nullable|string',
            ]);

            // Check requested qty does not exceed accession's available qty
            if ($httpRequest->accession_id) {
                $accession = Accession::find($httpRequest->accession_id);
                if ($accession && $accession->quantity_show !== null) {
                    if ((float)$httpRequest->quantity > (float)$accession->quantity_show) {
                        return back()->withInput()->withErrors([
                            'quantity' => 'Request quantity (' . $httpRequest->quantity . ') cannot exceed available quantity (' . $accession->quantity_show . ').',
                        ]);
                    }
                }
            }

            // If admin selected user
            $user = Auth::user();
            if ($httpRequest->user_id) {

                $user = User::find($httpRequest->user_id);

                $validated['user_id'] = $user->id;
                $validated['requester_name'] = $user->name;
                $validated['requester_email'] = $user->email;

            } else {

                // Normal logged user
                $validated['user_id'] = $user->id;
                $validated['requester_name'] = $user->name;
                $validated['requester_email'] = $user->email;
            }

            $validated['request_number'] = SeedRequest::generateRequestNumber();
            $validated['status'] = 'pending';
            $validated['request_through'] = $validated['request_through'] ?? '3';

            $admins = User::whereHas('role', function ($q) {
            $q->whereIn('slug',['admin','super-admin']);
            })->get();

            foreach ($admins as $admin) {
                $user = Auth::user();
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'New Seed Request',
                    'message' => $user->name.' submitted a new seed request.'
                ]);

            }

            SeedRequest::create($validated);

            return redirect()->route('requests.index')
                ->with('success', 'Request created successfully.');
        }

    public function show(SeedRequest $seedRequest)
        {
            $seedRequest->load(['crop', 'unit', 'approvedBy', 'accession.unit']);
            return view('requests.show', compact('seedRequest'));
        }

    public function edit(SeedRequest $seedRequest)
        {
            $crops = Crop::where([
                ['is_active', 1],
                ['update_status', 1]
            ])->select('id', 'crop_name')->get();
            $accessions = Accession::all();
            $units = Unit::all();
            $users = User::all();

            return view('requests.create', compact(
                'seedRequest',
                'crops',
                'accessions',
                'units',
                'users'
            ))->with('preselectedAccessionId', $seedRequest->accession_id);
        }

    public function update(HttpRequest $httpRequest, SeedRequest $seedRequest)
        {
            $validated = $httpRequest->validate([
                'crop_id'        => 'required|exists:core_crop,id',
                'quantity'       => 'required|numeric|min:0.01',
                'unit_id'        => 'required|exists:units,id',
                'user_id'        => 'nullable|exists:users,id',
                'purpose'        => 'nullable|string',
                'purpose_details'=> 'nullable|string',
                'request_through'=> 'nullable|string',
                'request_date'   => 'nullable|date',
                'required_date'  => 'nullable|date',
                'notes'          => 'nullable|string',
                'status'         => 'nullable|in:pending,approved,rejected,completed',
            ]);

            // Resolve requester info from user_id if provided
            if (!empty($validated['user_id'])) {
                $user = User::find($validated['user_id']);
                $validated['requester_name']  = $user->name;
                $validated['requester_email'] = $user->email;
            }

            // Keep existing status if not submitted
            if (empty($validated['status'])) {
                unset($validated['status']);
            }

            // Keep existing request_date if not submitted
            if (empty($validated['request_date'])) {
                unset($validated['request_date']);
            }

            // Auto-set approval info if status changed to approved
            if (isset($validated['status']) && $validated['status'] === 'approved' && $seedRequest->status !== 'approved') {
                $validated['approved_by'] = Auth::id();
                $validated['approved_at'] = now();
            }

            $seedRequest->update($validated);

            return redirect()->route('requests.index')
                ->with('success', 'Request updated successfully.');
        }


    
    public function destroy(SeedRequest $seedRequest)
        {
            $seedRequest->delete();

            return redirect()->route('requests.index')
                ->with('success', 'Request deleted successfully');
        }


    public function getAccessions($crop_id)
        {
            $accessions = Accession::where('crop_id', $crop_id)
                ->where('requester_show', 'yes')     
                ->get(['id', 'accession_number']);
            return response()->json($accessions);
        }
    
}
