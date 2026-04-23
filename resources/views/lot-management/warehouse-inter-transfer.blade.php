@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Warehouse Inter-Transfer</h3>
                <p class="text-muted mb-0" style="font-size:13px">Move a Warehouse from one location to another location</p>
            </div>
            <a href="{{ route('lot-management') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Back to Lot List
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('warehouse-transfer.store') }}">
            @csrf
            <div class="row g-3">
                {{-- ── FROM ── --}}
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="ri-map-pin-line me-1"></i>Pick From</h6>
                        </div>
                        <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select name="from_warehouse_id" id="from_warehouse" class="form-select" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" 
                                            data-state="{{ $w->state?->state_name }}"
                                                    data-district="{{ $w->district?->district_name }}"
                                                    data-city="{{ $w->city?->city_village_name }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Storage <span class="text-danger">*</span></label>
                                <select id="from_storage" name="from_storage" class="form-select">
                                    <option value="">Select Storage</option>
                                    @foreach($storages as $s)
                                        <option value="{{ $s->id }}">{{ $s->storage_id }} — {{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                            <div id="from_warehouseInfo" class="card border bg-light d-none">
                                <div class="card-body py-2 small">
                                    <div class="row g-1">
                                        <div class="col-6"><span class="text-muted">Country:</span> <span id="fw_country">—</span></div>
                                        <div class="col-6"><span class="text-muted">State:</span> <span id="fw_state">—</span></div>
                                        <div class="col-6"><span class="text-muted">District:</span> <span id="fw_district">—</span></div>
                                        <div class="col-6"><span class="text-muted">City/Village:</span> <span id="fw_city">—</span></div>
                                    </div>
                                </div>
                            </div>
                     
                            <div class="card mt-2 d-none" id="lotTableCard">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Available Lots</h6>
                                </div>

                                <div class="card-body p-0"  style="height: 200px;overflow:auto;">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select_all"></th>
                                                <th>Lot No</th>
                                                <th>Crop</th>
                                                <th>Accession</th>
                                                <th>Storage</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lotTableBody"></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Arrow ── --}}
                <div class="col-md-2 d-flex align-items-center justify-content-center d-none">
                    <div class="bg-light rounded-circle shadow-sm" style="padding: 11px 18px;">
                        <i class="ri-arrow-right-line fs-4 text-primary"></i>
                    </div>
                </div>

                {{-- ── TO ── --}}
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="ri-map-pin-2-line me-1"></i>Transfer To</h6>
                        </div>
                        <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select name="to_warehouse_id" id="to_warehouse" class="form-select" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Storage <span class="text-danger">*</span></label>
                                <select id="to_storage" name="to_storage" class="form-select">
                                    <option value="">Select Storage</option>
                                    @foreach($storages as $s)
                                        <option value="{{ $s->id }}">{{ $s->storage_id }} — {{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-12">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" name="reamrks" id="remarks" ></textarea>
                            </div>
                        </div>

                            <div id="to_warehouseInfo" class="card border bg-light mb-3 d-none">
                                <div class="card-body py-2 small">
                                    <div class="row g-1">
                                        <div class="col-6"><span class="text-muted">Country:</span> <span id="tw_country">—</span></div>
                                        <div class="col-6"><span class="text-muted">State:</span> <span id="tw_state">—</span></div>
                                        <div class="col-6"><span class="text-muted">District:</span> <span id="tw_district">—</span></div>
                                        <div class="col-6"><span class="text-muted">City/Village:</span> <span id="tw_city">—</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-2 d-none" id="tolotTableCard">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Available Lots</h6>
                                </div>

                                <div class="card-body p-0"  style="height: 200px;overflow:auto;">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Lot No</th>
                                                <th>Crop</th>
                                                <th>Accession</th>
                                                <th>Storage</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tolotTableBody"></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-send-plane-line me-1"></i> Transfer Now
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
<div class="col-md-12">
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Last 10 Transfers</h5>
                <div class="d-flex gap-2 align-items-center">

                    {{-- FILTER FORM --}}
                    <form method="GET" action="{{ route('warehouse-transfer.index') }}" class="d-flex gap-2">
                        <input type="date" name="date_from" class="form-control form-control-sm"
                            value="{{ request('date_from') }}" style="width:140px">

                        <input type="date" name="date_to" class="form-control form-control-sm"
                            value="{{ request('date_to') }}" style="width:140px">

                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>

                    {{-- EXPORT --}}
                    <a href="{{ route('warehouse-transfer.export', request()->all()) }}" class="btn btn-sm btn-success">
                        <i class="ri-file-download-line me-1"></i> Export Report
                    </a>

                </div>
            </div>
<div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Crop</th>
                    <th>Accession No.</th>
                    <th>Lot</th>
                    <th>From Warehouse</th>
                    <th>From Storage</th>
                    <th>To Warehouse</th>
                    <th>To Storage</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wTransfers as $t)
                    <tr>
                        <td>{{ $t->created_at->format('d-m-Y H:i') }}</td>
                        <td>{{ $t->lot->crop->crop_name ?? '-' }}</td>
                        <td>{{ $t->lot->accession->accession_number ?? '-' }}</td>
                        <td>{{ $t->lot->lot_number ?? '-' }}</td>
                        
                        <td>{{ $t->fromWarehouse->name ?? '-' }}</td>
                        <td>{{ $t->fromStorage->name ?? '-' }}</td>
                        <td>{{ $t->toWarehouse->name ?? '-' }}</td>
                        
                        <td>{{ $t->toStorage->name ?? '-' }}</td>
                        <td>{{ $t->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No transfers found</td>
                    </tr>
                @endforelse
               </tbody>
        </table> 
     </div>
        </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const warehouses = @json($warehouses);

    function getWarehouse(id) {
        return warehouses.find(w => w.id == id);
    }

    // --- FROM WAREHOUSE CHANGE ---
    document.getElementById('from_warehouse').addEventListener('change', function () {
        let warehouseId = this.value;
        if (!warehouseId) return;

        let w = getWarehouse(warehouseId);
        document.getElementById('from_warehouseInfo').classList.remove('d-none');
        document.getElementById('fw_country').innerText = w.country?.country_name ?? '—';
        document.getElementById('fw_state').innerText = w.state?.state_name ?? '—';
        document.getElementById('fw_district').innerText = w.district?.district_name ?? '—';
        document.getElementById('fw_city').innerText = w.city?.city_village_name ?? '—';

        // Filter Storage and THEN load lots
        filterStorageByWarehouse(warehouseId);
    });

    // --- FROM STORAGE CHANGE ---
    document.getElementById('from_storage').addEventListener('change', function () {
        let storageId = this.value;
        if (!storageId) return;

        fetch(`/get-warehouse-by-storage?storage_id=${storageId}`)
            .then(res => {
                if (!res.ok) throw new Error('Route not found');
                return res.json();
            })
            .then(data => {
                if (data.warehouse_id) {
                    let whDropdown = document.getElementById('from_warehouse');
                    
                    // Only update and refresh info if it's a different warehouse
                    if (whDropdown.value != data.warehouse_id) {
                        whDropdown.value = data.warehouse_id;
                        
                        // Manually update the warehouse info card
                        let w = warehouses.find(i => i.id == data.warehouse_id);
                        if (w) {
                            document.getElementById('from_warehouseInfo').classList.remove('d-none');
                            document.getElementById('fw_country').innerText = w.country?.country_name ?? '—';
                            document.getElementById('fw_state').innerText = w.state?.state_name ?? '—';
                            document.getElementById('fw_district').innerText = w.district?.district_name ?? '—';
                            document.getElementById('fw_city').innerText = w.city?.city_village_name ?? '—';
                        }
                    }
                    // ✅ Always load lots after finding the warehouse
                    loadLots();
                }
            })
            .catch(err => console.error("Error syncing warehouse:", err));
    });

    function filterStorageByWarehouse(warehouseId) {
        return fetch(`/get-storages-by-warehouse?warehouse_id=${warehouseId}`)
            .then(res => res.json())
            .then(data => {

                let storageDropdown = document.getElementById('from_storage');
                storageDropdown.innerHTML = `<option value="">Select Storage</option>`;

                data.forEach(storage => {
                    storageDropdown.innerHTML += `
                        <option value="${storage.id}">
                            ${storage.storage_id} — ${storage.name}
                        </option>`;
                });

                if (data.length > 0) {
                    storageDropdown.value = data[0].id;

                    // ✅ FIX: wait for DOM update
                    setTimeout(() => {
                        loadLots();
                    }, 50);

                } else {
                    document.getElementById('lotTableBody').innerHTML =
                        '<tr><td colspan="5" class="text-center">No storage found</td></tr>';
                }
            });
    }

    function loadLots() {
        let warehouseId = document.getElementById('from_warehouse').value;
        let storageId = document.getElementById('from_storage').value;

        if (!warehouseId) return;

        let url = `/get-lots-by-warehouse?warehouse_id=${warehouseId}`;

        // ✅ Only send storage if exists
        if (storageId) {
            url += `&storage_id=${storageId}`;
        }

        fetch(`/get-lots-by-warehouse?warehouse_id=${warehouseId}&storage_id=${storageId}`)
                .then(res => res.json())
                .then(data => {

                    let tbody = document.getElementById('lotTableBody');
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center">No lots found</td></tr>`;
                    }

                    data.forEach(lot => {
                        tbody.innerHTML += `
                            <tr>
                                <td><input type="checkbox" name="lot_ids[]" value="${lot.id}"></td>
                                <td>${lot.lot_number}</td>
                                <td>${lot.crop?.crop_name ?? ''}</td>
                                <td>${lot.accession?.accession_number ?? ''}</td>
                                <td>${lot.storage?.name ?? ''}</td>
                            </tr>
                        `;
                    });

                    document.getElementById('lotTableCard').classList.remove('d-none');
                });
            
    }

    // TO WAREHOUSE logic remains the same as your old working code...
    // --- TO WAREHOUSE CHANGE ---
    document.getElementById('to_warehouse').addEventListener('change', function () {
        let warehouseId = this.value;
        if (!warehouseId) return;

        let w = warehouses.find(i => i.id == warehouseId);

        document.getElementById('to_warehouseInfo').classList.remove('d-none');
        document.getElementById('tw_country').innerText = w.country?.country_name ?? '—';
        document.getElementById('tw_state').innerText = w.state?.state_name ?? '—';
        document.getElementById('tw_district').innerText = w.district?.district_name ?? '—';
        document.getElementById('tw_city').innerText = w.city?.city_village_name ?? '—';

        filterToStorageByWarehouse(warehouseId);

    });

    document.getElementById('to_storage').addEventListener('change', function () {
        let storageId = this.value;
        if (!storageId) return;

        fetch(`/get-warehouse-by-storage?storage_id=${storageId}`)
            .then(res => {
                if (!res.ok) throw new Error('Route not found');
                return res.json();
            })
            .then(data => {
                if (data.warehouse_id) {
                    let whDropdown = document.getElementById('to_warehouse');
                    
                    // Only update and refresh info if it's a different warehouse
                    if (whDropdown.value != data.warehouse_id) {
                        whDropdown.value = data.warehouse_id;
                        
                        // Manually update the warehouse info card
                        let w = warehouses.find(i => i.id == data.warehouse_id);
                        if (w) {
                            document.getElementById('to_warehouseInfo').classList.remove('d-none');
                            document.getElementById('tw_country').innerText = w.country?.country_name ?? '—';
                            document.getElementById('tw_state').innerText = w.state?.state_name ?? '—';
                            document.getElementById('tw_district').innerText = w.district?.district_name ?? '—';
                            document.getElementById('tw_city').innerText = w.city?.city_village_name ?? '—';
                        }
                    }
                    // ✅ Always load lots after finding the warehouse
                    loadLotsTo();
                }
            })
            .catch(err => console.error("Error syncing warehouse:", err));

             
    });
    function filterToStorageByWarehouse(warehouseId) {
        fetch(`/get-storages-by-warehouse?warehouse_id=${warehouseId}`)
            .then(res => res.json())
            .then(data => {

                let storageDropdown = document.getElementById('to_storage');
                storageDropdown.innerHTML = `<option value="">Select Storage</option>`;

                data.forEach(storage => {
                    storageDropdown.innerHTML += `
                        <option value="${storage.id}">
                            ${storage.storage_id} — ${storage.name}
                        </option>`;
                });

                // auto select first (same as FROM)

                if (data.length > 0) {
                    storageDropdown.value = data[0].id;

                    // ✅ FIX: wait for DOM update
                    setTimeout(() => {
                        loadLotsTo();
                    }, 50);

                } else {
                    document.getElementById('lotTableBody').innerHTML =
                        '<tr><td colspan="5" class="text-center">No storage found</td></tr>';
                }
            });
    }

    function loadLotsTo() {
        let warehouseId = document.getElementById('to_warehouse').value;
        let storageId = document.getElementById('to_storage').value;

        if (!warehouseId) return;

        let url = `/get-lots-by-warehouse?warehouse_id=${warehouseId}`;

        // ✅ Only send storage if exists
        if (storageId) {
            url += `&storage_id=${storageId}`;
        }

        fetch(`/get-lots-by-warehouse?warehouse_id=${warehouseId}&storage_id=${storageId}`)
                .then(res => res.json())
                .then(data => {

                    let tbody = document.getElementById('tolotTableBody');
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center">No lots found</td></tr>`;
                    }

                    data.forEach(lot => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${lot.lot_number}</td>
                                <td>${lot.crop?.crop_name ?? ''}</td>
                                <td>${lot.accession?.accession_number ?? ''}</td>
                                <td>${lot.storage?.name ?? ''}</td>
                            </tr>
                        `;
                    });

                    document.getElementById('tolotTableCard').classList.remove('d-none');
                });
            
    }
});
</script>
@endpush
