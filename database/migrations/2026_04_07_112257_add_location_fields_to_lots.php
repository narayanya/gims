<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            if (!Schema::hasColumn('lots', 'section_id'))   $table->unsignedBigInteger('section_id')->nullable()->after('storage_id');
            if (!Schema::hasColumn('lots', 'rack_id'))      $table->unsignedBigInteger('rack_id')->nullable()->after('section_id');
            if (!Schema::hasColumn('lots', 'bin_id'))       $table->unsignedBigInteger('bin_id')->nullable()->after('rack_id');
            if (!Schema::hasColumn('lots', 'container_id')) $table->unsignedBigInteger('container_id')->nullable()->after('bin_id');
            if (!Schema::hasColumn('lots', 'expiry_date'))  $table->date('expiry_date')->nullable()->after('container_id');
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('lots','section_id')   ? 'section_id'   : null,
                Schema::hasColumn('lots','rack_id')      ? 'rack_id'      : null,
                Schema::hasColumn('lots','bin_id')       ? 'bin_id'       : null,
                Schema::hasColumn('lots','container_id') ? 'container_id' : null,
                Schema::hasColumn('lots','expiry_date')  ? 'expiry_date'  : null,
            ]));
        });
    }
};
