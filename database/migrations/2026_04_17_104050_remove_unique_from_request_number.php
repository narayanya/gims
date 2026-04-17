<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop unique — multiple rows can share the same request_number (multi-accession request)
        DB::statement('ALTER TABLE requests DROP INDEX requests_request_number_unique');
        // Add a regular index for performance
        DB::statement('ALTER TABLE requests ADD INDEX idx_request_number (request_number)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE requests DROP INDEX idx_request_number');
        DB::statement('ALTER TABLE requests ADD UNIQUE INDEX requests_request_number_unique (request_number)');
    }
};
