<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->string('lot_number')->nullable()->after('id');
            $table->unsignedBigInteger('storage_id')->nullable()->after('storage_location_id');
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn(['lot_number', 'storage_id']);
        });
    }
};
