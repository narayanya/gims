<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('receive_status')->nullable()->after('status');
            $table->text('receive_remarks')->nullable()->after('receive_status');
            $table->date('receive_date')->nullable()->after('receive_remarks');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['receive_status', 'receive_remarks', 'receive_date']);
        });
    }
};
