<?php

namespace Database\Seeders;

use App\Models\Accession;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first crop and variety IDs for sample data
        $wheatCrop = \App\Models\Crop::firstOrCreate(['name' => 'Wheat', 'code' => 'WHT']);
        $riceCrop = \App\Models\Crop::firstOrCreate(['name' => 'Rice', 'code' => 'RIC']);
        $maizeCrop = \App\Models\Crop::firstOrCreate(['name' => 'Maize', 'code' => 'MAZ']);
        $barleyCrop = \App\Models\Crop::firstOrCreate(['name' => 'Barley', 'code' => 'BAR']);
        $sorghumCrop = \App\Models\Crop::firstOrCreate(['name' => 'Sorghum', 'code' => 'SOR']);
        
        $variety1 = \App\Models\Variety::firstOrCreate(['name' => 'HD-2967', 'crop_id' => $wheatCrop->id]);
        $variety2 = \App\Models\Variety::firstOrCreate(['name' => 'IR-64', 'crop_id' => $riceCrop->id]);
        $variety3 = \App\Models\Variety::firstOrCreate(['name' => 'DKC-9090', 'crop_id' => $maizeCrop->id]);
        $variety4 = \App\Models\Variety::firstOrCreate(['name' => 'BH-906', 'crop_id' => $barleyCrop->id]);
        $variety5 = \App\Models\Variety::firstOrCreate(['name' => 'CSH-16', 'crop_id' => $sorghumCrop->id]);
        $variety6 = \App\Models\Variety::firstOrCreate(['name' => 'PBW-725', 'crop_id' => $wheatCrop->id]);
        
        $warehouse1 = \App\Models\Warehouse::firstOrCreate(['name' => 'Main Warehouse', 'code' => 'WH1']);
        $warehouse2 = \App\Models\Warehouse::firstOrCreate(['name' => 'Cold Storage', 'code' => 'WH2']);
        $warehouse3 = \App\Models\Warehouse::firstOrCreate(['name' => 'Long-term Storage', 'code' => 'WH3']);
        
        $accessions = [
            [
                'accession_number' => 'ACC-2026-0001',
                'accession_name' => 'HD-2967 Punjab Collection',
                'crop_id' => $wheatCrop->id,
                'variety_id' => $variety1->id,
                'accession_type' => 'Seed',
                'quantity' => 500.00,
                'quantity_unit' => 'Bag',
                'capacity' => 500.00,
                'warehouse_id' => $warehouse1->id,
                'shelf_rack_box' => 'Room A-01, Shelf 1',
                'collection_date' => '2026-01-15',
                'collector_name' => 'Dr. Sarah Johnson',
                'donor_name' => 'Research Station Alpha',
                'status' => 'Active',
                'notes' => 'High-yielding wheat variety resistant to rust disease. Collected from Punjab region.',
                'barcode_type' => 'auto',
                'barcode' => 'ACC-2026-0001',
                'entry_date' => '2026-01-15',
                'created_by' => User::where('email', 'admin@example.com')->first()?->id,
            ],
            [
                'accession_number' => 'ACC-2026-0002',
                'accession_name' => 'IR-64 Drought Tolerant',
                'crop_id' => $riceCrop->id,
                'variety_id' => $variety2->id,
                'accession_type' => 'Seed',
                'quantity' => 250.00,
                'quantity_unit' => 'Bag',
                'capacity' => 250.00,
                'warehouse_id' => $warehouse2->id,
                'shelf_rack_box' => 'Cold Room B-02, Bin 3',
                'collection_date' => '2026-02-10',
                'collector_name' => 'Mr. Rajesh Kumar',
                'donor_name' => 'Farmer Field Collection',
                'status' => 'Active',
                'notes' => 'Popular rice variety known for drought tolerance. Requires cold storage.',
                'barcode_type' => 'auto',
                'barcode' => 'ACC-2026-0002',
                'entry_date' => '2026-02-10',
                'created_by' => User::where('email', 'manager@example.com')->first()?->id,
            ],
            [
                'accession_number' => 'ACC-2026-0003',
                'accession_name' => 'DKC-9090 QPM',
                'crop_id' => $maizeCrop->id,
                'variety_id' => $variety3->id,
                'accession_type' => 'Seed',
                'quantity' => 300.00,
                'quantity_unit' => 'Bag',
                'capacity' => 300.00,
                'warehouse_id' => $warehouse1->id,
                'shelf_rack_box' => 'Room A-03, Shelf 2',
                'collection_date' => '2026-01-28',
                'collector_name' => 'Dr. Michael Chen',
                'donor_name' => 'Breeding Program',
                'status' => 'Active',
                'notes' => 'Quality protein maize variety with enhanced nutritional value.',
                'barcode_type' => 'auto',
                'barcode' => 'ACC-2026-0003',
                'entry_date' => '2026-01-28',
                'created_by' => User::where('email', 'user@example.com')->first()?->id,
            ],
            [
                'accession_number' => 'ACC-2026-0004',
                'accession_name' => 'BH-906 Malting Barley',
                'crop_id' => $barleyCrop->id,
                'variety_id' => $variety4->id,
                'accession_type' => 'Seed',
                'quantity' => 150.00,
                'quantity_unit' => 'Bag',
                'capacity' => 150.00,
                'warehouse_id' => $warehouse3->id,
                'shelf_rack_box' => 'Long-term Storage C-01',
                'collection_date' => '2026-02-05',
                'collector_name' => 'Ms. Priya Sharma',
                'donor_name' => 'Regional Collection Center',
                'status' => 'Active',
                'notes' => 'Two-row barley variety suitable for malting. Stored for long-term preservation.',
                'barcode_type' => 'auto',
                'barcode' => 'ACC-2026-0004',
                'entry_date' => '2026-02-05',
                'created_by' => User::where('email', 'admin@example.com')->first()?->id,
            ],
            [
                'accession_number' => 'ACC-2026-0005',
                'accession_name' => 'CSH-16 Sweet Sorghum',
                'crop_id' => $sorghumCrop->id,
                'variety_id' => $variety5->id,
                'accession_type' => 'Seed',
                'quantity' => 200.00,
                'quantity_unit' => 'Bag',
                'capacity' => 200.00,
                'warehouse_id' => $warehouse1->id,
                'shelf_rack_box' => 'Room A-02, Shelf 4',
                'collection_date' => '2026-02-20',
                'collector_name' => 'Dr. Ahmed Hassan',
                'donor_name' => 'Imported Germplasm',
                'status' => 'Archived',
                'notes' => 'Sweet sorghum variety under quarantine for pest screening.',
                'barcode_type' => 'auto',
                'barcode' => 'ACC-2026-0005',
                'entry_date' => '2026-02-20',
                'created_by' => User::where('email', 'manager@example.com')->first()?->id,
            ],
            [
                'accession_number' => 'ACC-2026-0006',
                'accession_name' => 'PBW-725 Research Stock',
                'crop_id' => $wheatCrop->id,
                'variety_id' => $variety6->id,
                'accession_type' => 'Seed',
                'quantity' => 0.00,
                'quantity_unit' => 'Bag',
                'capacity' => 0.00,
                'warehouse_id' => $warehouse1->id,
                'shelf_rack_box' => 'Room A-01, Shelf 3',
                'collection_date' => '2026-01-20',
                'collector_name' => 'Dr. Lisa Wong',
                'donor_name' => 'Research Station Beta',
                'status' => 'Archived',
                'notes' => 'Previously active accession that has been fully utilized in research.',
                'barcode_type' => 'auto',
                'barcode' => 'ACC-2026-0006',
                'entry_date' => '2026-01-20',
                'created_by' => User::where('email', 'user@example.com')->first()?->id,
            ],
        ];

        foreach ($accessions as $accession) {
            Accession::create($accession);
        }
    }
}
