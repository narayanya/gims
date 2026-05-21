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

        // Structured module config: module => [ group => [actions] ]
        $modules = [
            'accession' => [
                'core'    => ['view', 'create', 'edit', 'delete'],
                'data'    => ['import', 'export', 'report'],
            ],
            'lot' => [
                'core'    => ['view', 'create', 'edit', 'delete'],
                'data'    => ['import', 'export', 'transfer'],
                'workflow'=> ['transfer', 'quality_update'],
            ],
            'crop' => [
                'core'    => ['view', 'create', 'edit', 'delete'],
                'data'    => ['import', 'export'],
            ],
            'storage' => [
                'core'    => ['view', 'create', 'edit', 'delete'],
                'workflow'=> ['transfer'],
                'data'    => ['export'],
            ],
            'request' => [
                'core'    => ['view', 'create', 'edit', 'delete'],
                'workflow'=> ['approve', 'receive', 'return', 'export'],
            ],
            'dispatch' => [
                'core'    => ['view', 'create', 'edit', 'delete'],
                'workflow'=> ['mrn', 'export'],
            ],
            'report' => [
                'access'  => ['view', 'summary', 'export', 'expiry', 'transaction', 'request'],
            ],
            'menu' => [
                'access'  => ['dashboard', 'accession', 'lot', 'storage', 'dispatch', 'request', 'reports', 'masters', 'master_settings', 'settings', 'logs'],
            ],
            'settings' => [
                'admin'   => ['view', 'users', 'roles', 'permissions', 'masters','logs',],
            ],
            'master' => [
                'access' => [
                    'crop',
                    'category',
                    'crop_category',
                    'crop_type',
                    'variety_type',
                    'season',
                    'seed_class',
                    'unit',
                    'soil_type',
                    'arrival_type',
                    'pouch',
                    'location',
                    'employee',
                    'quality',
                ],
            ],
            'storage_master' => [
                'access' => [
                    'warehouse',
                    'storage_type',
                    'storage_time',
                    'storage_condition',
                    'rack_bin',
                ],
            ],
        ];

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
