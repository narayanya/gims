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

                            {{-- Selected lots preview --}}
                            <div class="card mt-2 d-none" id="selectedLotsCard">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="ri-list-check me-1"></i>Selected Lots to Transfer <span id="selectedCount" class="badge bg-dark ms-1">0</span></h6>
                                </div>
                                <div class="card-body p-0" style="height: 200px; overflow:auto;">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Lot No</th>
                                                <th>Crop</th>
                                                <th>Accession</th>
                                                <th>Storage</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedLotsBody">
                                            <tr><td colspan="4" class="text-center text-muted">No lots selected yet</td></tr>
                                        </tbody>
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
                    <th>Batch ID</th>
                    <th>From Warehouse</th>
                    <th>From Storage</th>
                    <th>To Warehouse</th>
                    <th>To Storage</th>
                    <th>Lots</th>
                    <th>User</th>
                    <th>Generate</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wTransfers as $t)
                    @php
                        // All lots in this batch
                        $batchTransfers = \App\Models\WarehouseTransfer::with(['lot.crop','lot.accession'])
                            ->where('batch_id', $t->batch_id)->get();
                        $itn = \App\Models\Itn::where('batch_id', $t->batch_id)->first();
                        $batchJson = $batchTransfers->map(function($bt) {
                            return [
                                'crop'      => $bt->lot->crop->crop_name ?? '-',
                                'accession' => $bt->lot->accession->accession_number ?? '-',
                                'lot'       => $bt->lot->lot_number ?? '-',
                                'quantity'  => $bt->quantity,
                            ];
                        })->toJson();
                    @endphp
                    <tr>
                        <td>{{ $t->created_at->format('d-m-Y H:i') }}</td>
                        <td><small class="text-muted">{{ $t->batch_id }}</small></td>
                        <td>{{ $t->fromWarehouse->name ?? '-' }}</td>
                        <td>{{ $t->fromStorage->name ?? '-' }}</td>
                        <td>{{ $t->toWarehouse->name ?? '-' }}</td>
                        <td>{{ $t->toStorage->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $batchTransfers->count() }} lot(s)</span>
                        </td>
                        <td>{{ $t->user->name ?? '-' }}</td>
                        <td>
                            @if($t->status == 0)
                                <a href="{{ route('warehouse-transfer.itn', $t->id) }}"
                                class="btn btn-info btn-sm">
                                    Generate ITN
                                </a>
                            @else
                                <a href="{{ route('warehouse-transfer.itn.print', $itn->id ?? $t->id) }}"
                                target="_blank"
                                class="btn btn-success btn-sm">
                                    Print ITN
                                </a>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm previewTransfer"
                                data-date="{{ $t->created_at->format('d-m-Y H:i') }}"
                                data-from-warehouse="{{ $t->fromWarehouse->name ?? '-' }}"
                                data-from-storage="{{ $t->fromStorage->name ?? '-' }}"
                                data-to-warehouse="{{ $t->toWarehouse->name ?? '-' }}"
                                data-to-storage="{{ $t->toStorage->name ?? '-' }}"
                                data-batch='{{ $batchJson }}'
                            >
                                View
                            </button>
                        </td>
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
@section('modals')
<div class="modal fade" id="itnModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Internal Transfer Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="itnContent">
                <!-- dynamic content -->
            </div>

            <div class="modal-footer">
                <button class="btn btn-success btn-sm">Process</button>
                <button class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Select All checkbox for FROM lots table
    document.getElementById('select_all').addEventListener('change', function () {
        document.querySelectorAll('#lotTableBody input[type="checkbox"]').forEach(cb => {
            cb.checked = this.checked;
        });
        setTimeout(syncSelectedLotsPreview, 10);
    });

    // Sync select_all state when individual checkboxes change
    document.getElementById('lotTableBody').addEventListener('change', function (e) {
        if (e.target.type === 'checkbox') {
            const all = document.querySelectorAll('#lotTableBody input[type="checkbox"]');
            const checked = document.querySelectorAll('#lotTableBody input[type="checkbox"]:checked');
            document.getElementById('select_all').checked = all.length === checked.length;
            document.getElementById('select_all').indeterminate = checked.length > 0 && checked.length < all.length;
            syncSelectedLotsPreview();
        }
    });

document.querySelectorAll('.previewTransfer').forEach(button => {
        button.addEventListener('click', function () {
            const batch = JSON.parse(this.dataset.batch || '[]');

            let rowsHtml = batch.map(item => `
                <tr>
                    <td>${item.crop}</td>
                    <td>${item.accession}</td>
                    <td>${item.lot}</td>
                    <td>${item.quantity}</td>
                </tr>
            `).join('');

            let html = `
                <table class="table table-bordered">
                    <tr><th>Date</th><td>${this.dataset.date}</td></tr>
                    <tr><th>From Warehouse</th><td>${this.dataset.fromWarehouse}</td><th>From Storage</th><td>${this.dataset.fromStorage}</td></tr>
                    <tr><th>To Warehouse</th><td>${this.dataset.toWarehouse}</td><th>To Storage</th><td>${this.dataset.toStorage}</td></tr>
                </table>
                <table class="table">
                    <thead>
                        <tr><th>Crop</th><th>Accession No.</th><th>Lot No.</th><th>Qty.</th></tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>
            `;

            document.getElementById('itnContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('itnModal')).show();
        });
    });


/***************************************/
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

                    // Reset select_all state
                    let selectAll = document.getElementById('select_all');
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                });
            
    }

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

        // Filter destination storages
        fetch(`/get-storages-by-warehouse?warehouse_id=${warehouseId}`)
            .then(res => res.json())
            .then(data => {
                let storageDropdown = document.getElementById('to_storage');
                storageDropdown.innerHTML = `<option value="">Select Storage</option>`;
                data.forEach(s => {
                    storageDropdown.innerHTML += `<option value="${s.id}">${s.storage_id} — ${s.name}</option>`;
                });
            });
    });

    // --- SELECTED LOTS PREVIEW (syncs whenever checkboxes change) ---
    function syncSelectedLotsPreview() {
        const checked = document.querySelectorAll('#lotTableBody input[type="checkbox"]:checked');
        const tbody = document.getElementById('selectedLotsBody');
        const card  = document.getElementById('selectedLotsCard');
        const count = document.getElementById('selectedCount');

        count.textContent = checked.length;

        if (checked.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No lots selected yet</td></tr>';
            card.classList.add('d-none');
            return;
        }

        card.classList.remove('d-none');
        tbody.innerHTML = '';
        checked.forEach(cb => {
            const row = cb.closest('tr');
            const cells = row.querySelectorAll('td');
            tbody.innerHTML += `
                <tr>
                    <td>${cells[1]?.innerText ?? ''}</td>
                    <td>${cells[2]?.innerText ?? ''}</td>
                    <td>${cells[3]?.innerText ?? ''}</td>
                    <td>${cells[4]?.innerText ?? ''}</td>
                </tr>`;
        });
    }

    // Hook into existing checkbox listeners
    document.getElementById('select_all').addEventListener('change', function () {
        setTimeout(syncSelectedLotsPreview, 10);
    });
});
</script>
@endpush
