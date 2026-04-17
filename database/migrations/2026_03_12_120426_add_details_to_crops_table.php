<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('crops', function (Blueprint $table) {

            // Basic
            $table->string('scientific_name')->nullable();
            $table->string('common_name')->nullable();

            // Master Relations
            $table->foreignId('crop_category_id')->nullable()->constrained('crop_categories');
            $table->foreignId('crop_type_id')->nullable()->constrained('crop_types');
            $table->foreignId('season_id')->nullable()->constrained('seasons');

            // Classification
            $table->string('family_name')->nullable();
            $table->string('genus')->nullable();
            $table->string('species')->nullable();

            // Agronomy
            $table->integer('duration_days')->nullable();
            $table->string('sowing_time')->nullable();
            $table->string('harvest_time')->nullable();
            $table->string('climate_requirement')->nullable();
            $table->string('soil_type')->nullable();

            // Seed Production
            $table->decimal('seed_rate',8,2)->nullable();
            $table->integer('germination_percentage')->nullable();
            $table->integer('isolation_distance')->nullable();
            $table->decimal('expected_yield',8,2)->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn([
                'scientific_name',
                'common_name',
                'family_name',
                'genus',
                'species',
                'duration_days',
                'sowing_time',
                'harvest_time',
                'climate_requirement',
                'soil_type',
                'seed_rate',
                'germination_percentage',
                'isolation_distance',
                'expected_yield'
            ]);
        });
    }
};
