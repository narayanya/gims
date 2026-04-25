<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // warehouse_transfers already has batch_id, status, quantity, remarks — skip

        Schema::table('itns', function (Blueprint $table) {
            if (!Schema::hasColumn('itns', 'batch_id')) {
                $table->string('batch_id')->nullable()->after('transfer_id')->index();
            }
            // make transfer_id nullable for batch ITNs
            $table->unsignedBigInteger('transfer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('itns', function (Blueprint $table) {
            if (Schema::hasColumn('itns', 'batch_id')) {
                $table->dropColumn('batch_id');
            }
        });
    }
};
