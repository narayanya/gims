<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QualityMaster;

class QualityMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $qualityMasters = QualityMaster::all();
        return view('master.quality_master.index', compact('qualityMasters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'qc_code' => 'required|unique:quality_master,qc_code',
        'qc_name' => 'required',
        ]);

        QualityMaster::create([
            'qc_code' => $request->qc_code,
            'qc_name' => $request->qc_name,
            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'Quality Master created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
