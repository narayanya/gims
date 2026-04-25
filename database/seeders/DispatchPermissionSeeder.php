<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class DispatchPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['slug' => 'dispatch.view',   'name' => 'View Dispatch'],
            ['slug' => 'dispatch.create', 'name' => 'Create Dispatch'],
            ['slug' => 'dispatch.edit',   'name' => 'Edit Dispatch'],
            ['slug' => 'dispatch.delete', 'name' => 'Delete Dispatch'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                ['name' => $perm['name'], 'description' => $perm['name'] . ' access']
            );
        }
    }
}
