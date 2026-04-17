<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accessions', function (Blueprint $table) {
            if (!Schema::hasColumn('accessions', 'scientific_name')) {
                $table->string('scientific_name')->nullable()->after('variety_id');
            }
            if (!Schema::hasColumn('accessions', 'altitude')) {
                $table->integer('altitude')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('accessions', 'storage_location_id')) {
                $table->unsignedBigInteger('storage_location_id')->nullable()->after('warehouse_id');
            }
            if (!Schema::hasColumn('accessions', 'storage_time_id')) {
                $table->unsignedBigInteger('storage_time_id')->nullable()->after('storage_location_id');
            }
            if (!Schema::hasColumn('accessions', 'storage_condition_id')) {
                $table->unsignedBigInteger('storage_condition_id')->nullable()->after('storage_time_id');
            }
            if (!Schema::hasColumn('accessions', 'storage_type_id')) {
                $table->unsignedBigInteger('storage_type_id')->nullable()->after('storage_condition_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accessions', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('accessions', 'scientific_name')    ? 'scientific_name'    : null,
                Schema::hasColumn('accessions', 'altitude')           ? 'altitude'           : null,
                Schema::hasColumn('accessions', 'storage_location_id')? 'storage_location_id': null,
                Schema::hasColumn('accessions', 'storage_time_id')    ? 'storage_time_id'    : null,
                Schema::hasColumn('accessions', 'storage_condition_id')? 'storage_condition_id': null,
                Schema::hasColumn('accessions', 'storage_type_id')    ? 'storage_type_id'    : null,
            ]));
        });
    }
};
