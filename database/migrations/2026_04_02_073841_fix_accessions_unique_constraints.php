<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the wrong composite unique (crop_id + variety_id)
        // Multiple accessions can have the same crop+variety combination
        $indexes = collect(DB::select("SHOW INDEX FROM accessions"))
            ->pluck('Key_name')->unique()->toArray();

        if (in_array('unique_crop_variety', $indexes)) {
            DB::statement('ALTER TABLE accessions DROP INDEX unique_crop_variety');
        }

        // Also drop the old accession_id unique if it exists (renamed to accession_number)
        if (in_array('accessions_accession_id_unique', $indexes)) {
            DB::statement('ALTER TABLE accessions DROP INDEX accessions_accession_id_unique');
        }
    }

    public function down(): void {}
};
