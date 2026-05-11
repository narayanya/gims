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
        Schema::table('core_crop', function (Blueprint $table) {
            $table->unsignedBigInteger('pouch_standard_id')
                  ->nullable()
                  ->after('species');

            $table->integer('seed_quantity')
                  ->nullable()
                  ->after('pouch_standard_id');

            $table->decimal('seed_weight', 10, 2)
                  ->nullable()
                  ->after('seed_quantity');

            $table->unsignedBigInteger('unit_id')
                  ->nullable()
                  ->after('seed_weight');

            $table->unsignedBigInteger('season_start_month_id')
                  ->nullable()
                  ->after('unit_id');

            $table->unsignedBigInteger('season_end_month_id')
                  ->nullable()
                  ->after('season_start_month_id');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_crop', function (Blueprint $table) {
            //
            $table->dropColumn([
                'pouch_standard_id',
                'seed_quantity',
                'seed_weight',
                'unit_id',
                'season_start_month_id',
                'season_end_month_id'
            ]);
        });
    }
};
