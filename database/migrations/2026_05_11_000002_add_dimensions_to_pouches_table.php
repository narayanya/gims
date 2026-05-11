<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pouches', function (Blueprint $table) {
            $table->decimal('length', 10, 2)->nullable()->after('code');
            $table->decimal('width', 10, 2)->nullable()->after('length');
            $table->decimal('height', 10, 2)->nullable()->after('width');
            $table->string('dimension_unit', 20)->default('cm')->after('height'); // cm, mm, inch
        });
    }

    public function down(): void
    {
        Schema::table('pouches', function (Blueprint $table) {
            $table->dropColumn(['length', 'width', 'height', 'dimension_unit']);
        });
    }
};
