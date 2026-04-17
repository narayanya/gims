<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE requests MODIFY status ENUM('pending','approved','rejected','dispatched','completed','received') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("UPDATE requests SET status = 'completed' WHERE status = 'received'");
        DB::statement("ALTER TABLE requests MODIFY status ENUM('pending','approved','rejected','dispatched','completed') NOT NULL DEFAULT 'pending'");
    }
};
