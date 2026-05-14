<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seed_qualities', function (Blueprint $table) {
            $table->decimal('chlorophyll_percentage', 8, 2)
                  ->nullable()
                  ->after('purity_percentage');

            $table->decimal('water_level_percentage', 8, 2)
                  ->nullable()
                  ->after('chlorophyll_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seed_qualities', function (Blueprint $table) {
            $table->dropColumn([
                'chlorophyll_percentage',
                'water_level_percentage'
            ]);
        });
    }
};
