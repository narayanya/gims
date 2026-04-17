<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        $seasons = Season::orderBy('name')->get();
        return view('master.season.index', compact('seasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:seasons,name',
            'code' => 'nullable|string|max:50|unique:seasons,code',
            'description' => 'nullable|string|max:1000',
        ]);

        Season::create($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('seasons.index')->with('success', 'Season added successfully.');
    }

    public function update(Request $request, $id)
    {
        $season = Season::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:seasons,name,' . $season->id,
            'code' => 'nullable|string|max:50|unique:seasons,code,' . $season->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $season->update($request->only('name', 'code', 'status', 'description'));
        return redirect()->route('seasons.index')->with('success', 'Season updated successfully.');
    }

    public function destroy($id)
    {
        $season = Season::findOrFail($id);
        $season->update(['status' => 0]);
        return redirect()->route('seasons.index')->with('success', 'Season deactivated.');
    }
}
