<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // FKs pointing to non-existent tables
    private array $brokenFks = [
        'accessions_crop_id_foreign',
        'accessions_country_id_foreign',
        'accessions_state_id_foreign',
    ];

    public function up(): void
    {
        $existing = collect(DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accessions'
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
        ))->pluck('CONSTRAINT_NAME')->toArray();

        foreach ($this->brokenFks as $fk) {
            if (in_array($fk, $existing)) {
                DB::statement("ALTER TABLE accessions DROP FOREIGN KEY `{$fk}`");
            }
        }
    }

    public function down(): void
    {
        // Not restoring broken FKs
    }
};
