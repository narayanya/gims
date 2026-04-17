<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Column is currently tinyint(0/1), convert to enum string values
        DB::statement("ALTER TABLE accessions MODIFY requester_show VARCHAR(3) NULL");
        DB::statement("UPDATE accessions SET requester_show = 'yes' WHERE requester_show = '1' OR requester_show IS NULL OR requester_show NOT IN ('yes','no')");
        DB::statement("UPDATE accessions SET requester_show = 'no'  WHERE requester_show = '0'");
        DB::statement("ALTER TABLE accessions MODIFY requester_show ENUM('yes','no') NOT NULL DEFAULT 'yes'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE accessions MODIFY requester_show TINYINT(1) NULL DEFAULT 0");
    }
};
