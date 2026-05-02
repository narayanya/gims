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
            $table->decimal('o_quantity', 10, 2)->nullable()->after('quantity');
            $table->decimal('c_quantity', 10, 2)->nullable()->after('o_quantity');
            $table->decimal('b_quantity', 10, 2)->nullable()->after('c_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lot_transfers', function (Blueprint $table) {
            $table->dropColumn(['o_quantity', 'c_quantity', 'b_quantity']);
        });
    }
};
