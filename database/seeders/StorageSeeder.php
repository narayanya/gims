<?php

namespace Database\Seeders;

use App\Models\Storage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $storages = [
            [
                'storage_id' => 'STG-2024-0001',
                'name' => 'Main Warehouse A',
                'type' => 'warehouse',
                'location' => 'Building A, Ground Floor',
                'capacity' => 5000.00,
                'unit' => 'kg',
                'current_usage' => 1250.50,
                'temperature' => 'Room Temperature',
                'humidity' => '40-60%',
                'description' => 'Primary storage warehouse for bulk germplasm materials',
                'status' => 'active',
                'managed_by' => User::where('email', 'admin@example.com')->first()?->id,
            ],
            [
                'storage_id' => 'STG-2024-0002',
                'name' => 'Cold Storage Unit 1',
                'type' => 'freezer',
                'location' => 'Building B, Basement',
                'capacity' => 2000.00,
                'unit' => 'kg',
                'current_usage' => 850.25,
                'temperature' => '-20°C',
                'humidity' => 'Controlled',
                'description' => 'Temperature-controlled storage for sensitive germplasm',
                'status' => 'active',
                'managed_by' => User::where('email', 'manager@example.com')->first()?->id,
            ],
            [
                'storage_id' => 'STG-2024-0003',
                'name' => 'Seed Cabinet Alpha',
                'type' => 'cabinet',
                'location' => 'Lab Room 101',
                'capacity' => 100.00,
                'unit' => 'kg',
                'current_usage' => 45.75,
                'temperature' => '22°C',
                'humidity' => '45%',
                'description' => 'Small-scale storage cabinet for active research samples',
                'status' => 'active',
                'managed_by' => User::where('email', 'user@example.com')->first()?->id,
            ],
            [
                'storage_id' => 'STG-2024-0004',
                'name' => 'Outdoor Storage Shed',
                'type' => 'warehouse',
                'location' => 'External Compound',
                'capacity' => 3000.00,
                'unit' => 'kg',
                'current_usage' => 1200.00,
                'temperature' => 'Ambient',
                'humidity' => 'Variable',
                'description' => 'Weather-protected outdoor storage for non-sensitive materials',
                'status' => 'active',
                'managed_by' => User::where('email', 'manager@example.com')->first()?->id,
            ],
            [
                'storage_id' => 'STG-2024-0005',
                'name' => 'Maintenance Bay',
                'type' => 'room',
                'location' => 'Building C, Wing 2',
                'capacity' => null,
                'unit' => 'units',
                'current_usage' => 0,
                'temperature' => 'Room Temperature',
                'humidity' => 'Standard',
                'description' => 'Storage area currently under maintenance',
                'status' => 'maintenance',
                'managed_by' => null,
            ],
        ];

        foreach ($storages as $storage) {
            Storage::create($storage);
        }
    }
}
