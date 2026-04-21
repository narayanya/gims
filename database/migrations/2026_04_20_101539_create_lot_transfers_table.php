<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lot_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lot_id');
            $table->unsignedBigInteger('from_storage_id')->nullable();
            $table->unsignedBigInteger('to_storage_id');
            $table->unsignedBigInteger('from_section_id')->nullable();
            $table->unsignedBigInteger('to_section_id')->nullable();
            $table->unsignedBigInteger('from_rack_id')->nullable();
            $table->unsignedBigInteger('to_rack_id')->nullable();
            $table->unsignedBigInteger('from_bin_id')->nullable();
            $table->unsignedBigInteger('to_bin_id')->nullable();
            $table->unsignedBigInteger('from_container_id')->nullable();
            $table->unsignedBigInteger('to_container_id')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('transferred_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lot_transfers');
    }
};
