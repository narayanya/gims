<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, temporarily change status to varchar to allow updates
        DB::statement("ALTER TABLE accessions MODIFY COLUMN status VARCHAR(50)");
        
        // Update existing status values to match new enum
        DB::statement("UPDATE accessions SET status = 'Active' WHERE status IN ('active', 'inactive', 'testing')");
        DB::statement("UPDATE accessions SET status = 'Archived' WHERE status IN ('quarantine', 'depleted')");
        
        // Rename accession_id to accession_number using raw SQL
        DB::statement("ALTER TABLE accessions CHANGE COLUMN accession_id accession_number VARCHAR(255) NOT NULL");
        
        Schema::table('accessions', function (Blueprint $table) {
            // Add new basic information fields
            $table->string('accession_name')->after('accession_number')->nullable();
            $table->string('scientific_name')->after('accession_name')->nullable();
            
            // Change crop and variety to foreign keys
            $table->unsignedBigInteger('crop_id')->after('scientific_name')->nullable();
            $table->foreign('crop_id')->references('id')->on('crops')->onDelete('set null');
            
            $table->unsignedBigInteger('variety_id')->after('crop_id')->nullable();
            $table->foreign('variety_id')->references('id')->on('varieties')->onDelete('set null');
            
            $table->enum('accession_type', ['Seed', 'Tissue', 'Clone', 'Plant'])->after('variety_id')->nullable();
            
            // Collection Information
            $table->string('collection_number', 100)->after('accession_type')->nullable();
            $table->string('collector_name')->after('collection_date')->nullable();
            $table->string('donor_name')->after('collector_name')->nullable();
            $table->string('collection_site')->after('donor_name')->nullable();
            $table->foreignId('country_id')->after('collection_site')->nullable()->constrained('countries')->onDelete('set null');
            $table->foreignId('state_id')->after('country_id')->nullable()->constrained('states')->onDelete('set null');
            $table->string('district', 100)->after('state_id')->nullable();
            $table->string('village', 100)->after('district')->nullable();
            $table->decimal('latitude', 10, 8)->after('village')->nullable();
            $table->decimal('longitude', 11, 8)->after('latitude')->nullable();
            $table->integer('altitude')->after('longitude')->nullable()->comment('in meters');
            
            // Biological/Genetic Information
            $table->enum('biological_status', ['Wild', 'Landrace', 'Breeding Material', 'Improved Variety'])->after('altitude')->nullable();
            $table->enum('sample_type', ['Seed', 'Plant', 'Tissue'])->after('biological_status')->nullable();
            $table->enum('reproductive_type', ['Self Pollinated', 'Cross Pollinated'])->after('sample_type')->nullable();
            
            // Quantity Information (update existing)
            $table->enum('quantity_unit', ['Nos', 'Packet', 'Bag'])->after('quantity')->nullable();
            $table->decimal('capacity', 10, 2)->after('quantity_unit')->nullable();
            $table->foreignId('capacity_unit_id')->after('capacity')->nullable()->constrained('units')->onDelete('set null');
            
            // Storage Information (update existing)
            $table->unsignedBigInteger('warehouse_id')->after('capacity_unit_id')->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
            
            $table->foreignId('storage_location_id')->after('warehouse_id')->nullable()->constrained('storage_locations')->onDelete('set null');
            $table->string('shelf_rack_box', 100)->after('storage_location_id')->nullable();
            $table->foreignId('storage_type_id')->after('shelf_rack_box')->nullable()->constrained('storage_types')->onDelete('set null');
            
            // Seed Quality Information
            $table->decimal('germination_percentage', 5, 2)->after('storage_type_id')->nullable();
            $table->decimal('moisture_content', 5, 2)->after('germination_percentage')->nullable();
            $table->decimal('purity_percentage', 5, 2)->after('moisture_content')->nullable();
            $table->date('viability_test_date')->after('purity_percentage')->nullable();
            $table->enum('seed_health_status', ['Healthy', 'Infected', 'Damaged', 'Under Treatment'])->after('viability_test_date')->nullable();
            
            // Documentation
            $table->enum('barcode_type', ['auto', 'manual', 'existing', 'scan', 'none'])->after('seed_health_status')->default('auto');
            $table->string('barcode', 100)->after('barcode_type')->nullable()->unique();
            $table->string('image_path')->after('barcode')->nullable();
            $table->string('passport_file_path')->after('image_path')->nullable();
            $table->text('notes')->after('passport_file_path')->nullable();
            
            // System Fields
            $table->date('entry_date')->after('notes')->nullable();
            $table->foreignId('entered_by')->after('entry_date')->nullable()->constrained('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index('accession_number');
            $table->index('barcode');
            $table->index('entry_date');
        });
        
        // Now change status to new enum
        DB::statement("ALTER TABLE accessions MODIFY COLUMN status ENUM('Active', 'Archived') NOT NULL DEFAULT 'Active'");
        
        // Drop old columns that are replaced by foreign keys
        Schema::table('accessions', function (Blueprint $table) {
            $table->dropColumn(['crop', 'variety', 'unit', 'warehouse', 'location', 'source', 'collector', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accessions', function (Blueprint $table) {
            // Add back old columns
            $table->string('crop')->nullable();
            $table->string('variety')->nullable();
            $table->string('unit')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('location')->nullable();
            $table->string('source')->nullable();
            $table->string('collector')->nullable();
            $table->enum('priority', ['normal', 'high', 'critical'])->default('normal');
        });
        
        Schema::table('accessions', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['accession_number']);
            $table->dropIndex(['barcode']);
            $table->dropIndex(['entry_date']);
            
            // Drop foreign keys first
            $table->dropForeign(['crop_id']);
            $table->dropForeign(['variety_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['capacity_unit_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['storage_location_id']);
            $table->dropForeign(['storage_type_id']);
            $table->dropForeign(['entered_by']);
            
            // Drop columns
            $table->dropColumn([
                'entered_by',
                'entry_date',
                'notes',
                'passport_file_path',
                'image_path',
                'barcode',
                'barcode_type',
                'seed_health_status',
                'viability_test_date',
                'purity_percentage',
                'moisture_content',
                'germination_percentage',
                'storage_type_id',
                'shelf_rack_box',
                'storage_location_id',
                'warehouse_id',
                'capacity_unit_id',
                'capacity',
                'quantity_unit',
                'reproductive_type',
                'sample_type',
                'biological_status',
                'altitude',
                'longitude',
                'latitude',
                'village',
                'district',
                'state_id',
                'country_id',
                'collection_site',
                'donor_name',
                'collector_name',
                'collection_number',
                'accession_type',
                'variety_id',
                'crop_id',
                'scientific_name',
                'accession_name',
            ]);
            
            // Rename back
            $table->renameColumn('accession_number', 'accession_id');
            
            // Restore original status enum
            $table->enum('status', ['active', 'inactive', 'quarantine', 'depleted', 'testing'])->default('active')->change();
        });
    }
};
