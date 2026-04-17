<?php

namespace App\Http\Controllers;

use App\Models\AccessionRule;
use Illuminate\Http\Request;

class AccessionRuleController extends Controller
{
    public function index()
    {
        $rules = AccessionRule::orderBy('name')->paginate(15);
        return view('master.accession-rule.index', compact('rules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'nullable|string|max:50|unique:accession_rules,code',
            'rule_type' => 'nullable|string|max:100',
            'status'    => 'required|in:0,1',
        ]);

        AccessionRule::create($request->only(
            'name', 'code', 'rule_type', 'description',
            'min_value', 'max_value', 'unit', 'is_mandatory', 'status'
        ));

        return redirect()->route('accession-rules.index')->with('success', 'Rule created successfully.');
    }

    public function update(Request $request, AccessionRule $accessionRule)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'nullable|string|max:50|unique:accession_rules,code,' . $accessionRule->id,
            'rule_type' => 'nullable|string|max:100',
            'status'    => 'required|in:0,1',
        ]);

        $accessionRule->update($request->only(
            'name', 'code', 'rule_type', 'description',
            'min_value', 'max_value', 'unit', 'is_mandatory', 'status'
        ));

        return redirect()->route('accession-rules.index')->with('success', 'Rule updated successfully.');
    }

    public function destroy(AccessionRule $accessionRule)
    {
        $accessionRule->delete();
        return redirect()->route('accession-rules.index')->with('success', 'Rule deleted.');
    }
}
