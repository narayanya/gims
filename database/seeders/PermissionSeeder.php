<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [

            'accession' => [
                'core' => ['view', 'create', 'edit', 'delete'],
                'data' => ['import', 'export', 'report'],
            ],

            'crop' => [
                'core' => ['view', 'create', 'edit', 'delete'],
                'data' => ['import', 'export'],
            ],

            'variety' => [
                'core' => ['view', 'create', 'edit', 'delete'],
            ],

            'lot' => [
                'core' => ['view', 'create', 'edit', 'delete'],
                'workflow' => [
                    'transfer',
                    'quality_update',
                ],
                'data' => ['import', 'export'],
            ],

            'storage' => [
                'core' => ['view', 'create', 'edit', 'delete'],
                'workflow' => ['transfer'],
                'data' => ['export'],
            ],

            'request' => [
                'core' => ['view', 'create', 'edit', 'delete'],
                'workflow' => [
                    'approve',
                    'receive',
                    'return',
                    'export'
                ],
            ],

            'dispatch' => [
                'core' => ['view', 'create', 'edit', 'delete'],
                'workflow' => ['mrn', 'export'],
            ],

            'report' => [
                'access' => [
                    'view',
                    'summary',
                    'request',
                    'expiry',
                    'transaction',
                    'export'
                ],
            ],

            'menu' => [
                'access' => [
                    'dashboard',
                    'accession',
                    'lot',
                    'storage',
                    'dispatch',
                    'request',
                    'reports',
                    'masters',
                    'master_settings',
                    'settings',
                    'logs',
                    'sync',
                ],
            ],

            'settings' => [
                'admin' => [
                    'view',
                    'users',
                    'roles',
                    'permissions',
                    'masters',
                    'logs',
                ],
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

        // Create permissions
        foreach ($modules as $module => $groups) {

            foreach ($groups as $group => $actions) {

                foreach ($actions as $action) {

                    $slug = $module . '.' . $action;

                    Permission::firstOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => ucwords(str_replace(['.', '_'], ' ', $slug)),
                            'description' => ucwords(str_replace(['.', '_'], ' ', $slug)),
                        ]
                    );
                }
            }
        }

        // Roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full access'
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Admin access'
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manager access'
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Basic user'
            ],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(
                ['slug' => $r['slug']],
                $r
            );
        }

        // Super admin gets all permissions
        $superAdmin = Role::where('slug', 'super-admin')->first();

        if ($superAdmin) {
            $superAdmin->permissions()->sync(
                Permission::pluck('id')
            );
        }
    }
}
