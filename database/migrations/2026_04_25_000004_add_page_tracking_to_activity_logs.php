<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('page_url', 500)->nullable()->after('user_agent');
            $table->string('page_title', 255)->nullable()->after('page_url');
            $table->timestamp('in_time')->nullable()->after('page_title');
            $table->timestamp('out_time')->nullable()->after('in_time');
            $table->unsignedInteger('time_spent_seconds')->nullable()->after('out_time');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['page_url', 'page_title', 'in_time', 'out_time', 'time_spent_seconds']);
        });
    }
};
