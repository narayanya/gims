<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convert existing int values to string equivalents first
        DB::statement("ALTER TABLE lots MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");
        DB::statement("UPDATE lots SET status = 'active'   WHERE status = '1'");
        DB::statement("UPDATE lots SET status = 'inactive' WHERE status = '0'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE lots MODIFY status INT(11) NOT NULL DEFAULT 1");
    }
};
