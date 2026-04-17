<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accession_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accession_id');
            $table->string('image_name');       // filename only
            $table->tinyInteger('is_primary')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accession_images');
    }
};
