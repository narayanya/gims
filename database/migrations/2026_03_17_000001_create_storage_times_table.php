<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_times', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // e.g. "Short Term", "Long Term"
            $table->string('code')->nullable()->unique();  // e.g. "ST", "LT"
            $table->integer('duration_value')->nullable(); // e.g. 6
            $table->string('duration_unit')->nullable();   // months, years, days
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_times');
    }
};
