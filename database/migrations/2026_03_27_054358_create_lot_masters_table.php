<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lot_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->unsignedBigInteger('lot_type_id')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Add lot_master_id to lots table
        Schema::table('lots', function (Blueprint $table) {
            $table->unsignedBigInteger('lot_master_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn('lot_master_id');
        });
        Schema::dropIfExists('lot_masters');
    }
};
