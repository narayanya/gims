<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class ExtendedPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // ── Accession sub-actions ──────────────────────────────────
            ['slug' => 'accession.import',  'name' => 'Import Accessions'],
            ['slug' => 'accession.export',  'name' => 'Export Accessions'],
            ['slug' => 'accession.report',  'name' => 'Accession Reports'],

            // ── Lot sub-actions ────────────────────────────────────────
            ['slug' => 'lot.import',        'name' => 'Import Lots'],
            ['slug' => 'lot.export',        'name' => 'Export Lots'],
            ['slug' => 'lot.transfer',      'name' => 'Lot Transfer'],

            // ── Crop sub-actions ───────────────────────────────────────
            ['slug' => 'crop.import',       'name' => 'Import Crops'],
            ['slug' => 'crop.export',       'name' => 'Export Crops'],

            // ── Variety sub-actions ────────────────────────────────────
            ['slug' => 'variety.import',    'name' => 'Import Varieties'],
            ['slug' => 'variety.export',    'name' => 'Export Varieties'],

            // ── Storage sub-actions ────────────────────────────────────
            ['slug' => 'storage.export',    'name' => 'Export Storage'],
            ['slug' => 'storage.transfer',  'name' => 'Warehouse Transfer'],

            // ── Request sub-actions ────────────────────────────────────
            ['slug' => 'request.export',    'name' => 'Export Requests'],
            ['slug' => 'request.receive',   'name' => 'Receive Request'],
            ['slug' => 'request.return',    'name' => 'Return Request'],

            // ── Dispatch sub-actions ───────────────────────────────────
            ['slug' => 'dispatch.export',   'name' => 'Export Dispatch'],
            ['slug' => 'dispatch.mrn',      'name' => 'Generate MRN'],

            // ── Reports menu ───────────────────────────────────────────
            ['slug' => 'report.view',       'name' => 'View Reports'],
            ['slug' => 'report.export',     'name' => 'Export Reports'],
            ['slug' => 'report.expiry',     'name' => 'Expiry Report'],
            ['slug' => 'report.transaction','name' => 'Transaction Report'],

            // ── Menu access ────────────────────────────────────────────
            ['slug' => 'menu.dashboard',    'name' => 'Dashboard Menu'],
            ['slug' => 'menu.accession',    'name' => 'Accession Menu'],
            ['slug' => 'menu.lot',          'name' => 'Lot Management Menu'],
            ['slug' => 'menu.storage',      'name' => 'Storage Menu'],
            ['slug' => 'menu.dispatch',     'name' => 'Dispatch Menu'],
            ['slug' => 'menu.request',      'name' => 'Request Menu'],
            ['slug' => 'menu.reports',      'name' => 'Reports Menu'],
            ['slug' => 'menu.masters',      'name' => 'Masters Menu'],
            ['slug' => 'menu.settings',     'name' => 'Settings Menu'],
            ['slug' => 'menu.logs',         'name' => 'Logs Menu'],

            // ── Settings / Admin ───────────────────────────────────────
            ['slug' => 'settings.view',     'name' => 'View Settings'],
            ['slug' => 'settings.users',    'name' => 'Manage Users'],
            ['slug' => 'settings.roles',    'name' => 'Manage Roles'],
            ['slug' => 'settings.permissions','name'=> 'Manage Permissions'],
            ['slug' => 'settings.masters',  'name' => 'Manage Masters'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                ['name' => $perm['name'], 'description' => $perm['name']]
            );
        }
    }
}
