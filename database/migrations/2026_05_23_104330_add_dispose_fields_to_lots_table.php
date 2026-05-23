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
        Schema::table('lots', function (Blueprint $table) {
              $table->date('dispose_date')->nullable();

            $table->string('dispose_type')->nullable();

            $table->text('dispose_reason')->nullable();
            $table->date('regeneration_date')
                  ->nullable()
                  ->after('dispose_reason');

            $table->string('regen_year')
                  ->nullable()
                  ->after('regeneration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
                $table->dropColumn([
                'regeneration_date',
                'regen_year'
            ]);
        });
    }
};
