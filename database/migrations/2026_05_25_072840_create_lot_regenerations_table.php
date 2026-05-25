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
        Schema::create('lot_regenerations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')
                ->constrained('lots')
                ->cascadeOnDelete();

            // old values
            $table->string('old_regen_year')->nullable();

            $table->date('old_expiry_date')->nullable();

            $table->date('old_regeneration_date')->nullable();

            // new values
            $table->string('regen_year')->nullable();

            $table->date('expiry_date')->nullable();

            $table->date('regeneration_date')->nullable();

            $table->text('reason')->nullable();

            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_regenerations');
    }
};
