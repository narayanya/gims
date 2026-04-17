<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Storage;
use App\Models\Lot;

class SyncStorageUsageSeeder extends Seeder
{
    public function run(): void
    {
        // Fix lots with null/zero quantity — pull from accession quantity_show
        $lots = DB::select('
            SELECT l.id, l.accession_id, a.quantity_show
            FROM lots l
            JOIN accessions a ON a.id = l.accession_id
            WHERE l.lot_number IS NOT NULL
              AND (l.quantity IS NULL OR l.quantity = 0)
        ');

        foreach ($lots as $lot) {
            DB::update('UPDATE lots SET quantity = ? WHERE id = ?', [
                $lot->quantity_show ?? 0,
                $lot->id,
            ]);
            $this->command->info("Fixed lot {$lot->id} → qty {$lot->quantity_show}");
        }

        // Sync current_usage for all storages from actual lot quantities
        Storage::all()->each(function ($storage) {
            $used = Lot::where('storage_id', $storage->id)
                       ->whereNotNull('lot_number')
                       ->sum('quantity');
            $storage->update(['current_usage' => $used]);
            $this->command->info("Storage {$storage->id} ({$storage->name}): current_usage = {$used}");
        });
    }
}
