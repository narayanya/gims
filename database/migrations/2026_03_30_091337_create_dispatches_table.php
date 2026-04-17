<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('request_id'); // link to request
            $table->unsignedBigInteger('accession_id')->nullable();

            $table->string('mrn_number')->unique();
            $table->decimal('quantity', 10, 2);

            $table->string('courier_name')->nullable();
            $table->string('tracking_number')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamp('dispatched_at')->nullable();

            $table->timestamps();

            // foreign keys
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
            $table->foreign('accession_id')->references('id')->on('accessions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
