<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->renameColumn('name', 'state_name');
            $table->renameColumn('code', 'state_code');
        });

        Schema::table('states', function (Blueprint $table) {
            $table->string('short_code', 10)->nullable()->after('state_code');
            $table->date('effective_date')->nullable()->after('short_code');
            $table->tinyInteger('is_active')->default(1)->after('effective_date');
        });
    }

    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->dropColumn(['short_code', 'effective_date', 'is_active']);
        });
        Schema::table('states', function (Blueprint $table) {
            $table->renameColumn('state_name', 'name');
            $table->renameColumn('state_code', 'code');
        });
    }
};
