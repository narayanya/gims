<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions'
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access to manage users and system settings'
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manage inventory, accessions, and reports'
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Basic user with limited access to view data'
            ],
            [
                'name' => 'Researcher',
                'slug' => 'researcher',
                'description' => 'Basic researcher with limited access to view data'
            ],
            [
                'name' => 'Dispatcher',
                'slug' => 'dispatcher',
                'description' => 'Basic dispatcher with limited access to view data'
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
