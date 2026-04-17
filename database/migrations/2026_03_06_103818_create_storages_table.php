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
        Schema::create('storages', function (Blueprint $table) {
            $table->id();
            $table->string('storage_id')->unique();
            $table->string('name');
            $table->string('type')->default('warehouse'); // warehouse, freezer, cabinet, etc.
            $table->string('location')->nullable();
            $table->decimal('capacity', 10, 2)->nullable(); // capacity in appropriate units
            $table->string('unit')->default('kg'); // kg, liters, boxes, etc.
            $table->decimal('current_usage', 10, 2)->default(0);
            $table->string('temperature')->nullable(); // for cold storage
            $table->string('humidity')->nullable(); // for controlled environments
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->unsignedBigInteger('managed_by')->nullable(); // user who manages this storage
            $table->foreign('managed_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storages');
    }
};
