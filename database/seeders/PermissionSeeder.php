<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = ['crop', 'variety', 'lot', 'storage', 'accession', 'request'];
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['slug' => $module . '.' . $action],
                    [
                        'name'        => ucfirst($module) . ' ' . ucfirst($action),
                        'description' => ucfirst($action) . ' ' . ucfirst($module),
                    ]
                );
            }
        }

        // Extra permission
        Permission::firstOrCreate(
            ['slug' => 'request.approve'],
            ['name' => 'Request Approve', 'description' => 'Approve seed requests']
        );

        // Ensure default roles exist
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full access'],
            ['name' => 'Admin',       'slug' => 'admin',       'description' => 'Admin access'],
            ['name' => 'Manager',     'slug' => 'manager',     'description' => 'Manager access'],
            ['name' => 'User',        'slug' => 'user',        'description' => 'Basic user'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['slug' => $r['slug']], $r);
        }

        // Super admin gets all permissions
        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync(Permission::pluck('id'));
        }
    }
}
