@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Lot Management</h3>
                <p class="text-muted mb-0" style="font-size:13px">Create and manage germplasm lots</p>
            </div>
            <div>
                <div class="d-flex flex-wrap align-items-end gap-2">

        <!-- Search -->
        <div style="min-width:240px;">
            <label class="form-label small mb-1">Search</label>

            <input type="text"
                class="form-control form-control-sm"
                id="accessionSearch"
                placeholder="Lot No, Name, Crop...">
        </div>

        <!-- Crop Filter -->
        <div style="min-width:180px;">
            <label class="form-label small mb-1">Crop</label>

            <select class="form-select form-select-sm" id="cropFilter">

                <option value="">All Crops</option>

                @foreach ($crops as $crop)

                    <option value="{{ strtolower($crop->crop_name) }}">
                        {{ $crop->crop_name }}
                    </option>

                @endforeach

            </select>
        </div>
                
                <a href="{{ route('lot-management.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i> Add New Arrival(Lot)
                </a>
                <a href="{{ route('lot-transfer.index') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i> Inter Transfer Location
                </a>
                <a href="{{ route('lot.export') }}" class="btn btn-sm btn-success">
                            <i class="ri-download-line me-1"></i>Export
                        </a>
               <a href="{{ route('lot.qrprint.all') }}"
   target="_blank"
   class="btn btn-success btn-sm">
    <i class="ri-printer-line me-1"></i>
    Print All QR Codes
</a>
                    </div>
            </div>
            <!--<a href="{{ route('lot-management.create') }}" class="btn btn-primary btn-sm">
                <i class="ri-add-line me-1"></i> Add New Lot
            </a>-->
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Lot List --}}
        <div class="card">
            <div class="card-body p-1">
                @if($lots->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Lot Number</th>
                                <th>Crop</th>
                                <th>Reference No.</th>
                                <th>Sample ID</th>
                                <th>Storage</th>
                                <th>Accession</th>
                                 @if(!auth()->user()->hasRole(['user','researcher']))
                                <th>Actual Qty</th>
                                @endif
                                <th>Qty (Min Stock)</th>
                                <th>Qty (Visible)</th>
                                <th>Unit</th>
                                <th>Expiry Date</th>
                                <th>Regeneration Date</th>
                                <th>Created Date</th>
                                <th>Status</th>
                                <th width="100">Actions</th>
                                <th>Dispose</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lots as $lot)
                            <tr>
                                <td><span class="badge bg-success">{{ $lot->lot_number }}</span></td>
                                <td>{{ $lot->accession?->crop?->crop_name ?? '—' }}</td>
                                <td>{{ $lot->reference_number ?? '—' }}</td>
                                <td>{{ $lot->sample_id ?? '—' }}</td>
                                 <td>{{ $lot->storage?->name ?? '—' }}</td>
                                <td>
                                    @if($lot->accession)
                                        <div>{{ $lot->accession->accession_number }}</div>
                                        <small class="text-muted">{{ $lot->accession->accession_name }}</small>
                                    @else —
                                    @endif
                                </td>
                               
                               <td>
                                    {{ optional($lot->seedQuantities->first())->quantity 
                                        ? number_format($lot->seedQuantities->first()->quantity, 2) 
                                        : '—' }}
                                </td>

                                <td>
                                    {{ optional($lot->seedQuantities->first())->min_quantity 
                                        ? number_format($lot->seedQuantities->first()->min_quantity, 2) 
                                        : '—' }}
                                </td>

                                <td>
                                    {{ optional($lot->seedQuantities->first())->quantity_show 
                                        ? number_format($lot->seedQuantities->first()->quantity_show, 2) 
                                        : '—' }}
                                </td>

                                <td>
                                    {{ optional($lot->seedQuantities->first()?->unit)->name ?? '—' }}
                                </td>
                                
                                
                                <td>{{ $lot->expiry_date 
                                    ? \Carbon\Carbon::parse($lot->expiry_date)->format('d M Y') 
                                    : '—' 
                                }}</td>
                                <td>{{ $lot->recheck_date 
                                    ? \Carbon\Carbon::parse($lot->recheck_date)->format('d M Y') 
                                    : '—' 
                                }}</td>
                                <td>{{ $lot->created_at->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $lot->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($lot->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info viewLotBtn"
                                        data-id="{{ $lot->id }}"
                                        data-accession_id="{{ $lot->accession_id }}"
                                        data-storage_id="{{ $lot->storage_id }}"
                                        data-lot_number="{{ $lot->lot_number }}"
                                        data-lot_master="{{ $lot->lotMaster?->name }}"
                                        data-lot_type="{{ $lot->lotType?->name }}"
                                        data-regeneration_program="{{ $lot->rejuvenation_program }}"
                                        data-regen_year="{{ $lot->regen_year }}"
                                        data-prefix="{{ $lot->prefix }}"
                                        data-sample_id="{{ $lot->sample_id }}"
                                        data-accession="{{ $lot->accession?->accession_number }}"
                                        data-accession_name="{{ $lot->accession?->accession_name }}"
                                        data-storage="{{ $lot->storage?->name }}"
                                        data-expiry="{{ $lot->expiry_date ? \Carbon\Carbon::parse($lot->expiry_date)->format('d M Y') : '' }}"
                                        data-germination="{{ $lot->germination_percent }}"
                                        data-moisture="{{ $lot->moisture_content }}"
                                        data-purity="{{ $lot->purity_percent }}"
                                        data-status="{{ $lot->status }}"
                                        data-description="{{ $lot->description }}"
                                        data-rack="{{ $lot->rack?->name }}"
                                        data-bin="{{ $lot->bin?->name }}"
                                        data-container="{{ $lot->container?->name }}"
                                        title="View">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                     @auth
                                        @if(auth()->user()->hasRole(['super-admin', 'admin',]))
                                        <a href="{{ route('lot-management.edit', $lot->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        @endif
                                    @endauth
                                </td>
                                <td>
                                    @auth
                                         @if(
                                                auth()->user()->hasRole(['super-admin', 'admin']) 
                                                && $lot->status != 'Disposed'
                                            )

                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger disposeBtn"
                                                        title="Dispose/Delete"
                                                        data-id="{{ $lot->id }}"
                                                        data-lot="{{ $lot->lot_number }}"
                                                         data-expiry="{{ $lot->expiry_date 
            ? \Carbon\Carbon::parse($lot->expiry_date)->format('Y-m-d') 
            : '' }}">

                                                    <i class="ri-delete-bin-line"></i>

                                                </button>

                                            @endif
                                    @endauth
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="ri-stack-line fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No lots created yet.</p>
                    <a href="{{ route('lot-management.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i> Create First Lot
                    </a>
                </div>
                @endif
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <div>
                        Showing {{ $lots->firstItem() }} to {{ $lots->lastItem() }}
                        of {{ $lots->total() }} results
                    </div>

                    <div>
                        {{ $lots->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>





{{-- View Lot Modal --}}
<div class="modal fade" id="viewLotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title mb-0"><i class="ri-stack-line me-2"></i>Lot Details</h5>
                    <small id="vl_lot_number" class="opacity-75"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                
                
            </div>
            <div class="modal-body">

                {{-- Lot Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-stack-line me-1"></i>Lot Information</strong></div>
                    <div class="card-body">
                        <div class="row ">
                            <div class="col-md-9">
                                <div class="row g-3 small" >
                            <div class="col-md-4"><span class="text-muted d-block">Number</span><strong id="vl_lot_number2"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Status</span><strong id="vl_status"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Expiry Date</span><strong id="vl_expiry"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Regeneration Date</span><strong id="vl_regeneration"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Regeneration Year</span><strong id="vl_regen_year"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Regeneration Program</span><strong id="vl_regeneration_program"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Prefix</span><strong id="vl_prefix"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Sample Id</span><strong id="vl_sample_id"></strong></div>
                            <div class="col-md-12"><span class="text-muted d-block">Description</span><strong id="vl_description"></strong></div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                            <div id="qrcode"></div>
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
                                <script>
                                new QRCode(document.getElementById("qrcode"), {
                                    text: "{{ url('lots/public/' . $lot->id) }}",
                                    title: "Lot {{ $lot->lot_number }}",
                                    width: 120,
                                    height: 120
                                });
                                </script>
                        </div>
                        </div>
                    </div>
                </div>

                {{-- Accession Details --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-seedling-line me-1"></i>Accession Details</strong></div>
                    <div class="card-body">
                        <div class="row g-3 small" id="vl_accession_section">
                            <div class="col-md-3"><span class="text-muted d-block">Accession No.</span><strong id="vl_acc_number"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Accession Name</span><strong id="vl_acc_name"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Crop</span><strong id="vl_acc_crop"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Scientific Name</span><strong id="vl_acc_scientific"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Quantity</span><strong id="vl_acc_qty"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Status</span><strong id="vl_acc_status"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Barcode</span><strong id="vl_acc_barcode"></strong></div>
                        </div>
                    </div>
                </div>

                {{-- Quantity Parameters --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-test-tube-line me-1"></i>Quantity Parameters</strong></div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref No.</th>
                                    <th>Seeds</th>
                                    <th>Bags</th>
                                    <th>Seed Wt (g)</th>
                                    <th>Quantity</th>
                                    <th>Min Stock</th>
                                    <th>Visible</th>
                                    <th>Unit</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody id="vl_quantity_tbody">
                                <tr><td colspan="9" class="text-center text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Quality Parameters --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-test-tube-line me-1"></i>Quality Parameters</strong></div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Germination %</th>
                                    <th>Moisture %</th>
                                    <th>Purity %</th>
                                    <th>Health</th>
                                    <th>Viability Date</th>
                                    <th>Researcher</th>
                                    <th>Research Date</th>
                                </tr>
                            </thead>
                            <tbody id="vl_quality_tbody">
                                <tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Storage Details --}}
                <div class="card mb-0">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-archive-line me-1"></i>Storage Details</strong></div>
                    <div class="card-body">
                        <div class="row g-3 small" id="vl_storage_section">
                            <div class="col-md-3"><span class="text-muted d-block">Warehouse</span><strong id="vl_st_warehouse"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Storage Name</span><strong id="vl_st_name"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Storage ID</span><strong id="vl_st_id"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Type</span><strong id="vl_st_type"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Condition</span><strong id="vl_st_condition"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Temperature</span><strong id="vl_st_temp"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Humidity</span><strong id="vl_st_humidity"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Capacity</span><strong id="vl_st_capacity"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Available</span><strong id="vl_st_available"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Rack</span><strong id="vl_st_rack"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Bin</span><strong id="vl_st_bin"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Container</span><strong id="vl_st_container"></strong></div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="disposeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="disposeForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        Dispose Lot
                    </h5>
                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Lot Number
                            </label>
                            <input type="text"
                                   id="disposeLotNumber"
                                   class="form-control"
                                   readonly>
                        </div>
                        <div class="col-md-3 mb-3">

    <label class="form-label">
        Expiry Date
    </label>

    <input type="date"
           id="disposeLotExpirydate"
           class="form-control"
           readonly>

</div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                Dispose Date
                            </label>
                            <input type="date"
                                   name="dispose_date"
                                   class="form-control"
                                   value="{{ date('Y-m-d') }}"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Dispose Type
                            </label>
                            <select name="dispose_type"
                                    class="form-select"
                                    required>
                                <option value="">Select Type </option>
                                <option value="Expired">Expired                                </option>
                                <option value="Damaged">Damaged                                </option>
                                <option value="Infected">Infected                                </option>
                                <option value="Moisture">Moisture                                </option>
                                <option value="Other"> Other                                </option>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status"
                                    class="form-select">

                                <option value="Disposed">
                                    Disposed
                                </option>

                            </select>

                        </div>

                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Dispose Reason
                            </label>

                            <textarea name="dispose_reason"
                                      class="form-control"
                                      rows="4"
                                      required></textarea>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">

                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-danger">

                        Dispose Lot
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── View Lot ──────────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {

        const btn = e.target.closest('.viewLotBtn');
        if (!btn) return;

        const d = btn.dataset;

        const set = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = (val !== null && val !== undefined && val !== '') ? val : '—';
        };

        // Reset tables before loading
        document.getElementById('vl_quantity_tbody').innerHTML = '<tr><td colspan="9" class="text-center text-muted">Loading...</td></tr>';
        document.getElementById('vl_quality_tbody').innerHTML  = '<tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>';

        // Lot info
        set('vl_lot_number',  d.lot_number);
        set('vl_lot_number2', d.lot_number);
        set('vl_lot_master',  d.lot_master);
        set('vl_lot_type',    d.lot_type);
        set('vl_expiry',      d.expiry);
        set('vl_regeneration', d.germination);
        set('vl_regen_year', d.regen_year);
        set('vl_regeneration_program', d.regeneration_program);
        set('vl_prefix', d.prefix);
        set('vl_sample_id', d.sample_id);
        set('vl_status',      d.status);
        set('vl_description', d.description);
        set('vl_st_rack',     d.rack);
        set('vl_st_bin',      d.bin);
        set('vl_st_container', d.container);

        // ✅ Accession
        if (d.accession_id) {
            fetch(`/lot-management/accession/${d.accession_id}`)
                .then(r => r.json())
                .then(a => {
                    set('vl_acc_number', a.accession_number);
                    set('vl_acc_name', a.accession_name);
                    set('vl_acc_crop', a.crop);
                    set('vl_acc_scientific', a.scientific_name);
                    set('vl_acc_qty', a.quantity_show);
                    set('vl_acc_warehouse', a.warehouse);
                    set('vl_acc_status', a.status);
                    set('vl_acc_barcode', a.barcode);
                });
        }

        // ✅ Storage
        if (d.storage_id) {
            fetch(`/lot-management/storage/${d.storage_id}`)
                .then(r => r.json())
                .then(s => {
                    set('vl_st_name', s.name);
                    set('vl_st_id', s.storage_id);
                    set('vl_st_warehouse', s.warehouse);
                    set('vl_st_type', s.storage_type);
                    set('vl_st_condition', s.storage_condition);
                    set('vl_st_temp', s.temperature ? s.temperature + ' °C' : null);
                    set('vl_st_humidity', s.humidity ? s.humidity + ' %' : null);
                    set('vl_st_capacity', s.capacity ? s.capacity + ' ' + (s.unit || '') : null);
                    set('vl_st_available', s.available ? s.available + ' ' + (s.unit || '') : null);

                });
        }

        // ✅ Quantity
        if (d.id) {
            fetch(`/lot-management/${d.id}/quantity`)
                .then(r => r.json())
                .then(rows => {
                    const tbody = document.getElementById('vl_quantity_tbody');
                    if (!rows || rows.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No quantity data</td></tr>';
                        return;
                    }
                    tbody.innerHTML = rows.map(q => `
                        <tr>
                            <td>${q.reference_number || '—'}</td>
                            <td>${q.number_of_seeds || '—'}</td>
                            <td>${q.number_of_bags || '—'}</td>
                            <td>${q.per_seed_weight ? q.per_seed_weight + ' g' : '—'}</td>
                            <td>${q.quantity || '—'}</td>
                            <td>${q.min_quantity || '—'}</td>
                            <td>${q.quantity_show || '—'}</td>
                            <td>${q.unit || '—'}</td>
                            <td>${q.quantity_updated ? new Date(q.quantity_updated).toLocaleDateString('en-GB') : '—'}</td>
                        </tr>
                    `).join('');
                });
        }

        // ✅ Quality
        if (d.id) {
            fetch(`/lot-management/${d.id}/quality`)
                .then(r => r.json())
                .then(rows => {
                    const tbody = document.getElementById('vl_quality_tbody');
                    if (!rows || rows.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No quality data</td></tr>';
                        return;
                    }
                    tbody.innerHTML = rows.map(q => `
                        <tr>
                            <td>${q.germination_percent ? q.germination_percent + ' %' : '—'}</td>
                            <td>${q.moisture_content ? q.moisture_content + ' %' : '—'}</td>
                            <td>${q.purity_percent ? q.purity_percent + ' %' : '—'}</td>
                            <td>${q.seed_health_status || '—'}</td>
                            <td>${q.viability_test_date ? new Date(q.viability_test_date).toLocaleDateString('en-GB') : '—'}</td>
                            <td>${q.researcher || '—'}</td>
                            <td>${q.research_date ? new Date(q.research_date).toLocaleDateString('en-GB') : '—'}</td>
                        </tr>
                    `).join('');
                });
        }

        // ✅ SHOW MODAL
        new bootstrap.Modal(document.getElementById('viewLotModal')).show();
    });

    // ── Storage select → load details ────────────────────────────────────
    let _storageData    = null;
    let _accessionData  = null;

    function updateBalance() {
        const balEl = document.getElementById('sd_balance');
        if (!balEl) return;
        if (_storageData && _accessionData) {
            const avail   = parseFloat(_storageData.available || 0);
            const accQty  = parseFloat(_accessionData.quantity_show ?? _accessionData.quantity ?? 0);
            const balance = avail - accQty;
            balEl.textContent = `${balance.toFixed(2)} ${_storageData.unit || ''}`;
            balEl.className   = 'ms-1 ' + (balance < 0 ? 'text-danger' : 'text-success');
        } else {
            balEl.textContent = '—';
            balEl.className   = 'ms-1';
        }
    }


    const searchInput = document.getElementById('accessionSearch');
    const cropFilter  = document.getElementById('cropFilter');

    const tableRows = document.querySelectorAll('table tbody tr');

    function filterTable() {

        let search = searchInput.value.toLowerCase().trim();
        let crop   = cropFilter.value.toLowerCase().trim();

        tableRows.forEach(row => {

            let rowText = row.innerText.toLowerCase();

            // Crop column text
            let cropText = row.children[1]?.innerText.toLowerCase() || '';

            let matchSearch = search === '' || rowText.includes(search);

            let matchCrop = crop === '' || cropText.includes(crop);

            if (matchSearch && matchCrop) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }

        });
    }

    // Search typing
    searchInput.addEventListener('keyup', filterTable);

    // Crop change
    cropFilter.addEventListener('change', filterTable);


});

document.querySelectorAll('.disposeBtn').forEach(btn => {

    btn.addEventListener('click', function () {

        let id     = this.dataset.id;
        let lot    = this.dataset.lot;
        let expiry = this.dataset.expiry;

        document.getElementById('disposeLotNumber').value = lot;

        let expiryInput = document.getElementById('disposeLotExpirydate');

        if (expiryInput) {
            expiryInput.value = expiry;
        }

        document.getElementById('disposeForm').action =
            `/lot-management/dispose/${id}`;

        new bootstrap.Modal(
            document.getElementById('disposeModal')
        ).show();

    });

});
</script>
@endsection

