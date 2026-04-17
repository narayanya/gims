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
        Schema::table('varieties', function (Blueprint $table) {
            $table->foreignId('variety_type_id')->nullable()->constrained('variety_types');

            $table->foreignId('seed_class_id')->nullable()->constrained('seed_classes');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('varieties', function (Blueprint $table) {
            //
        });
    }
};
