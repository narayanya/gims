<?php

namespace Database\Seeders;

use App\Models\StorageType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StorageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $storageTypes = [
            [
                'name' => 'Warehouse',
                'description' => 'Large storage facility for bulk germplasm materials',
            ],
            [
                'name' => 'Freezer',
                'description' => 'Temperature-controlled cold storage for sensitive materials',
            ],
            [
                'name' => 'Cabinet',
                'description' => 'Small enclosed storage unit for organized sample storage',
            ],
            [
                'name' => 'Room',
                'description' => 'Dedicated room or space for storage purposes',
            ],
            [
                'name' => 'Shelf',
                'description' => 'Open shelving system for accessible storage',
            ],
            [
                'name' => 'Refrigerator',
                'description' => 'Cool storage for moderately sensitive materials',
            ],
            [
                'name' => 'Dry Storage',
                'description' => 'Climate-controlled dry environment storage',
            ],
            [
                'name' => 'Field Collection',
                'description' => 'Temporary storage for field-collected samples',
            ],
        ];

        foreach ($storageTypes as $type) {
            StorageType::create($type);
        }
    }
}
