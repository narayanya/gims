<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make storage_time nullable with default 0 so it doesn't block inserts
        DB::statement("ALTER TABLE accessions MODIFY storage_time INT(11) NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE accessions MODIFY storage_time INT(11) NOT NULL");
    }
};
