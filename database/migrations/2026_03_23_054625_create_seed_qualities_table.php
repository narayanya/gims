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
        Schema::create('seed_qualities', function (Blueprint $table) {
            $table->id();
            // Relation with accession
            $table->foreignId('accession_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Seed Quality Fields
            $table->decimal('germination_percentage', 5, 2)->nullable();
            $table->decimal('moisture_content', 5, 2)->nullable();
            $table->decimal('purity_percentage', 5, 2)->nullable();
            $table->date('viability_test_date')->nullable();
            $table->string('seed_health_status')->nullable();

            // Researcher
            $table->foreignId('researcher_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('researcher_other')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seed_qualities');
    }
};
