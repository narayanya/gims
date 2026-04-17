<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core_crop', function (Blueprint $table) {


            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('crop_category_id')->nullable();
            $table->unsignedBigInteger('crop_type_id')->nullable();
            $table->unsignedBigInteger('season_id')->nullable();

            $table->string('family_name')->nullable();
            $table->string('genus')->nullable();
            $table->string('species')->nullable();

            $table->integer('duration_days')->nullable();
            $table->string('sowing_time')->nullable();
            $table->string('harvest_time')->nullable();

            $table->string('climate_requirement')->nullable();

            $table->integer('soil_type_id'); // NOT NULL (as per your table)

            $table->integer('isolation_distance')->nullable();
            $table->decimal('expected_yield', 8, 2)->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('core_crop', function (Blueprint $table) {

            $table->dropColumn([
                'category_id',
                'crop_category_id',
                'crop_type_id',
                'season_id',
                'family_name',
                'genus',
                'species',
                'duration_days',
                'sowing_time',
                'harvest_time',
                'climate_requirement',
                'soil_type_id',
                'isolation_distance',
                'expected_yield',
            ]);

        });
    }
};