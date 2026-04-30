<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accessions', function (Blueprint $table) {
            $table->unsignedSmallInteger('regen_year')->nullable()->after('sample_id');
        });
    }

    public function down(): void
    {
        Schema::table('accessions', function (Blueprint $table) {
            $table->dropColumn('regen_year');
        });
    }
};
