<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gims.com',
            'password' => bcrypt('password123'),
        ]);
        $superAdmin->roles()->attach(Role::where('slug', 'super-admin')->first());

        // Create Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gims.com',
            'password' => bcrypt('password123'),
        ]);
        $admin->roles()->attach(Role::where('slug', 'admin')->first());

        // Create Manager
        $manager = User::create([
            'name' => 'John Manager',
            'email' => 'manager@gims.com',
            'password' => bcrypt('password123'),
        ]);
        $manager->roles()->attach(Role::where('slug', 'manager')->first());

        // Create Regular User
        $user = User::create([
            'name' => 'Jane User',
            'email' => 'user@gims.com',
            'password' => bcrypt('password123'),
        ]);
        $user->roles()->attach(Role::where('slug', 'user')->first());
    }
}
