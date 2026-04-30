<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arrival_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Seed defaults
        \Illuminate\Support\Facades\DB::table('arrival_types')->insert([
            ['name' => 'Rejuvenation',       'code' => 'RJV', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accession Arrival',  'code' => 'ACC', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Return From Field',  'code' => 'RFF', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('arrival_types');
    }
};
