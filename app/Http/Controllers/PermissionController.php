<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $roles       = Role::with('permissions')->orderBy('id')->get();
        $permissions = Permission::orderBy('slug')->get();
        $modules     = ['crop', 'variety', 'lot', 'storage', 'accession', 'request'];

        return view('settings.permission', compact('roles', 'permissions', 'modules'));
    }

    public function update(Request $request)
    {
        // $request->permissions = ['role_id' => ['perm_slug', ...], ...]
        $matrix = $request->input('permissions', []);

        foreach ($matrix as $roleId => $slugs) {
            $role = Role::find($roleId);
            if (!$role) continue;
            $ids = Permission::whereIn('slug', $slugs)->pluck('id');
            $role->permissions()->sync($ids);
        }

        // Roles with no checkboxes ticked — clear their permissions
        $allRoleIds = Role::pluck('id');
        foreach ($allRoleIds as $rid) {
            if (!isset($matrix[$rid])) {
                Role::find($rid)?->permissions()->sync([]);
            }
        }

        return redirect()->route('settings.permission')->with('success', 'Permissions saved successfully.');
    }
}
