<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AccessionController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VarietyController;
use App\Http\Controllers\SoilTypeController;
use App\Http\Controllers\LogReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CoreAPIController;
use App\Http\Controllers\LocationMasterController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\SeedReturnController;
use App\Http\Controllers\SeedQuantityController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\LotTransferController;
use App\Http\Controllers\WarehouseTransferController;
use App\Http\Controllers\QualityMasterController;

use App\Http\Controllers\CropRequestController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LotRegenerationController;

Route::get('/', function () {
    return view('auth.login');
});

// ── JWT Token-based login (separate route, no CSRF, no guest middleware) ──
Route::get('/gims_login', [App\Http\Controllers\Auth\LoginController::class, 'handleTokenLogin'])
    ->name('token.login');

Route::get('/accessions/public/{id}', [AccessionController::class, 'publicShow'])
    ->name('accessions.public.show');
Route::get('/lots/public/{id}', [LotController::class, 'publicShow'])
    ->name('lots.public.show');

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

Route::middleware('auth')->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('report.reports')->middleware('permission:report.view');
    Route::get('/reports/summary', [ReportController::class, 'summary'])->name('report.summary')->middleware('permission:report.view');
    Route::get('/reports/accession/{id}/history', [ReportController::class, 'accessionHistory'])->name('report.accession.history');
    Route::get('/reports/lot/{id}/history', [ReportController::class, 'lotHistory'])->name('report.lot.history');
    Route::get('/reports/request', [ReportController::class, 'requestReport'])->name('report.request')->middleware('permission:report.view');
    
    Route::get('/reports/request/download', [ReportController::class, 'downloadRequestReport'])->name('report.request.download')->middleware('permission:report.export');
    Route::get('/reports/expiry-report', [ReportController::class, 'expiryReport'])->name('expiry.report')->middleware('permission:report.expiry');
    Route::get('/reports/expiry-report/download', [ReportController::class, 'downloadExpiryReport'])->name('expiry.report.download')->middleware('permission:report.export');
    Route::get('/reports/transaction/{type}', [ReportController::class, 'transactionReport'])->name('report.transaction')->middleware('permission:report.transaction');
    Route::get('/reports/transaction/{type}/download', [ReportController::class, 'downloadTransactionReport'])->name('report.transaction.download')->middleware('permission:report.export');
 
    // AJAX helpers
    Route::get('/get-states/{countryId}',   [AccessionController::class, 'getStatesByCountry']);
    Route::get('/get-districts/{stateId}',  [AccessionController::class, 'getDistricts']);
    Route::get('/get-cities/{districtId}',  [AccessionController::class, 'getCities']);
    Route::get('/get-varieties/{crop_id}',  [AccessionController::class, 'getVarieties']);
    Route::get('/check-accession', function (Illuminate\Http\Request $request) {
        $exists = \App\Models\Accession::where('crop_id', $request->crop_id)
            ->where('variety_id', $request->variety_id)
            ->exists();

        return response()->json(['exists' => $exists]);
    });

    // Accession routes (static paths before wildcard)
    Route::get('/accessions', function () { return redirect()->route('accession.accession-list'); });
    Route::get('/accession-list',            [AccessionController::class, 'index'])->name('accession.accession-list')->middleware('permission:accession.view');
    Route::get('/accessionform',             [AccessionController::class, 'create'])->name('accessionform')->middleware('permission:accession.create');
    Route::post('/accessions',               [AccessionController::class, 'store'])->name('accessions.store')->middleware('permission:accession.create');
    Route::post('/accessions/import',        [AccessionController::class, 'import'])->name('accessions.import')->middleware('permission:accession.import');
    Route::get('/accessions/export',         [AccessionController::class, 'export'])->name('accessions.export')->middleware('permission:accession.export');
    Route::get('/accessions/sample-template', function () {
        return response()->download(public_path('templates/accessions_sample.csv'));
    })->name('accessions.template');

    Route::get('/accessions/passport-template', function () {
        return response()->download(public_path('templates/accessions_passport_sample.csv'));
    })->name('accessions.passport-template');


    Route::get('/accessions/{id}',           [AccessionController::class, 'show'])->name('accessions.show')->middleware('permission:accession.view');
    Route::get('/accessions/{id}/json',      [AccessionController::class, 'showJson'])->name('accessions.show.json');
    Route::get('/accessions/{id}/edit',      [AccessionController::class, 'edit'])->name('accessions.edit')->middleware('permission:accession.edit');
    Route::put('/accessions/{id}',           [AccessionController::class, 'update'])->name('accessions.update')->middleware('permission:accession.edit');
    Route::post('/accession/{id}/deactivate',    [AccessionController::class, 'deactivate'])->name('accession.deactivate');
    Route::get('/reports/low-stock-report',    [AccessionController::class, 'lowStockReport'])->name('accession.lowStockReport');

Route::get('/get-crop-details/{id}', [CropController::class,'getCropDetails']);

    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users')->middleware('permission:menu.users');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store')->middleware('permission:user.create');
    Route::post('/users/{user}/roles', [App\Http\Controllers\UserController::class, 'assignRole'])->name('users.assignRole')->middleware('permission:user.edit');
    Route::post('/users/{user}/remove-role', [App\Http\Controllers\UserController::class, 'removeRole'])->name('users.removeRole')->middleware('permission:user.edit');
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:user.delete');
    Route::post('/users/sync', [UserController::class, 'syncEmployees'])->name('users.sync');

    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\UserController::class, 'profileUpdate'])->name('profile.update');
    Route::post('/profile/password', [App\Http\Controllers\UserController::class, 'passwordUpdate'])->name('profile.password');

    // Storage Routes
    //Route::resource('storage', App\Http\Controllers\StorageController::class);
    //Route::resource('storage-management', App\Http\Controllers\StorageController::class);
    Route::resource('storage-management', App\Http\Controllers\StorageController::class)
    ->parameters([
        'storage-management' => 'storage'
    ]);

    Route::get('/lot-management', [App\Http\Controllers\LotController::class, 'managementIndex'])->name('lot-management')->middleware('permission:lot.view');
    Route::get('/lot-management/create', [App\Http\Controllers\LotController::class, 'managementCreate'])->name('lot-management.create')->middleware('permission:lot.create');
    Route::get('/lot-management/{id}/edit', [App\Http\Controllers\LotController::class, 'managementEdit'])->name('lot-management.edit')->middleware('permission:lot.edit');
    Route::post('/lot-management', [App\Http\Controllers\LotController::class, 'managementStore'])->name('lot-management.store')->middleware('permission:lot.create');
    Route::put('/lot-management/{id}', [App\Http\Controllers\LotController::class, 'managementUpdate'])->name('lot-management.update')->middleware('permission:lot.edit');
    Route::get('/lot-management/storage/{id}', [App\Http\Controllers\LotController::class, 'getStorageDetails'])->name('lot-management.storage');
    Route::get('/lot-management/accession/{id}', [App\Http\Controllers\LotController::class, 'getAccessionDetails'])->name('lot-management.accession');
    Route::get('/lot-management/{id}/quality', [App\Http\Controllers\LotController::class, 'getLotQualityDetails'])->name('lot-management.quality');
    Route::get('/lot-management/{id}/quantity', [App\Http\Controllers\LotController::class, 'getLotQuantityDetails'])->name('lot-management.quantity');
    Route::get('/lot-management/accessions-by-storage/{id}', [LotController::class, 'getAccessionsByStorage'])->name('lot-management.accessions-by-storage');
    Route::get('/lot-management/{id}/quantity', [LotController::class, 'getQuantityDetails']);
    Route::get('/lot-management/{id}/quality', [LotController::class, 'getQualityDetails']);
    Route::get('/inter-transfer-location', [LotController::class, 'interTransfer'])->name('inter.transfer');
    Route::post('/lot-management/dispose/{id}',
    [LotController::class, 'dispose'])
    ->name('lot-management.dispose');

    Route::get('/lot-regeneration', [LotRegenerationController::class, 'index'])
    ->name('lot-regeneration.index');

    Route::post('/lot-regeneration/store', [LotRegenerationController::class, 'store'])
        ->name('lot-regeneration.store');

    Route::get('/lot-regeneration/get-lot/{id}', [LotRegenerationController::class, 'getLot'])
        ->name('lot-regeneration.getLot');
    
    Route::get('/quality-control', [LotController::class, 'qualityControl'])->name('quality-control.index');
    Route::get('/quality-control-history', [LotController::class, 'qualityHistoryControl'])->name('quality-control.history');
    Route::post('/quality-control/{lotId}/save', [LotController::class, 'qualityControlSave'])->name('quality-control.save');
    Route::get('/quality-control/{lotId}/qualities', [LotController::class, 'getLotQualities'])->name('quality-control.qualities');
    Route::get('/get-lot-by-number', [LotController::class, 'getLotByNumber']);
    Route::get('/lot/export',         [LotController::class, 'export'])->name('lot.export')->middleware('permission:lot.export');
    Route::get('/get-lot-details', [LotController::class, 'getLotDetails']);
    Route::get('/lot-management/qrprint-all', [LotController::class, 'qrprintAll'])
    ->name('lot.qrprint.all');


    Route::get('/lot-transfer-export', [LotTransferController::class, 'export'])
    ->name('lot-transfer.export');

    // Crop Master
    Route::resource('crops', App\Http\Controllers\CropController::class)->except(['show', 'create']);
    Route::post('/crops/import', [CropController::class, 'import'])->name('crops.import');
    Route::get('/crops/export',         [CropController::class, 'export'])->name('crops.export')->middleware('permission:crops.export');
    Route::get('/crop/crop-template', function () {
        return response()->download(public_path('templates/GIMS-Master-CROP.csv'));
    })->name('crop.crop-template');

    // Variety/Seed Master
    Route::resource('varieties', App\Http\Controllers\VarietyController::class)->except(['show', 'create']);
    Route::post('/varieties/import', [VarietyController::class, 'import'])->name('varieties.import');

    // Category Master
    Route::resource('categories', App\Http\Controllers\CategoryController::class)->except(['show', 'create']);

    // Unit Master
    Route::resource('units', App\Http\Controllers\UnitController::class)->except(['show', 'create']);

    // Warehouse Master
    Route::resource('warehouses', App\Http\Controllers\WarehouseController::class)->except(['show', 'create']);

    Route::resource('quality-master', QualityMasterController::class);

    // Storage Location Sub-Masters (Section, Rack, Bin, Container)
    Route::get('/storage-location-master', [App\Http\Controllers\StorageLocationMasterController::class, 'index'])->name('storage-location-master.index');
    Route::get('/storage-location-master/export', [App\Http\Controllers\StorageLocationMasterController::class, 'export'])->name('storage-location-master.export');
    Route::post('/racks/import',       [App\Http\Controllers\StorageLocationMasterController::class, 'rackImport'])->name('racks.import');
    Route::post('/bins/import',        [App\Http\Controllers\StorageLocationMasterController::class, 'binImport'])->name('bins.import');
    Route::post('/containers/import',  [App\Http\Controllers\StorageLocationMasterController::class, 'containerImport'])->name('containers.import');
    Route::post('/sections',              [App\Http\Controllers\StorageLocationMasterController::class, 'sectionStore'])->name('sections.store');
    Route::put('/sections/{section}',     [App\Http\Controllers\StorageLocationMasterController::class, 'sectionUpdate'])->name('sections.update');
    Route::delete('/sections/{section}',  [App\Http\Controllers\StorageLocationMasterController::class, 'sectionDestroy'])->name('sections.destroy');
    Route::post('/racks',                 [App\Http\Controllers\StorageLocationMasterController::class, 'rackStore'])->name('racks.store');
    Route::put('/racks/{rack}',           [App\Http\Controllers\StorageLocationMasterController::class, 'rackUpdate'])->name('racks.update');
    Route::delete('/racks/{rack}',        [App\Http\Controllers\StorageLocationMasterController::class, 'rackDestroy'])->name('racks.destroy');
    Route::post('/bins',                  [App\Http\Controllers\StorageLocationMasterController::class, 'binStore'])->name('bins.store');
    Route::put('/bins/{bin}',             [App\Http\Controllers\StorageLocationMasterController::class, 'binUpdate'])->name('bins.update');
    Route::delete('/bins/{bin}',          [App\Http\Controllers\StorageLocationMasterController::class, 'binDestroy'])->name('bins.destroy');
    Route::post('/containers',            [App\Http\Controllers\StorageLocationMasterController::class, 'containerStore'])->name('containers.store');
    Route::put('/containers/{container}', [App\Http\Controllers\StorageLocationMasterController::class, 'containerUpdate'])->name('containers.update');
    Route::delete('/containers/{container}', [App\Http\Controllers\StorageLocationMasterController::class, 'containerDestroy'])->name('containers.destroy');
    Route::get('/storage/storage-template', function () {
        return response()->download(public_path('templates/storage-template.csv'));
    })->name('storage.storage-template');

    // Storage Location Master
    Route::resource('storage-locations', App\Http\Controllers\StorageLocationController::class)->except(['show', 'create']);

    // Lot Inter-Transfer
    Route::get('/lot-transfer', [App\Http\Controllers\LotTransferController::class, 'index'])->name('lot-transfer.index');
    Route::post('/lot-transfer', [App\Http\Controllers\LotTransferController::class, 'transfer'])->name('lot-transfer.store');
    Route::get('/get-storage-lots/{id}', [App\Http\Controllers\LotTransferController::class, 'getStorageLots'])->name('lot-transfer.storage-lots');
    Route::get('/get-storage-hierarchy/{id}', [App\Http\Controllers\LotTransferController::class, 'getStorageHierarchy'])->name('lot-transfer.hierarchy');
    Route::get('/get-accessions/{cropId}', [App\Http\Controllers\LotTransferController::class, 'getAccessions']);
    Route::get('/get-accession-storages/{accessionId}', [App\Http\Controllers\LotTransferController::class, 'getAccessionStorages']);

    // Lot Inter-Transfer
    Route::get('/warehouse-transfer', [App\Http\Controllers\WarehouseTransferController::class, 'index'])->name('warehouse-transfer.index')->middleware('permission:storage.transfer');
    Route::post('/warehouse-transfer', [WarehouseTransferController::class, 'store'])->name('warehouse-transfer.store')->middleware('permission:storage.transfer');
    Route::get('/get-lots-by-warehouse', [WarehouseTransferController::class, 'getLotsByWarehouse']);
    Route::get('/get-storages-by-warehouse', [WarehouseTransferController::class, 'getStoragesByWarehouse']);
    Route::get('/get-warehouse-by-storage', [WarehouseTransferController::class, 'getWarehouseByStorage']);
    Route::get('/warehouse-transfer-export', [WarehouseTransferController::class, 'export'])
    ->name('warehouse-transfer.export');
    Route::get('/warehouse-transfer/{id}/itn', [WarehouseTransferController::class, 'itn'])->name('warehouse-transfer.itn');
    Route::post('/warehouse-transfer/process-itn', [WarehouseTransferController::class, 'processITN'])
    ->name('warehouse-transfer.process.itn');
    Route::get('/warehouse-transfer/{id}/itn/print', [WarehouseTransferController::class, 'printITN'])
    ->name('warehouse-transfer.itn.print');
    Route::get('/dispatch/itn/{id}', [DispatchController::class, 'showITN'])
    ->name('dispatch.itn.show');

    // Lot Master
    Route::resource('lots', App\Http\Controllers\LotMasterController::class)->except(['show', 'create', 'edit']);

    // Lot Management Type Master
    Route::resource('lot-types', App\Http\Controllers\LotTypeController::class)->except(['show', 'create']);

    // Master storage types
    Route::resource('storage-types', App\Http\Controllers\StorageTypeController::class)->except(['show', 'create']);

    // Storage Time Master
    Route::resource('storage-times', App\Http\Controllers\StorageTimeController::class)->except(['show', 'create']);

    // Storage Condition Master
    Route::resource('storage-conditions', App\Http\Controllers\StorageConditionController::class)->except(['show', 'create']);

    // Location Master (read-only, sync data)
    Route::get('/location/countries',        [App\Http\Controllers\LocationMasterController::class, 'countries'])->name('location.countries');
    Route::get('/location/states',           [App\Http\Controllers\LocationMasterController::class, 'states'])->name('location.states');
    Route::get('/location/districts',        [App\Http\Controllers\LocationMasterController::class, 'districts'])->name('location.districts');
    Route::get('/location/cities',           [App\Http\Controllers\LocationMasterController::class, 'cities'])->name('location.cities');
    Route::post('/location/sync/countries',  [App\Http\Controllers\LocationMasterController::class, 'syncCountries'])->name('location.sync.countries');

    // Crop Category Master
    Route::resource('crop-categories', App\Http\Controllers\CropCategoryController::class)->except(['show', 'create', 'edit']);
    
    // Crop Type Master
    Route::resource('crop-types', App\Http\Controllers\CropTypeController::class)->except(['show', 'create', 'edit']);
    
    // Variety Type Master
    Route::resource('variety-types', App\Http\Controllers\VarietyTypeController::class)->except(['show', 'create', 'edit']);
    
    // Season Master
    Route::resource('seasons', App\Http\Controllers\SeasonController::class)->except(['show', 'create', 'edit']);

    // Arrival Type Master
    Route::resource('arrival-types', App\Http\Controllers\ArrivalTypeController::class)->except(['show', 'create', 'edit']);

    // Pouch Master
    Route::resource('pouches', App\Http\Controllers\PouchController::class)->except(['show', 'create', 'edit']);
    
    // Accession Rule Master
    Route::resource('accession-rules', App\Http\Controllers\AccessionRuleController::class)->except(['show', 'create', 'edit']);

    // Seed Class Master
    Route::resource('seed-classes', App\Http\Controllers\SeedClassController::class)->except(['show', 'create', 'edit']);

    Route::resource('soil-types', SoilTypeController::class);
    
    // CoreData Sync
    Route::get('/sync', [App\Http\Controllers\CoreDataSyncController::class, 'index'])->name('sync.index');
    Route::post('/sync/data', [App\Http\Controllers\CoreDataSyncController::class, 'syncData'])->name('sync.data');
    
    // Location Sync
    Route::get('/sync/location', [App\Http\Controllers\CoreDataSyncController::class, 'locationIndex'])->name('sync.location.index');
    Route::post('/sync/location', [App\Http\Controllers\CoreDataSyncController::class, 'syncLocation'])->name('sync.location');
    Route::post('/sync/location/all', [App\Http\Controllers\CoreDataSyncController::class, 'syncAllLocations'])->name('sync.location.all');
    Route::get('/sync/location/counts', [App\Http\Controllers\CoreDataSyncController::class, 'getLocationCounts'])->name('sync.location.counts');

    // emp detaisl Sync
    Route::get('/employees', [EmployeeController::class, 'index'])->name('master.employees.index');
    Route::get('/employees/list', [EmployeeController::class, 'getEmployees'])->name('employees.list');
    Route::get('/employee/{id}', function ($id) {
        $emp = DB::table('core_employee as e')
            ->leftJoin('core_employee as rm', 'e.emp_reporting', '=', 'rm.employee_id')
            ->where('e.employee_id', $id)
            ->where('e.emp_status', 'A')
            ->select(
                'e.emp_name',
                'e.emp_email',
                'e.emp_department',
                'rm.emp_name as reporting_name',
                'rm.emp_email as reporting_email'
            )
            ->first();

        return response()->json($emp);
    });
    
    // Request Management
    Route::resource('requests', App\Http\Controllers\RequestController::class)->parameters([
        'requests' => 'seedRequest'
    ]);

    Route::get('/request-chart', [RequestController::class, 'getRequestChart']);
    
    Route::put('/requests/{id}/approve', [RequestController::class,'approve'])->name('requests.approve');
    Route::put('/requests/{id}/reject',  [RequestController::class,'reject'])->name('requests.reject');
    Route::post('/requests/{id}/receive', [RequestController::class,'receive'])->name('requests.receive');
    Route::post('/requests/{id}/return', [RequestController::class,'return'])->name('requests.return');
    Route::get('/get-accessions/{variety_id}', [App\Http\Controllers\RequestController::class,'getAccessions'])->name('get.accessions');
    Route::get('/get-varieties/{id}', [RequestController::class, 'getVarieties'])->name('get.varieties');
    Route::get('/get-accessions/{id}', [RequestController::class, 'getAccessions'])->name('get.accessions');

    Route::get('/requests/export',         [RequestController::class, 'export'])->name('requests.export')->middleware('permission:requests.export');
    
    Route::get('/employee/{id}', function ($id) {
        $emp = \Illuminate\Support\Facades\DB::table('core_employee')
            ->where('employee_id', $id)
            ->first(['emp_name','emp_code','emp_email','emp_contact','emp_department','emp_designation']);
        if (!$emp) return response()->json(null);
        return response()->json($emp);
    });

    Route::get('/check-user', function (\Illuminate\Http\Request $request) {
        $exists = \App\Models\User::where('emp_code', $request->emp_code)->exists();
        return response()->json(['exists' => $exists]);
    });

    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings')->middleware('permission:menu.settings');

    // Log Report
    Route::get('/logs', [LogReportController::class, 'index'])->name('logs.index')->middleware('permission:menu.logs');
    Route::get('/logs/export', [LogReportController::class, 'export'])->name('logs.export')->middleware('permission:menu.logs');
    Route::get('/logs/{id}', [LogReportController::class, 'show'])->name('logs.show')->middleware('permission:menu.logs');
    Route::post('/logs/page-exit', [LogReportController::class, 'pageExit'])->name('logs.page-exit');

     //========================Core API=======================
    Route::resource('core_api', CoreAPIController::class);
    Route::get('core_api_sync', [CoreAPIController::class, 'sync'])->name('core_api_sync');
    Route::post('importAPISData', [CoreAPIController::class, 'importAPISData'])->name('importAPISData');

    Route::prefix('roles')->group(function () {

        Route::get('/', [RoleController::class, 'index'])->name('settings.role')->middleware('permission:menu.roles');

        Route::get('/create', [RoleController::class, 'create'])->name('settings.create')->middleware('permission:role.create');

        Route::post('/store', [RoleController::class, 'store'])->name('settings.store')->middleware('permission:role.create');

        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:role.edit');

        Route::put('/update/{id}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:role.edit');

        Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('roles.delete')->middleware('permission:role.delete');

    });

    Route::prefix('permission')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('settings.permission')->middleware('permission:menu.permissions');
        Route::post('/save', [PermissionController::class, 'update'])->name('settings.permission.save')->middleware('permission:menu.permissions');
    });

    Route::get('/dispatch-orders', [DispatchController::class, 'index'])
    ->name('dispatch-management.index')->middleware('permission:dispatch.view');

    Route::post('/dispatch/{id}', [DispatchController::class, 'dispatch'])
        ->name('dispatch-management.send');

    Route::post('/dispatch/itn/{id}', [DispatchController::class, 'itnStore'])->name('dispatch.itnStore');
    Route::get('/dispatch/{id}', [DispatchController::class, 'show'])->name('dispatch.show');
    Route::post('/dispatch/{id}', [DispatchController::class, 'store'])->name('dispatch.store');
    Route::get('/dispatch-print/{id}', [DispatchController::class, 'print'])->name('dispatch.print');
    Route::get('/dispatch-report', [DispatchController::class, 'dispatchReport'])
    ->name('dispatch.report');
    Route::get('/storage-report', [StorageController::class, 'storageReport'])
    ->name('storage.report');

    Route::post('/requests/{id}/return', [SeedReturnController::class, 'store'])->name('requests.return');

    Route::resource('seed-quantities', SeedQuantityController::class);
    Route::post('/crop-request/store', [CropRequestController::class, 'storeCropRequest'])
    ->name('crop.request.store');
    Route::get('/crop-requests', [CropRequestController::class, 'index'])->name('cropRequests.index');

    Route::get('/accession/{id}', function ($id) {
        $acc = \App\Models\Accession::with(['crop','capacityUnit'])->find($id);

        if (!$acc) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Aggregate from seed_quantities table
        $sq = \App\Models\SeedQuantity::where('accession_id', $id)
            ->selectRaw('SUM(quantity) as total_quantity, SUM(quantity_show) as total_quantity_show, MAX(capacity_unit_id) as capacity_unit_id')
            ->first();

        $unit = null;
        if ($sq?->capacity_unit_id) {
            $unit = \App\Models\Unit::find($sq->capacity_unit_id);
        }
        if (!$unit) {
            $unit = $acc->capacityUnit;
        }

        $totalQty     = $sq?->total_quantity     ?? $acc->quantity       ?? 0;
        $totalQtyShow = $sq?->total_quantity_show ?? $acc->quantity_show  ?? 0;

        // Per-lot breakdown
        $lots = \App\Models\Lot::with(['unit', 'storage'])
            ->where('accession_id', $id)
            ->whereNotNull('lot_number')
            ->get()
            ->map(function ($lot) use ($unit) {
                $sq = \App\Models\SeedQuantity::where('lot_id', $lot->id)->first();
                return [
                    'lot_number' => $lot->lot_number,
                    'storage'    => $lot->storage?->name ?? '—',
                    'quantity'   => $sq?->quantity_show ?? $sq?->quantity ?? $lot->quantity ?? 0,
                    'unit'       => $lot->unit?->name ?? $unit?->name ?? '',
                ];
            });

        return response()->json([
            'id'               => $acc->id,
            'accession_name'   => $acc->accession_name,
            'accession_number' => $acc->accession_number,
            'crop'             => optional($acc->crop)->crop_name,
            'scientific_name'  => $acc->scientific_name,
            'quantity'         => $totalQtyShow,
            'quantity_show'    => $totalQtyShow,
            'total_quantity'   => $totalQty,
            'unit'             => $unit?->name ?? $unit?->code ?? '',
            'unit_id'          => $unit?->id ?? $acc->capacity_unit_id,
            'expiry_date'      => $acc->expiry_date,
            'barcode'          => $acc->barcode,
            'lots'             => $lots,
        ]);
    });
    
});