<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\CropRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;


class CropRequestController extends Controller
{
    public function index()
    {
        $cropRequests = CropRequest::with('user')
            ->latest()
            ->paginate(10);

        return view('master.crop.crop-request-list', compact('cropRequests'));
    }
    public function storeCropRequest(Request $request)
    {
        $request->validate([
            'req_crop_name' => 'required|string|max:255',
            'req_crop_code' => 'required|string|max:100',
            'description'   => 'nullable|string',
        ]);

        // ✅ Save to DB
        $cropRequest = CropRequest::create([
            'crop_name'  => $request->req_crop_name,
            'crop_code'  => $request->req_crop_code,
            'description'=> $request->description,
            'user_id'    => auth()->id(),
        ]);

        // ✅ Email भेजना
        $emails = ['corecrop@vspl.com']; // multiple emails add kar sakte ho

        foreach ($emails as $email) {
            Mail::raw(
                "New Crop Request\n\n".
                "Crop Name: {$cropRequest->crop_name}\n".
                "Crop Code: {$cropRequest->crop_code}\n".
                "Description: {$cropRequest->description}",
                function ($message) use ($email) {
                    $message->to($email)
                            ->subject('New Crop Request Submitted');
                }
            );
        }

        return back()->with('success', 'Crop request submitted successfully!');
    }

}