<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Unit;
use App\Models\CropCategory;
use App\Models\CropType;
use App\Models\VarietyType;
use App\Models\Season;
use App\Models\SeedClass;
use App\Models\StorageType;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Cereals', 'code' => 'CER', 'description' => 'Cereal crops including wheat, rice, maize'],
            ['name' => 'Pulses', 'code' => 'PUL', 'description' => 'Pulse crops including lentils, chickpeas'],
            ['name' => 'Oilseeds', 'code' => 'OIL', 'description' => 'Oilseed crops including sunflower, mustard'],
            ['name' => 'Vegetables', 'code' => 'VEG', 'description' => 'Vegetable crops'],
            ['name' => 'Fruits', 'code' => 'FRT', 'description' => 'Fruit crops'],
        ];
        foreach ($categories as $category) {
            Category::firstOrCreate(['code' => $category['code']], $category);
        }

        // Units
        $units = [
            ['name' => 'Kilogram', 'code' => 'kg', 'description' => 'Weight measurement unit'],
            ['name' => 'Gram', 'code' => 'g', 'description' => 'Weight measurement unit'],
            ['name' => 'Ton', 'code' => 't', 'description' => 'Weight measurement unit'],
            ['name' => 'Quintal', 'code' => 'q', 'description' => 'Weight measurement unit'],
            ['name' => 'Liter', 'code' => 'l', 'description' => 'Volume measurement unit'],
            ['name' => 'Milliliter', 'code' => 'ml', 'description' => 'Volume measurement unit'],
            ['name' => 'Piece', 'code' => 'pcs', 'description' => 'Count measurement unit'],
            ['name' => 'Packet', 'code' => 'pkt', 'description' => 'Count measurement unit'],
            ['name' => 'Bag', 'code' => 'bag', 'description' => 'Count measurement unit'],
        ];
        foreach ($units as $unit) {
            Unit::firstOrCreate(['code' => $unit['code']], $unit);
        }

        // Crop Categories
        $cropCategories = [
            ['name' => 'Food Crops', 'code' => 'FOOD', 'description' => 'Crops grown for human consumption'],
            ['name' => 'Cash Crops', 'code' => 'CASH', 'description' => 'Crops grown for commercial purposes'],
            ['name' => 'Fodder Crops', 'code' => 'FODD', 'description' => 'Crops grown for animal feed'],
            ['name' => 'Fiber Crops', 'code' => 'FIBR', 'description' => 'Crops grown for fiber production'],
        ];
        foreach ($cropCategories as $cropCategory) {
            CropCategory::firstOrCreate(['code' => $cropCategory['code']], $cropCategory);
        }

        // Crop Types
        $cropTypes = [
            ['name' => 'Annual', 'code' => 'ANN', 'description' => 'Crops that complete lifecycle in one year'],
            ['name' => 'Perennial', 'code' => 'PER', 'description' => 'Crops that live for multiple years'],
            ['name' => 'Biennial', 'code' => 'BIE', 'description' => 'Crops that complete lifecycle in two years'],
        ];
        foreach ($cropTypes as $cropType) {
            CropType::firstOrCreate(['code' => $cropType['code']], $cropType);
        }

        // Variety Types
        $varietyTypes = [
            ['name' => 'Hybrid', 'code' => 'HYB', 'description' => 'Hybrid varieties'],
            ['name' => 'Open Pollinated', 'code' => 'OP', 'description' => 'Open pollinated varieties'],
            ['name' => 'Landrace', 'code' => 'LR', 'description' => 'Traditional landrace varieties'],
            ['name' => 'Improved', 'code' => 'IMP', 'description' => 'Improved varieties'],
        ];
        foreach ($varietyTypes as $varietyType) {
            VarietyType::firstOrCreate(['code' => $varietyType['code']], $varietyType);
        }

        // Seasons
        $seasons = [
            ['name' => 'Kharif', 'code' => 'KHR', 'description' => 'Monsoon season (June-October)'],
            ['name' => 'Rabi', 'code' => 'RAB', 'description' => 'Winter season (November-March)'],
            ['name' => 'Zaid', 'code' => 'ZAI', 'description' => 'Summer season (March-June)'],
            ['name' => 'Year Round', 'code' => 'YR', 'description' => 'Can be grown throughout the year'],
        ];
        foreach ($seasons as $season) {
            Season::firstOrCreate(['code' => $season['code']], $season);
        }

        // Seed Classes
        $seedClasses = [
            ['name' => 'Breeder Seed', 'code' => 'BS', 'description' => 'Highest quality seed produced by breeder'],
            ['name' => 'Foundation Seed', 'code' => 'FS', 'description' => 'Progeny of breeder seed'],
            ['name' => 'Certified Seed', 'code' => 'CS', 'description' => 'Progeny of foundation seed'],
            ['name' => 'Truthfully Labeled', 'code' => 'TL', 'description' => 'Seed meeting minimum standards'],
        ];
        foreach ($seedClasses as $seedClass) {
            SeedClass::firstOrCreate(['code' => $seedClass['code']], $seedClass);
        }

        // Storage Types
        $storageTypes = [
            ['name' => 'Cold Storage', 'description' => 'Temperature controlled cold storage'],
            ['name' => 'Ambient Storage', 'description' => 'Normal room temperature storage'],
            ['name' => 'Dry Storage', 'description' => 'Low humidity dry storage'],
            ['name' => 'Refrigerated', 'description' => 'Refrigerated storage'],
        ];
        foreach ($storageTypes as $storageType) {
            StorageType::firstOrCreate(['name' => $storageType['name']], $storageType);
        }

        $this->command->info('Master data seeded successfully!');
    }
}
