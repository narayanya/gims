<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pouches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable()->unique();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Seed defaults
        \Illuminate\Support\Facades\DB::table('pouches')->insert([
            ['name' => 'Small Pouch',  'code' => 'SP', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medium Pouch', 'code' => 'MP', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Large Pouch',  'code' => 'LP', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pouches');
    }
};
