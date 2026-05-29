@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Lot Quality Control</h3>
                <p class="text-muted mb-0" style="font-size:13px">Select a lot to view and manage its seed quality information</p>
            </div>
            <div class="">
                <a href="{{ route('quality-control.history') }}" class="btn btn-sm btn-outline-secondary">Lot Quality History</a>
                <a href="{{ route('lot-management') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Back to Lot List
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        <div id="alertBox" class="d-none"></div>

        {{-- ── STEP 1: Lot Selection ── --}}
        <div class="card mb-3">
            <div class="card-header bg-light fw-semibold">
                <i class="ri-search-line me-1"></i> Step 1 — Find Lot
            </div>
            <div class="card-body">
                <div class="row g-3">

                    {{-- Search by lot number --}}
                    <div class="col-md-4">
                        <label class="form-label">Search by Lot Number</label>
                        <div class="input-group">
                            <input type="text" id="lot_search" class="form-control" placeholder="e.g. 1564-2017-AccA/1-1483-01">
                            <button class="btn btn-outline-primary" id="lotSearchBtn" type="button">
                                <i class="ri-search-line"></i>
                            </button>
                        </div>
                        <small class="text-muted">Press Enter or click search</small>
                    </div>

                    <div class="col-md-1 d-flex align-items-center justify-content-center pt-3">
                        <span class="text-muted fw-semibold">— OR —</span>
                    </div>

                    {{-- Crop --}}
                    <div class="col-md-2">
                        <label class="form-label">Crop</label>
                        <select id="sel_crop" class="form-select">
                            <option value="">Select Crop</option>
                            @foreach($crops as $c)
                                <option value="{{ $c->id }}">{{ $c->crop_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Accession --}}
                    <div class="col-md-2">
                        <label class="form-label">Accession</label>
                        <select id="sel_accession" class="form-select">
                            <option value="">Select Accession</option>
                            @foreach($accessions as $ac)
                                <option value="{{ $ac->id }}">{{ $ac->accession_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Storage --}}
                    <div class="col-md-2">
                        <label class="form-label">Storage</label>
                        <select id="sel_storage" class="form-select">
                            <option value="">Select Storage</option>
                            @foreach($storages as $s)
                                <option value="{{ $s->id }}">{{ $s->storage_id }} — {{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Lot --}}
                    <div class="col-md-3">
                        <label class="form-label">Lot <span class="text-danger">*</span></label>
                        <select id="sel_lot" class="form-select">
                            <option value="">Select Lot</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── STEP 2: Lot Details + Quality Form (hidden until lot selected) ── --}}
        <div id="lotSection" class="d-none">

            {{-- Lot Info Card --}}
            <div class="card mb-3">
                <div class="card-header bg-light fw-semibold">
                    <i class="ri-box-3-line me-1"></i> Lot Information
                </div>
                <div class="card-body py-2 small">
                    <div class="row g-2">
                        <div class="col-md-3"><span class="text-muted">Lot No.:</span> <strong id="info_lot_number">—</strong></div>
                        <div class="col-md-3"><span class="text-muted">Crop:</span> <span id="info_crop">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Accession:</span> <span id="info_accession">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Accession Name:</span> <span id="info_accession_name">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Storage:</span> <span id="info_storage">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Quantity:</span> <span id="info_quantity">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Rack:</span> <span id="info_rack">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Bin:</span> <span id="info_bin">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Container:</span> <span id="info_container">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Expiry Date:</span> <span id="info_expiry">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Regeneration Date:</span> <span id="info_regeneration_program">—</span></div>
                        <div class="col-md-3"><span class="text-muted">Regeneration Year:</span> <span id="info_regen_year">—</span></div>
                    </div>
                </div>
            </div>

            {{-- Quality Form --}}
            <form id="qualityForm">
                @csrf
                <input type="hidden" id="form_lot_id" name="lot_id" value="">

                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ri-seedling-line me-2"></i>Seed Quality Information
                            </h5>
                            <button type="button" id="addRowBtn" class="btn btn-sm btn-outline-primary">
                                <i class="ri-add-line me-1"></i> Add Row
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0" id="qualityTable">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="min-width:100px">Germination %</th>
                                        <th style="min-width:100px">Moisture %</th>
                                        <th style="min-width:100px">Purity %</th>
                                        <th style="min-width:110px">Chlorophyll %</th>
                                        <th style="min-width:100px">Water Level %</th>
                                        <th style="min-width:130px">Viability Date</th>
                                        <th style="min-width:140px">Health Status</th>
                                        <th style="min-width:160px">Researcher</th>
                                        <th style="min-width:130px">Research Date</th>
                                        <th>Created Date</th>
                                        <th style="width:60px">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="qualityTbody">
                                    {{-- Rows injected by JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2">
                        <button type="button" id="cancelBtn" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-close-line me-1"></i> Cancel
                        </button>
                        <button type="submit" id="saveBtn" class="btn btn-primary btn-sm">
                            <i class="ri-save-line me-1"></i> Save Quality Data
                        </button>
                    </div>
                </div>
            </form>

        </div>{{-- /lotSection --}}

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Users list for researcher dropdown ──────────────────────────────
    const users = @json($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name]));

    const healthStatuses = ['Healthy', 'Infected', 'Damaged', 'Under Treatment'];

    // ── DOM refs ────────────────────────────────────────────────────────
    const selCrop       = document.getElementById('sel_crop');
    const selAccession  = document.getElementById('sel_accession');
    const selStorage    = document.getElementById('sel_storage');
    const selLot        = document.getElementById('sel_lot');
    const lotSection    = document.getElementById('lotSection');
    const qualityTbody  = document.getElementById('qualityTbody');
    const addRowBtn     = document.getElementById('addRowBtn');
    const cancelBtn     = document.getElementById('cancelBtn');
    const qualityForm   = document.getElementById('qualityForm');
    const alertBox      = document.getElementById('alertBox');
    const lotSearchInput = document.getElementById('lot_search');
    const lotSearchBtn  = document.getElementById('lotSearchBtn');

    // ── Helpers ─────────────────────────────────────────────────────────
    function showAlert(type, msg) {
        alertBox.className = `alert alert-${type} alert-dismissible fade show`;
        alertBox.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        alertBox.classList.remove('d-none');
        setTimeout(() => alertBox.classList.add('d-none'), 5000);
    }

    function formatDate(val) {
        if (!val || val === '—') return '—';
        const d = new Date(val);
        if (isNaN(d)) return val;
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    // ── Build a READ-ONLY row for existing quality records ──────────────
    function buildReadOnlyRow(q, index) {
        const tr = document.createElement('tr');
        tr.classList.add('table-light');
        tr.dataset.existing = '1';

        const resName = q.researcher_name || q.researcher_other || '—';

        tr.innerHTML = `
            <td class="text-center">${q.germination_percentage ?? '—'}</td>
            <td class="text-center">${q.moisture_content ?? '—'}</td>
            <td class="text-center">${q.purity_percentage ?? '—'}</td>
            <td class="text-center">${q.chlorophyll_percentage ?? '—'}</td>
            <td class="text-center">${q.water_level_percentage ?? '—'}</td>
            <td class="text-center">${formatDate(q.viability_test_date)}</td>
            <td class="text-center">${q.seed_health_status || '—'}</td>
            <td class="text-center">${resName}</td>
            <td class="text-center">${formatDate(q.research_date)}</td>
            <td class="text-center">${formatDate(q.created_at)}</td>
            <td class="text-center">
                <span class="badge bg-secondary" title="Existing record — read only">
                    <i class="ri-lock-line"></i>
                </span>
            </td>
        `;
        return tr;
    }

    // ── Build an EDITABLE row for new entries ────────────────────────────
    function buildEditableRow(data = {}) {
        const tr = document.createElement('tr');
        tr.dataset.existing = '0';

        // Researcher options
        let resOptions = `<option value="">Select</option>`;
        users.forEach(u => {
            resOptions += `<option value="${u.id}" ${data.researcher_id == u.id ? 'selected' : ''}>${u.name}</option>`;
        });
        resOptions += `<option value="Other">Other</option>`;

        // Health status options
        let healthOptions = `<option value="">Select</option>`;
        healthStatuses.forEach(s => {
            healthOptions += `<option value="${s}" ${data.seed_health_status === s ? 'selected' : ''}>${s}</option>`;
        });

        tr.innerHTML = `
            <td><input type="number" step="0.01" min="0" max="100" name="germination_percentage[]"
                class="form-control form-control-sm" value="${data.germination_percentage ?? ''}" placeholder="e.g. 85.50"></td>
            <td><input type="number" step="0.01" min="0" max="100" name="moisture_content[]"
                class="form-control form-control-sm" value="${data.moisture_content ?? ''}" placeholder="e.g. 12.00"></td>
            <td><input type="number" step="0.01" min="0" max="100" name="purity_percentage[]"
                class="form-control form-control-sm" value="${data.purity_percentage ?? ''}" placeholder="e.g. 98.00"></td>
            <td><input type="number" step="0.01" min="0" max="100" name="chlorophyll_percentage[]"
                class="form-control form-control-sm" value="${data.chlorophyll_percentage ?? ''}" placeholder="e.g. 50.00"></td>
            <td><input type="number" step="0.01" min="0" max="100" name="water_level_percentage[]"
                class="form-control form-control-sm" value="${data.water_level_percentage ?? ''}" placeholder="e.g. 80.00"></td>
            <td><input type="date" name="viability_test_date[]"
                class="form-control form-control-sm" value="${data.viability_test_date ?? ''}"></td>
            <td>
                <select name="seed_health_status[]" class="form-select form-select-sm">
                    ${healthOptions}
                </select>
            </td>
            <td>
                <select name="researcher_id[]" class="form-select form-select-sm researcher-select">
                    ${resOptions}
                </select>
                <input type="text" name="researcher_other[]" class="form-control form-control-sm mt-1 other-input"
                    placeholder="Enter researcher name" value="" style="display:none;">
            </td>
            <td><input type="date" name="research_date[]"
                class="form-control form-control-sm" value="${data.research_date ?? ''}" max="${new Date().toISOString().split('T')[0]}"></td>
            <td class="text-center" name="created_at[]">
                ${data.created_at ? formatDate(data.created_at) : '—'}
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove row">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        `;

        return tr;
    }

    // ── Load lot qualities via AJAX ──────────────────────────────────────
    function loadLotQualities(lotId) {
        fetch(`/quality-control/${lotId}/qualities`)
            .then(r => r.json())
            .then(data => {
                // Fill lot info
                const lot = data.lot;
                document.getElementById('info_lot_number').textContent     = lot.lot_number      || '—';
                document.getElementById('info_crop').textContent           = lot.crop            || '—';
                document.getElementById('info_accession').textContent      = lot.accession       || '—';
                document.getElementById('info_accession_name').textContent = lot.accession_name  || '—';
                document.getElementById('info_storage').textContent        = lot.storage         || '—';
                document.getElementById('info_quantity').textContent       = lot.quantity_show
                    ? `${lot.quantity_show} ${lot.unit || ''}` : (lot.quantity ? `${lot.quantity} ${lot.unit || ''}` : '—');
                document.getElementById('info_rack').textContent           = lot.rack            || '—';
                document.getElementById('info_bin').textContent            = lot.bin             || '—';
                document.getElementById('info_container').textContent      = lot.container       || '—';
                document.getElementById('info_expiry').textContent         = formatDate(lot.expiry_date) || '—';
                document.getElementById('info_regeneration_program').textContent = lot.regeneration_program || '—';
                document.getElementById('info_regen_year').textContent     = lot.regen_year || '—';

                document.getElementById('form_lot_id').value = lot.id;

                // Render rows
                qualityTbody.innerHTML = '';

                if (data.qualities.length > 0) {
                    // Existing records → read-only rows
                    data.qualities.forEach((q, i) => qualityTbody.appendChild(buildReadOnlyRow(q, i)));
                    // Always append one blank editable row after existing ones
                    qualityTbody.appendChild(buildEditableRow());
                } else {
                    // No records yet → one blank editable row
                    qualityTbody.appendChild(buildEditableRow());
                }

                lotSection.classList.remove('d-none');
                lotSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(() => showAlert('danger', 'Failed to load lot quality data.'));
    }

    // ── Crop → filter accessions ─────────────────────────────────────────
    selCrop.addEventListener('change', function () {
        const cropId = this.value;
        selAccession.innerHTML = '<option value="">Loading...</option>';
        selStorage.innerHTML   = '<option value="">Select Storage</option>';
        selLot.innerHTML       = '<option value="">Select Lot</option>';
        lotSection.classList.add('d-none');

        if (!cropId) {
            selAccession.innerHTML = '<option value="">Select Accession</option>';
            return;
        }

        fetch(`/get-accessions/${cropId}`)
            .then(r => r.json())
            .then(data => {
                selAccession.innerHTML = '<option value="">Select Accession</option>';
                data.forEach(ac => {
                    selAccession.innerHTML += `<option value="${ac.id}">${ac.accession_number}</option>`;
                });
            });
    });

    // ── Accession → filter storages ──────────────────────────────────────
    selAccession.addEventListener('change', function () {
        const accId = this.value;
        selStorage.innerHTML = '<option value="">Loading...</option>';
        selLot.innerHTML     = '<option value="">Select Lot</option>';
        lotSection.classList.add('d-none');

        if (!accId) {
            selStorage.innerHTML = '<option value="">Select Storage</option>';
            return;
        }

        fetch(`/get-accession-storages/${accId}`)
            .then(r => r.json())
            .then(data => {
                selStorage.innerHTML = '<option value="">Select Storage</option>';
                data.forEach(s => {
                    selStorage.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                });
            });
    });

    // ── Storage → load lots ──────────────────────────────────────────────
    selStorage.addEventListener('change', function () {
        const id = this.value;
        selLot.innerHTML = '<option value="">Loading...</option>';
        lotSection.classList.add('d-none');

        if (!id) {
            selLot.innerHTML = '<option value="">Select Lot</option>';
            return;
        }

        fetch(`/get-storage-lots/${id}`)
            .then(r => r.json())
            .then(d => {
                selLot.innerHTML = '<option value="">Select Lot</option>';
                (d.lots || []).forEach(lot => {
                    const sq  = lot.seed_quantities?.[0];
                    const qty = sq?.quantity_show ?? sq?.quantity ?? '—';
                    const unit = sq?.unit?.name ?? '';
                    selLot.innerHTML += `<option value="${lot.id}">${lot.lot_number} (Avail: ${qty} ${unit})</option>`;
                });
            });
    });

    // ── Lot selected → load quality data ────────────────────────────────
    selLot.addEventListener('change', function () {
        if (!this.value) {
            lotSection.classList.add('d-none');
            return;
        }
        loadLotQualities(this.value);
    });

    // ── Lot search by number ─────────────────────────────────────────────
    function searchLotByNumber() {
        const keyword = lotSearchInput.value.trim();
        if (!keyword) return;

        fetch(`/get-lot-by-number?lot_number=${encodeURIComponent(keyword)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.status) {
                    showAlert('warning', 'Lot not found. Please check the lot number.');
                    return;
                }
                loadLotQualities(data.lot.id);
            })
            .catch(() => showAlert('danger', 'Search failed. Please try again.'));
    }

    lotSearchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); searchLotByNumber(); }
    });
    lotSearchBtn.addEventListener('click', searchLotByNumber);

    // ── Add row ──────────────────────────────────────────────────────────
    addRowBtn.addEventListener('click', function () {
        qualityTbody.appendChild(buildEditableRow());
    });

    // ── Remove row (delegated — only editable rows can be removed) ───────
    qualityTbody.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row-btn');
        if (!btn) return;

        const editableRows = qualityTbody.querySelectorAll('tr[data-existing="0"]');
        if (editableRows.length <= 1) {
            showAlert('warning', 'At least one new row is required.');
            return;
        }
        btn.closest('tr').remove();
    });

    // ── Toggle "Other" researcher input (delegated) ──────────────────────
    qualityTbody.addEventListener('change', function (e) {
        if (!e.target.classList.contains('researcher-select')) return;
        const td         = e.target.closest('td');
        const otherInput = td.querySelector('.other-input');
        if (!otherInput) return;

        if (e.target.value === 'Other') {
            otherInput.style.display = 'block';
        } else {
            otherInput.style.display = 'none';
            otherInput.value = '';
        }
    });

    // ── Cancel ───────────────────────────────────────────────────────────
    cancelBtn.addEventListener('click', function () {
        lotSection.classList.add('d-none');
        selLot.value = '';
        lotSearchInput.value = '';
    });

    // ── Save form via AJAX ───────────────────────────────────────────────
    qualityForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const lotId  = document.getElementById('form_lot_id').value;
        if (!lotId) { showAlert('danger', 'No lot selected.'); return; }

        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const formData = new FormData(qualityForm);

        fetch(`/quality-control/${lotId}/save`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    || '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message || 'Quality data saved successfully.');
                // Reload the quality rows to reflect saved state
                loadLotQualities(lotId);
            } else {
                const msgs = data.errors
                    ? Object.values(data.errors).flat().join('<br>')
                    : (data.message || 'Save failed.');
                showAlert('danger', msgs);
            }
        })
        .catch(() => showAlert('danger', 'An error occurred while saving.'))
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="ri-save-line me-1"></i> Save Quality Data';
        });
    });

});
</script>
@endpush
