<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accession_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('rule_type')->nullable(); // e.g. Quantity, Quality, Storage, General
            $table->text('description')->nullable();
            $table->string('min_value')->nullable();
            $table->string('max_value')->nullable();
            $table->string('unit')->nullable();
            $table->tinyInteger('is_mandatory')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accession_rules');
    }
};
