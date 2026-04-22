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
                <a href="{{ route('lot-management.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i> Add New Lot
                </a>
                <a href="{{ route('inter.transfer') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i> Inter Transfer Location
                </a>
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
                                <th>Created Date</th>
                                <th>Status</th>
                                <th width="100">Actions</th>
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
                                
                                
                                <td>{{ $lot->accession?->expiry_date 
                                    ? \Carbon\Carbon::parse($lot->accession->expiry_date)->format('d M Y') 
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
                                        data-accession="{{ $lot->accession?->accession_number }}"
                                        data-accession_name="{{ $lot->accession?->accession_name }}"
                                        data-storage="{{ $lot->storage?->name }}"
                                        data-expiry="{{ $lot->expiry_date ? \Carbon\Carbon::parse($lot->expiry_date)->format('d M Y') : '' }}"
                                        data-germination="{{ $lot->germination_percent }}"
                                        data-moisture="{{ $lot->moisture_content }}"
                                        data-purity="{{ $lot->purity_percent }}"
                                        data-status="{{ $lot->status }}"
                                        data-description="{{ $lot->description }}"
                                        title="View">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <a href="{{ route('lot-management.edit', $lot->id) }}" class="btn btn-sm btn-outline-warning d-none" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
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
            </div>
            @if($lots->hasPages())
            <div class="card-footer">{{ $lots->links() }}</div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('modals')


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
                        <div class="row g-3 small">
                            <div class="col-md-3"><span class="text-muted d-block">Number</span><strong id="vl_lot_number2"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Status</span><strong id="vl_status"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Expiry Date</span><strong id="vl_expiry"></strong></div>
                            <div class="col-md-12"><span class="text-muted d-block">Description</span><strong id="vl_description"></strong></div>
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
                            <div class="col-md-3"><span class="text-muted d-block">Expiry Date</span><strong id="vl_acc_expiry"></strong></div>
                        </div>
                    </div>
                </div>

                {{-- Quantity Parameters --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-test-tube-line me-1"></i>Quantity Parameters</strong></div>
                    <div class="card-body">
                        <div class="row g-3 small">
                            <div class="col-md-4"><span class="text-muted d-block">Actual Quantity</span><strong id="vl_quantity"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Visible Quantity</span><strong id="vl_quantity_show"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Unit</span><strong id="vl_capacity_unit_id"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Quantity Last Updated</span><strong id="vl_qty_updated"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Number of Seeds</span><strong id="vl_number_of_seeds"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Number of Seeds</span><strong id="vl_number_of_bags"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Per Seed Weight</span><strong id="vl_per_seed_weight"></strong>

                        </div>
                    </div>
                </div>

                {{-- Quality Parameters --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-test-tube-line me-1"></i>Quality Parameters</strong></div>
                    <div class="card-body">
                        <div class="row g-3 small">
                            <div class="col-md-4"><span class="text-muted d-block">Germination %</span><strong id="vl_germination"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Moisture %</span><strong id="vl_moisture"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Purity %</span><strong id="vl_purity"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Health</span><strong id="vl_health"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Viability Date</span><strong id="vl_viability"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Researcher</span><strong id="vl_researcher"></strong></div>
                            <div class="col-md-4"><span class="text-muted d-block">Research Date</span><strong id="vl_research_date"></strong></div>

                        </div>
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
@endsection

@push('scripts')
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

        // Lot info
        set('vl_lot_number',  d.lot_number);
        set('vl_lot_number2', d.lot_number);
        set('vl_lot_master',  d.lot_master);
        set('vl_lot_type',    d.lot_type);
        set('vl_expiry',      d.expiry);
        set('vl_status',      d.status);
        set('vl_description', d.description);

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
                    set('vl_acc_expiry', a.expiry_date);
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
                .then(q => {
                    set('vl_quantity', q.quantity ? q.quantity + ' ' + (q.unit || '') : null);
                    set('vl_number_of_seeds', q.number_of_seeds);
                    set('vl_number_of_bags', q.number_of_bags);
                    set('vl_per_seed_weight', q.per_seed_weight ? q.per_seed_weight + ' g' : null);
                    set('vl_capacity_unit_id', q.unit);
                    set('vl_quantity_show', q.quantity_show ? q.quantity_show + ' ' + (q.unit || '') : null);
                    set('vl_qty_updated',
                        q.quantity_updated && !isNaN(Date.parse(q.quantity_updated))
                            ? new Date(q.quantity_updated).toLocaleDateString('en-GB')
                            : q.quantity_updated
                    );
                });
        }

        // ✅ Quality
        if (d.id) {
            fetch(`/lot-management/${d.id}/quality`)
                .then(r => r.json())
                .then(q => {
                    set('vl_germination', q.germination_percent ? q.germination_percent + ' %' : null);
                    set('vl_moisture', q.moisture_content ? q.moisture_content + ' %' : null);
                    set('vl_purity', q.purity_percent ? q.purity_percent + ' %' : null);
                    set('vl_health', q.seed_health_status);
                    set('vl_viability',
                        q.viability_test_date && !isNaN(Date.parse(q.viability_test_date))
                            ? new Date(q.viability_test_date).toLocaleDateString('en-GB')
                            : q.viability_test_date
                    );
                    
                    set('vl_researcher', q.researcher);
                    set('vl_research_date',
                        q.research_date && !isNaN(Date.parse(q.research_date))
                            ? new Date(q.research_date).toLocaleDateString('en-GB')
                            : q.research_date
                    );
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


});
</script>
@endpush
