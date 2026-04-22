<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Storage;
use App\Models\SeedQuantity;

class SyncStorageUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-storage-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $storages = Storage::all();

        foreach ($storages as $storage) {

            $used = SeedQuantity::whereHas('lot', function ($q) use ($storage) {
                $q->where('storage_id', $storage->id);
            })->sum('quantity');

            $storage->update([
                'current_usage' => $used
            ]);
        }

        $this->info('Storage usage synced successfully ✅');
    }
}
