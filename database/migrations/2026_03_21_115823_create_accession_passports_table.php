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
        Schema::create('accession_passports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accession_id')->constrained()->onDelete('cascade');

            $table->string('sample_name')->nullable();   // sample1, sample2
            $table->string('sample_name_o')->nullable();   // sample1, sample2
            $table->string('passport_no')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accession_passports');
    }
};
