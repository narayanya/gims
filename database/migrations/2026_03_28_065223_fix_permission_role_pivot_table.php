<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop and recreate the pivot with proper columns
        Schema::drop('permission_role');
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->unique(['permission_id', 'role_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('permission_role');
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
