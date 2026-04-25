<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->unsignedBigInteger('request_id')->nullable()->change();
            $table->unsignedBigInteger('itn_id')->nullable()->change();
            $table->unsignedBigInteger('accession_id')->nullable()->change();
            $table->unsignedBigInteger('lot_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->unsignedBigInteger('request_id')->nullable(false)->change();
        });
    }
};
