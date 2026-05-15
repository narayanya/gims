<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seed_qualities', function (Blueprint $table) {
            // Add lot_id after accession_id if it doesn't exist
            if (!Schema::hasColumn('seed_qualities', 'lot_id')) {
                $table->unsignedBigInteger('lot_id')->nullable()->after('accession_id');
                $table->foreign('lot_id')->references('id')->on('lots')->nullOnDelete();
            }

            // Add research_date if it doesn't exist
            if (!Schema::hasColumn('seed_qualities', 'research_date')) {
                $table->date('research_date')->nullable()->after('seed_health_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seed_qualities', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
            $table->dropColumn(['lot_id', 'research_date']);
        });
    }
};
