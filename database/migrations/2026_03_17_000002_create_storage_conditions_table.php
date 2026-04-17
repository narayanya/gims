<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // e.g. "Refrigerated", "Ambient", "Frozen"
            $table->string('code')->nullable()->unique(); // e.g. "REF", "AMB", "FRZ"
            $table->decimal('temp_min', 6, 2)->nullable(); // Min temperature (°C)
            $table->decimal('temp_max', 6, 2)->nullable(); // Max temperature (°C)
            $table->decimal('humidity_min', 5, 2)->nullable(); // Min humidity (%)
            $table->decimal('humidity_max', 5, 2)->nullable(); // Max humidity (%)
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_conditions');
    }
};
