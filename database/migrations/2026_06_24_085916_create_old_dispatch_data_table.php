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
        Schema::create('old_dispatch_data', function (Blueprint $table) {
            $table->id();

            $table->string('crop')->nullable();
            $table->string('month')->nullable();
            $table->year('year')->nullable();
            $table->string('prefix')->nullable();
            $table->string('sample_id')->nullable();

            $table->string('seed_weight')->nullable(); // No. of Seeds / Weight (kg)
            $table->integer('no_packets')->nullable();

            $table->text('remarks')->nullable();
            $table->string('concerned_person')->nullable();
            $table->string('location')->nullable();

            $table->date('request_date')->nullable();
            $table->date('dispatch_date')->nullable();

            $table->string('tracking_id')->nullable();
            $table->string('courier_service')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_dispatch_data');
    }
};
