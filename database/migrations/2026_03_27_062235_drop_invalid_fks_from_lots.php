<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // FK pointing to non-existent 'crops' table — real data is in core_crop
    private array $brokenFks = [
        'lots_crop_id_foreign',
    ];

    public function up(): void
    {
        $existing = collect(DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lots'
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
        ))->pluck('CONSTRAINT_NAME')->toArray();

        foreach ($this->brokenFks as $fk) {
            if (in_array($fk, $existing)) {
                DB::statement("ALTER TABLE lots DROP FOREIGN KEY `{$fk}`");
            }
        }
    }

    public function down(): void {}
};
