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
             // foreign key remove
            $table->dropForeign(['variety_id']);

            // column remove
            $table->dropColumn('variety_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->unsignedBigInteger('variety_id')->nullable();

            $table->foreign('variety_id')
                  ->references('id')
                  ->on('varieties')
                  ->nullOnDelete();

        });
    }
};
