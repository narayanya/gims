<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accessions', function (Blueprint $table) {
            if (!Schema::hasColumn('accessions', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('district_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accessions', function (Blueprint $table) {
            if (Schema::hasColumn('accessions', 'city_id')) {
                $table->dropColumn('city_id');
            }
        });
    }
};
