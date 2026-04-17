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
        Schema::create('accessions', function (Blueprint $table) {
            $table->id();
            $table->string('accession_id')->unique();
            $table->string('crop');
            $table->string('variety');
            $table->decimal('quantity', 10, 2);
            $table->string('unit');
            $table->string('warehouse');
            $table->string('location')->nullable();
            $table->date('collection_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'quarantine', 'depleted', 'testing'])->default('active');
            $table->enum('priority', ['normal', 'high', 'critical'])->default('normal');
            $table->string('source')->nullable();
            $table->string('collector')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // User who created the accession
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['crop', 'variety']);
            $table->index('status');
            $table->index('warehouse');
            $table->index('collection_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessions');
    }
};
