<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->decimal('return_quantity', 10, 2)->nullable()->after('receive_date');
            $table->text('return_remarks')->nullable()->after('return_quantity');
            $table->date('return_date')->nullable()->after('return_remarks');
        });

        // Add 'returned' to status enum
        DB::statement("ALTER TABLE requests MODIFY status ENUM('pending','approved','rejected','dispatched','completed','received','returned') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['return_quantity', 'return_remarks', 'return_date']);
        });
        DB::statement("ALTER TABLE requests MODIFY status ENUM('pending','approved','rejected','dispatched','completed','received') NOT NULL DEFAULT 'pending'");
    }
};
