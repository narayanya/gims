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
        Schema::create('seed_quantities', function (Blueprint $table) {
            $table->id();
            $table->integer('number_of_seeds')->nullable();
        $table->decimal('per_seed_weight', 10, 4)->nullable();

        $table->decimal('quantity', 10, 2)->nullable();
        $table->unsignedBigInteger('capacity_unit_id')->nullable();

        $table->decimal('quantity_show', 10, 2)->nullable();
        $table->decimal('min_quantity', 10, 2)->nullable();

        $table->unsignedBigInteger('lot_id')->nullable();
        $table->integer('in_seed')->default(0);
        $table->integer('out_seed')->default(0);
        $table->integer('return_seed')->default(0);

        $table->unsignedBigInteger('accession_id')->nullable();

        $table->timestamps();

        // Foreign Keys
        $table->foreign('capacity_unit_id')->references('id')->on('units')->nullOnDelete();
        $table->foreign('lot_id')->references('id')->on('lots')->nullOnDelete();
        $table->foreign('accession_id')->references('id')->on('accessions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seed_quantities');
    }
};
