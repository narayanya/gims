<?php

namespace App\Http\Controllers;
use App\Models\SeedRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use App\Models\Role;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('settings.role', compact('roles'));
    }

    public function store(Request $request)
    {
         $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:50|unique:roles,slug',
            'description' => 'nullable|string|max:1000',
        ]);

        Role::create($request->only('name', 'slug', 'description'));
        return redirect()->route('settings.role')->with('success', 'Role added successfully.');    

    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->all());

        return $role;
    }

    public function destroy($id)
    {
        return Role::destroy($id);
    }
}
