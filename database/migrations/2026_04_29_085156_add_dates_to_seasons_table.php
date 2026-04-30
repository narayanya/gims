<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seasons', function (Blueprint $table) {

            // Add new columns
            $table->tinyInteger('start_month')->nullable()->after('name');
            $table->tinyInteger('end_month')->nullable()->after('start_month');

            // Drop old columns (only if exist)
            if (Schema::hasColumn('seasons', 'start_date')) {
                $table->dropColumn('start_date');
            }

            if (Schema::hasColumn('seasons', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seasons', function (Blueprint $table) {

            // Recreate old columns
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Drop new columns
            $table->dropColumn(['start_month', 'end_month']);
        });
    }
};
