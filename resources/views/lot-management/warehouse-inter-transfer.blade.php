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

                            <div class="mb-3">
                                <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select name="from_warehouse_id" id="from_warehouse" class="form-select" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
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
                     
                            <div class="card mt-3 d-none" id="lotTableCard">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Available Lots</h6>
                                </div>

                                <div class="card-body p-0">
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

                            <div class="mb-3">
                                <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select name="to_warehouse_id" id="to_warehouse" class="form-select" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

    const warehouses = @json($warehouses);

    function getWarehouse(id) {
        return warehouses.find(w => w.id == id);
    }

    // ✅ FROM
    document.getElementById('from_warehouse').addEventListener('change', function () {

        let w = getWarehouse(this.value);

        if (!w) return;

        document.getElementById('from_warehouseInfo').classList.remove('d-none');

        document.getElementById('fw_country').innerText = w.country?.name ?? '—';
        document.getElementById('fw_state').innerText = w.state?.name ?? '—';
        document.getElementById('fw_district').innerText = w.district?.name ?? '—';
        document.getElementById('fw_city').innerText = w.city?.name ?? '—';
    });

    // ✅ TO (FIXED)
    document.getElementById('to_warehouse').addEventListener('change', function () {

        let w = getWarehouse(this.value);

        if (!w) return;

        document.getElementById('to_warehouseInfo').classList.remove('d-none');

        document.getElementById('tw_country').innerText = w.country?.name ?? '—';
        document.getElementById('tw_state').innerText = w.state?.name ?? '—';
        document.getElementById('tw_district').innerText = w.district?.name ?? '—';
        document.getElementById('tw_city').innerText = w.city?.name ?? '—';
    });
    //console.log(warehouses);

    // ✅ LOAD LOTS BY WAREHOUSE
    document.getElementById('from_warehouse').addEventListener('change', function () {

        let warehouseId = this.value;

        if (!warehouseId) return;

        fetch(`/get-lots-by-warehouse?warehouse_id=${warehouseId}`)
            .then(res => res.json())
            .then(data => {

                let tbody = document.getElementById('lotTableBody');
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center">No lots found</td></tr>`;
                }

                data.forEach(lot => {
                    tbody.innerHTML += `
                        <tr>
                            <td>
                                <input type="checkbox" name="lot_ids[]" value="${lot.id}">
                            </td>
                            <td>${lot.lot_number}</td>
                            <td>${lot.crop?.crop_name ?? ''}</td>
                            <td>${lot.accession?.accession_number ?? ''}</td>
                        </tr>
                    `;
                });

                document.getElementById('lotTableCard').classList.remove('d-none');
            })
            .catch(err => console.error(err));
    });

    // ✅ SELECT ALL
    document.getElementById('select_all').addEventListener('change', function () {

        let checkboxes = document.querySelectorAll('input[name="lot_ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

});
</script>
@endpush
