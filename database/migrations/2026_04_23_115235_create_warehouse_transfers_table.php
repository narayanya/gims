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
        Schema::create('warehouse_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lot_id');
            $table->unsignedBigInteger('crop_id');
            $table->unsignedBigInteger('accession_id');
            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('to_warehouse_id');

            $table->unsignedBigInteger('from_storage_id');
            $table->unsignedBigInteger('to_storage_id');

            $table->timestamp('transferred_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable(); // optional (user)
            $table->timestamps();

            // Foreign keys (optional but recommended)
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();

            $table->foreign('from_storage_id')->references('id')->on('storages')->cascadeOnDelete();
            $table->foreign('to_storage_id')->references('id')->on('storages')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_transfers');
    }
};
