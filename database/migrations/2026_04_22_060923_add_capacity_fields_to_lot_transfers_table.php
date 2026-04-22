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
        Schema::table('lot_transfers', function (Blueprint $table) {
            $table->integer('f_available_capacity')->nullable()->after('to_container_id');
            $table->integer('f_quantity')->nullable()->after('f_available_capacity');
            $table->integer('f_balance_capacity')->nullable()->after('f_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lot_transfers', function (Blueprint $table) {
            $table->dropColumn([
                'f_available_capacity',
                'f_quantity',
                'f_balance_capacity'
            ]);
        });
    }
};
