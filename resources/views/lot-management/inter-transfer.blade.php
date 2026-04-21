@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Lot Inter-Transfer</h3>
                <p class="text-muted mb-0" style="font-size:13px">Move a lot from one storage location to another</p>
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

        <form method="POST" action="{{ route('lot-transfer.store') }}">
            @csrf
            <div class="row g-3">

                {{-- ── FROM ── --}}
                <div class="col-md-5">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="ri-map-pin-line me-1"></i>Pick From</h6>
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Storage <span class="text-danger">*</span></label>
                                <select id="from_storage" class="form-select">
                                    <option value="">Select Storage</option>
                                    @foreach($storages as $s)
                                        <option value="{{ $s->id }}">{{ $s->storage_id }} — {{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="from_storageInfo" class="card border bg-light mb-3 d-none">
                                <div class="card-body py-2 small">
                                    <div class="row g-1">
                                        <div class="col-6"><span class="text-muted">Warehouse:</span> <span id="from_warehouse">—</span></div>
                                        <div class="col-6"><span class="text-muted">Type:</span> <span id="from_type">—</span></div>
                                        <div class="col-6"><span class="text-muted">Condition:</span> <span id="from_condition">—</span></div>
                                        <div class="col-6"><span class="text-muted">Time:</span> <span id="from_time">—</span></div>
                                        <div class="col-6"><span class="text-muted">Temp:</span> <span id="from_temp">—</span></div>
                                        <div class="col-6"><span class="text-muted">Humidity:</span> <span id="from_humidity">—</span></div>

                                        <div class="col-6"><span class="text-muted">Capacity:</span> <span id="from_capacity">—</span></div>
                                        <div class="col-6"><span class="text-muted">Available:</span> <span id="from_available">—</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Lot <span class="text-danger">*</span></label>
                                <select name="from_lot_id" id="from_lot" class="form-select" required>
                                    <option value="">Select Lot</option>
                                </select>
                            </div>

                            <div id="from_lotInfo" class="card border bg-light d-none">
                                <div class="card-body py-2 small">
                                    <div class="row g-1">
                                        <div class="col-6"><span class="text-muted">Lot No.:</span> <span id="fl_lot_number">—</span></div>
                                        <div class="col-6"><span class="text-muted">Crop:</span> <span id="fl_crop">—</span></div>
                                        <div class="col-6"><span class="text-muted">Accession:</span> <span id="fl_accession">—</span></div>
                                        <div class="col-6"><span class="text-muted">Quantity:</span> <span id="fl_qty">—</span></div>
                                        <div class="col-6"><span class="text-muted">Avail (User):</span> <span id="fl_qty_show" class="text-success fw-semibold">—</span></div>
                                        <div class="col-6"><span class="text-muted">Unit:</span> <span id="fl_unit">—</span></div>

                                        <div class="col-6"><span class="text-muted">Section:</span> <span id="fl_section">—</span></div>
                                        <div class="col-6"><span class="text-muted">Rack:</span> <span id="fl_rack">—</span></div>
                                        <div class="col-6"><span class="text-muted">Bin:</span> <span id="fl_bin">—</span></div>
                                        <div class="col-6"><span class="text-muted">Container:</span> <span id="fl_container">—</span></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Arrow ── --}}
                <div class="col-md-1 d-flex align-items-center justify-content-center">
                    <div class="bg-light rounded-circle p-3 shadow-sm">
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
                                <label class="form-label">Storage <span class="text-danger">*</span></label>
                                <select name="to_storage_id" id="to_storage" class="form-select" required>
                                    <option value="">Select Storage</option>
                                    @foreach($storages as $s)
                                        <option value="{{ $s->id }}">{{ $s->storage_id }} — {{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="to_storageInfo" class="card border bg-light mb-3 d-none">
                                <div class="card-body py-2 small">
                                    <div class="row g-1">
                                        <div class="col-6"><span class="text-muted">Warehouse:</span> <span id="to_warehouse">—</span></div>
                                        <div class="col-6"><span class="text-muted">Type:</span> <span id="to_type">—</span></div>
                                        <div class="col-6"><span class="text-muted">Condition:</span> <span id="to_condition">—</span></div>
                                        <div class="col-6"><span class="text-muted">Time:</span> <span id="to_time">—</span></div>
                                        <div class="col-6"><span class="text-muted">Temp:</span> <span id="to_temp">—</span></div>
                                        <div class="col-6"><span class="text-muted">Humidity:</span> <span id="to_humidity">—</span></div>

                                        <div class="col-6"><span class="text-muted">Capacity:</span> <span id="to_capacity">—</span></div>
                                        <div class="col-6"><span class="text-muted">Available:</span> <span id="to_available">—</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Section</label>
                                    <select name="section_id" id="to_section" class="form-select">
                                        <option value="">Select Section</option>
                                        @foreach($sections as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Rack</label>
                                    <select name="rack_id" id="to_rack" class="form-select">
                                        <option value="">Select Rack</option>
                                        @foreach($racks as $r)
                                            <option value="{{ $r->id }}" data-section="{{ $r->section_id }}">{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Bin</label>
                                    <select name="bin_id" id="to_bin" class="form-select">
                                        <option value="">Select Bin</option>
                                        @foreach($bins as $b)
                                            <option value="{{ $b->id }}" data-rack="{{ $b->rack_id }}">{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Container</label>
                                    <select name="container_id" id="to_container" class="form-select">
                                        <option value="">Select Container</option>
                                        @foreach($containers as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}{{ $c->container_type ? ' ('.$c->container_type.')' : '' }}</option>
                                        @endforeach
                                    </select>
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
    <div class="col-md-12">
        <div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Last 10 Transfers</h5>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Lot</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Section</th>
                    <th>Rack</th>
                    <th>Bin</th>
                    <th>Container</th>
                    <th>Qty</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $t)
                    <tr>
                        <td>{{ $t->created_at->format('d-m-Y H:i') }}</td>
                        <td>{{ $t->lot->lot_number ?? '-' }}</td>
                        <td>{{ $t->fromStorage->name ?? '-' }}</td>
                        <td>{{ $t->toStorage->name ?? '-' }}</td>
                        <td>{{ $t->toSection->name ?? '-' }}</td>
                        <td>{{ $t->toRack->name ?? '-' }}</td>
                        <td>{{ $t->toBin->name ?? '-' }}</td>
                        <td>{{ $t->toContainer->name ?? '-' }}</td>
                        <td>{{ $t->quantity }}</td>
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
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── FROM: Storage → load lots ─────────────────────────────────────────
    document.getElementById('from_storage').addEventListener('change', function () {
        const id = this.value;
        const infoBox = document.getElementById('from_storageInfo');
        const lotSel  = document.getElementById('from_lot');
        lotSel.innerHTML = '<option value="">Loading...</option>';
        document.getElementById('from_lotInfo').classList.add('d-none');

        if (!id) { infoBox.classList.add('d-none'); lotSel.innerHTML = '<option value="">Select Lot</option>'; return; }

        fetch(`/get-storage-lots/${id}`)
            .then(r => r.json())
            .then(d => {

                document.getElementById('from_warehouse').textContent  = d.storage.warehouse || '—';
                document.getElementById('from_type').textContent       = d.storage.storage_type || '—';
                document.getElementById('from_condition').textContent  = d.storage.storage_condition || '—';
                document.getElementById('from_time').textContent       = d.storage.storage_time || '—';

                document.getElementById('from_temp').textContent =
                    d.storage.temperature ? `${d.storage.temperature} ` : '—';

                document.getElementById('from_humidity').textContent =
                    d.storage.humidity ? `${d.storage.humidity} ` : '—';

                document.getElementById('from_capacity').textContent  = d.storage.capacity ?? '—';
                document.getElementById('from_available').textContent = d.available ?? '—';
                infoBox.classList.remove('d-none');

                lotSel.innerHTML = '<option value="">Select Lot</option>';
                d.lots.forEach(lot => {
                    const sq  = lot.seed_quantities?.[0];
                    const qty = sq?.quantity ?? lot.quantity ?? 0;
                    const qtyShow = sq?.quantity_show ?? '—';
                    const unit    = sq?.unit?.name ?? '';
                    lotSel.innerHTML += `<option value="${lot.id}"
                        data-lotno="${lot.lot_number}"
                        data-crop="${lot.accession?.crop?.crop_name || '—'}"
                        data-accession="${lot.accession?.accession_number || '—'}"
                        data-qty="${qty}"
                        data-qty_show="${qtyShow}"
                        data-unit="${unit}"
                        data-section="${lot.section?.name || '—'}"
data-rack="${lot.rack?.name || '—'}"
data-bin="${lot.bin?.name || '—'}"
data-container="${lot.container?.name || '—'}">
                        ${lot.lot_number} (Avail: ${qtyShow} ${unit})
                    </option>`;
                });
            });
    });

    // ── FROM: Lot → show details ──────────────────────────────────────────
    document.getElementById('from_lot').addEventListener('change', function () {
        const sel = this.options[this.selectedIndex];
        const box = document.getElementById('from_lotInfo');
        if (!this.value) { box.classList.add('d-none'); return; }
        document.getElementById('fl_lot_number').textContent = sel.dataset.lotno   || '—';
        document.getElementById('fl_crop').textContent       = sel.dataset.crop    || '—';
        document.getElementById('fl_accession').textContent  = sel.dataset.accession || '—';
        document.getElementById('fl_qty').textContent        = sel.dataset.qty     || '—';
        document.getElementById('fl_qty_show').textContent   = sel.dataset.qty_show || '—';
        document.getElementById('fl_unit').textContent       = sel.dataset.unit    || '—';
        document.getElementById('fl_section').textContent       = sel.dataset.section    || '—';
        document.getElementById('fl_rack').textContent       = sel.dataset.rack    || '—';
        document.getElementById('fl_bin').textContent       = sel.dataset.bin    || '—';
        document.getElementById('fl_container').textContent       = sel.dataset.container    || '—';
        box.classList.remove('d-none');
    });

    // ── TO: Storage → show capacity ───────────────────────────────────────
    document.getElementById('to_storage').addEventListener('change', function () {
        const id  = this.value;
        const box = document.getElementById('to_storageInfo');
        if (!id) { box.classList.add('d-none'); return; }

        fetch(`/lot-management/storage/${id}`)
            .then(r => r.json())
            .then(d => {
                document.getElementById('to_warehouse').textContent  = d.warehouse || '—';
                document.getElementById('to_type').textContent       = d.storage_type || '—';
                document.getElementById('to_condition').textContent  = d.storage_condition || '—';
                document.getElementById('to_time').textContent       = d.storage_time || '—';
                document.getElementById('to_temp').textContent       = d.temperature || '—';
                document.getElementById('to_humidity').textContent       = d.humidity || '—';


                document.getElementById('to_capacity').textContent  = d.capacity  ? `${d.capacity} ${d.unit||''}` : '—';
                document.getElementById('to_available').textContent = d.available ? `${d.available} ${d.unit||''}` : '—';
                box.classList.remove('d-none');
            });
    });

    // ── TO: Section → filter Racks ────────────────────────────────────────
    document.getElementById('to_section').addEventListener('change', function () {
        const sid = this.value;
        Array.from(document.getElementById('to_rack').options).forEach(o => {
            o.hidden = sid && o.value && o.dataset.section != sid;
        });
        document.getElementById('to_rack').value = '';
        Array.from(document.getElementById('to_bin').options).forEach(o => o.hidden = false);
    });

    // ── TO: Rack → filter Bins ────────────────────────────────────────────
    document.getElementById('to_rack').addEventListener('change', function () {
        const rid = this.value;
        Array.from(document.getElementById('to_bin').options).forEach(o => {
            o.hidden = rid && o.value && o.dataset.rack != rid;
        });
        document.getElementById('to_bin').value = '';
    });

});
</script>
@endpush
