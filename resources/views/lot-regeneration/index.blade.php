@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Lot Regeneration</h3>
                <p class="text-muted mb-0" style="font-size:13px">Update and manage Lot Regeneration</p>
            </div>
            <div >
                <a href="" class="btn btn-primary btn-sm">
                    Lot Regeneration History
                </a>
            </div>
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
    <div class="row">
        <div class="col-md-6">
            <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lot Number</th>
                        <th>Lot Entry Date</th>
                        <th>Expiry Date</th>
                        <th>Regeneration Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($lots as $key => $lot)
                    <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>{{ $lot->lot_number }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($lot->created_at)->format('d-m-Y') }}
                        </td>

                        <td>
                            {{ $lot->expiry_date 
                                ? \Carbon\Carbon::parse($lot->expiry_date)->format('d-m-Y') 
                                : '-' }}
                        </td>

                        <td>
                            {{ $lot->regeneration_date 
                                ? \Carbon\Carbon::parse($lot->regeneration_date)->format('d-m-Y') 
                                : '-' }}
                        </td>

                        <td>
                            <button class="btn btn-sm btn-outline-secondary regenerationBtn "
                                data-id="{{ $lot->id }}"
                                data-lot="{{ $lot->lot_number }}">
                                Action
                            </button>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        No Record Found
                    </td>
                </tr>
                @endforelse
                </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap p-2">
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
        <div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('lot-regeneration.store') }}">
                @csrf
                            <div class="row">
                                
                                <div class="col-md-12">
                                    <label class="form-label">Search Lot Number </label>
                                    <input type="text" id="lot_search" class="form-control" placeholder="Search lot number" />
                                    <small class="text-muted">e.g.: 1564-2017-2018/2-MB-1483-02</small>
                                </div>
                                <div class="col-md-12 text-center">
                                    or
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Crop </label>
                                    <select id="from_crop" class="form-select">
                                        <option value="">Select Crop</option>
                                        @foreach($crops as $c)
                                            <option value="{{ $c->id }}" data-regen="{{ $c->regeneration_cut_year }}" data-start="{{ $c->season_start_month_id ?? '' }}"
    data-end="{{ $c->season_end_month_id ?? '' }}" >{{ $c->crop_name }}-{{ $c->crop_code }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Accession </label>
                                    <select id="from_accesstion" class="form-select">
                                        <option value="">Select Accession</option>
                                        @foreach($accessions as $ac)
                                            <option value="{{ $ac->id }}">{{ $ac->accession_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Storage <span class="text-danger">*</span></label>
                                <select name="from_storage_id" id="from_storage" class="form-select">
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
                                        <div class="col-6"><span class="text-muted">Available Capacity:</span> <span id="from_available">—</span></div>
                                        <div class="col-6"><span class="text-muted">Total Lot Quantity:</span> <span id="total_lot_qty">—</span></div>
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
                                       
                                        <div class="col-6"><span class="text-muted">Rack:</span> <span id="fl_rack">—</span></div>
                                        <div class="col-6"><span class="text-muted">Bin:</span> <span id="fl_bin">—</span></div>
                                        <div class="col-6"><span class="text-muted">Container:</span> <span id="fl_container">—</span></div>
                                    </div>
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Regeneration Cut of Year <span class="text-danger">*</span></label> 
                                <input type="number" id="regen_year" name="regen_year" class="form-control"
                                    value="{{ old('regen_year', $accession->regen_year ?? '') }}"
                                    placeholder="Enter number only" min="0.1" max="100" step="0.1">
                                
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Next Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" id="expiry_date" name="expiry_date" class="form-control"
                                    value="{{ old('expiry_date', now()->addMonth(0)->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                                    <small class="text-muted">
                                        Old:
                                        <span id="old1_expiry_date">
                                            {{ old('expiry_date', now()->format('d-m-Y')) }}
                                        </span>
                                    </small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Next Regeneration Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" id="recheck_date" name="recheck_date" class="form-control"
                                    value="{{ old('recheck_date', $accession->recheck_date ?? '') }}" min="{{ date('Y-m-d') }}">
                                    <small class="text-muted">
                                        Old:
                                        <span id="old_recheck_date">
                                            {{ old('recheck_date', $accession->recheck_date ?? '') }}
                                        </span>
                                    </small>
                            </div>
                        </div>

                            <div class="col-md-12 mb-3">
                                <label>Reason</label>
                                <textarea name="reason" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary">
                                    Update
                                </button>
                                <button type="reset" class="btn btn-light">
                                    Reset
                                </button>
                            </div>
            </form>

        </div>
    </div>
        </div>
        
    </div>

    <div class="card mt-4">

        <div class="card-header">
            <h5>History of Regeneration</h5>
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lot Number</th>
                        <th>Entry Date</th>
                        <th>Old Regeneration Cut of Year</th>
                        <th>Old Expiry Date</th>
                        <th>Old Regeneration Date</th>
                        <th>Regeneration Cut of Year</th>
                        <th>New Expiry Date</th>
                        <th>New Regeneration Date</th>
                        <th>Update Date</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>

                @forelse($regenerations as $key => $row)

                <tr>

                    <td>{{ $key + 1 }}</td>

                    {{-- Lot Number --}}
                    <td>
                        {{ $row->lot->lot_number ?? '-' }}
                    </td>

                    {{-- Entry Date --}}
                    <td>
                        {{ $row->lot && $row->lot->created_at
                            ? \Carbon\Carbon::parse($row->lot->created_at)->format('d-m-Y')
                            : '-' }}
                    </td>

                    {{-- Old Regen Year --}}
                    <td>
                        {{ $row->old_regen_year ?? '-' }}
                    </td>

                    {{-- Old Expiry Date --}}
                    <td>
                        {{ $row->old_expiry_date
                            ? \Carbon\Carbon::parse($row->old_expiry_date)->format('d-m-Y')
                            : '-' }}
                    </td>

                    {{-- Old Regeneration Date --}}
                    <td>
                        {{ $row->old_regeneration_date
                            ? \Carbon\Carbon::parse($row->old_regeneration_date)->format('d-m-Y')
                            : '-' }}
                    </td>

                    {{-- New Regen Year --}}
                    <td>
                        {{ $row->regen_year ?? '-' }}
                    </td>

                    {{-- New Expiry Date --}}
                    <td>
                        {{ $row->expiry_date
                            ? \Carbon\Carbon::parse($row->expiry_date)->format('d-m-Y')
                            : '-' }}
                    </td>

                    {{-- New Regeneration Date --}}
                    <td>
                        {{ $row->regeneration_date
                            ? \Carbon\Carbon::parse($row->regeneration_date)->format('d-m-Y')
                            : '-' }}
                    </td>

                    {{-- Date --}}
                    <td>
                        {{ $row->created_at
                            ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y')
                            : '-' }}
                    </td>

                    {{-- Reason --}}
                    <td>
                        {{ $row->reason ?? '-' }}
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">
                        No Regeneration History Found
                    </td>
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

    document.getElementById('from_crop').addEventListener('change', function () {
        let cropId = this.value;
        let accessionSelect = document.getElementById('from_accesstion');

        accessionSelect.innerHTML = '<option value="">Loading...</option>';

        if (!cropId) {
            accessionSelect.innerHTML = '<option value="">Select Accession</option>';
            return;
        }

        fetch(`/get-accessions/${cropId}`)
            .then(res => res.json())
            .then(data => {
                accessionSelect.innerHTML = '<option value="">Select Accession</option>';

                data.forEach(ac => {
                    accessionSelect.innerHTML += `<option value="${ac.id}">
                        ${ac.accession_number}
                    </option>`;
                });
            });
    });

    document.getElementById('from_accesstion').addEventListener('change', function () {
        let accessionId = this.value;
        let storageSelect = document.getElementById('from_storage');

        storageSelect.innerHTML = '<option value="">Loading...</option>';

        if (!accessionId) {
            storageSelect.innerHTML = '<option value="">Select Storage</option>';
            return;
        }

        fetch(`/get-accession-storages/${accessionId}`)
            .then(res => res.json())
            .then(data => {
                storageSelect.innerHTML = '<option value="">Select Storage</option>';

                data.forEach(s => {
                    storageSelect.innerHTML += `<option value="${s.id}">
                        ${s.name}
                    </option>`;
                });
            });
    });

    // ── FROM: Storage → load lots ─────────────────────────────────────────
    document.getElementById('from_storage').addEventListener('change', function () {
        const id = this.value;
        const infoBox = document.getElementById('from_storageInfo');
        const lotSel  = document.getElementById('from_lot');
        lotSel.innerHTML = '<option value="">Loading...</option>';
        document.getElementById('from_lotInfo').classList.add('d-none');

        if (!id) {
            infoBox.classList.add('d-none');
            lotSel.innerHTML = '<option value="">Select Lot</option>';
            const qtyDisplay = document.getElementById('storageQtyDisplay');
            const qtyLabel   = document.getElementById('storageQtyLabel');
            if (qtyDisplay) qtyDisplay.textContent = '—';
            if (qtyLabel)   qtyLabel.textContent   = 'Select FROM storage';
            return;
        }

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

                document.getElementById('from_capacity').textContent  = d.storage.capacity ? `${d.storage.capacity} ${d.unit||''}` : '—';
                document.getElementById('from_available').textContent = d.available ? `${d.available} ${d.unit||''}` : '—';
                infoBox.classList.remove('d-none');

                // Calculate total lot quantity for this storage from lots array
                const storageTotalQty = d.lots.reduce((sum, lot) => {
                    const sq = lot.seed_quantities?.[0];
                    return sum + parseFloat(sq?.quantity ?? lot.quantity ?? 0);
                }, 0);
                const storageUnit = d.lots[0]?.seed_quantities?.[0]?.unit?.name ?? d.unit ?? '';

                // Update inline storage info card
                document.getElementById('total_lot_qty').textContent =
                    storageTotalQty > 0 ? `${storageTotalQty.toFixed(2)} ${storageUnit}` : '0';

                // Update summary card if present
                const qtyDisplay = document.getElementById('storageQtyDisplay');
                const qtyLabel   = document.getElementById('storageQtyLabel');
                if (qtyDisplay) qtyDisplay.textContent = storageTotalQty > 0 ? `${storageTotalQty.toFixed(2)} ${storageUnit}` : '0';
                if (qtyLabel)   qtyLabel.textContent   = d.storage.name || 'Selected storage';

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
                        data-container="${lot.container?.name || '—'}"
                         data-regen_year="${lot.regen_year}"
    data-expiry_date="${lot.expiry_date}"
    data-recheck_date="${lot.regeneration_date}"
    >
                        ${lot.lot_number} (Avail: ${qtyShow} ${unit})
                    </option>`;
                });
            });
    });
    function formatDisplayDate(dateString) {

        if (!dateString) return '—';

        let d = new Date(dateString);

        let day   = String(d.getDate()).padStart(2, '0');
        let month = String(d.getMonth() + 1).padStart(2, '0');
        let year  = d.getFullYear();

        return `${day}-${month}-${year}`;
    }

    // ── FROM: Lot → show details ──────────────────────────────────────────
    document.getElementById('from_lot').addEventListener('change', function () {
        const sel = this.options[this.selectedIndex];
        const cropOption =
    cropSelect.options[cropSelect.selectedIndex];
        const box = document.getElementById('from_lotInfo');
        if (!this.value) { box.classList.add('d-none'); return; }
        document.getElementById('fl_lot_number').textContent = sel.dataset.lotno   || '—';
        document.getElementById('fl_crop').textContent       = sel.dataset.crop    || '—';
        document.getElementById('fl_accession').textContent  = sel.dataset.accession || '—';
        document.getElementById('fl_qty').textContent        = sel.dataset.qty     || '—';
        document.getElementById('fl_qty_show').textContent   = sel.dataset.qty_show || '—';
        document.getElementById('fl_unit').textContent       = sel.dataset.unit    || '—';
        document.getElementById('fl_rack').textContent       = sel.dataset.rack    || '—';
        document.getElementById('fl_bin').textContent       = sel.dataset.bin    || '—';
        document.getElementById('fl_container').textContent       = sel.dataset.container    || '—';
        

        window._cropSeason = {
            start_month:
                parseInt(cropOption.dataset.start) || 0,
            end_month:
                parseInt(cropOption.dataset.end) || 0,
        };

        document.getElementById('old1_expiry_date').textContent =
            formatDisplayDate(sel.dataset.expiry_date || '—');

        document.getElementById('old_recheck_date').textContent =
            formatDisplayDate(sel.dataset.recheck_date || '—');

        // =========================
        // OLD VALUES
        // =========================

        let regenYear  = parseFloat(sel.dataset.regen_year || 0);

        let oldExpiry  = sel.dataset.expiry_date;
        let oldRecheck = sel.dataset.recheck_date;

        // =========================
        // AUTO FILL REGEN YEAR
        // =========================

        document.getElementById('regen_year').value = regenYear;

        // =========================
        // NEW EXPIRY DATE
        // old expiry + regen year
        // =========================

        if (oldExpiry && regenYear > 0) {
            let expiryDate = new Date(oldExpiry);
            expiryDate.setFullYear(
                expiryDate.getFullYear() + regenYear
            );
            let expiryFormatted =
                expiryDate.toISOString().split('T')[0];
            document.getElementById('expiry_date').value =
                expiryFormatted;
        }

        // =========================
        // NEW RECHECK DATE
        // old recheck + regen year
        // =========================

        if (oldRecheck && regenYear > 0) {

            let recheckDate = new Date(oldRecheck);
            recheckDate.setFullYear(
                recheckDate.getFullYear() + regenYear
            );

            let recheckFormatted =
                recheckDate.toISOString().split('T')[0];
            document.getElementById('recheck_date').value =
                recheckFormatted;
        }
            
            box.classList.remove('d-none');
        });

    // ── Helpers ───────────────────────────────────────────────────────────
    function resetSelect(id, placeholder, disable = false) {
        const sel = document.getElementById(id);
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        sel.disabled  = disable;
    }
    function showHint(id) { document.getElementById(id)?.classList.remove('d-none'); }
    function hideHint(id) { document.getElementById(id)?.classList.add('d-none'); }

    document.getElementById('lot_search').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            let keyword = this.value;

            if (!keyword) return;

            fetch(`/get-lot-by-number?lot_number=${keyword}`)
                .then(res => res.json())
                .then(data => {

                    if (!data.status) {
                        alert('Lot not found');
                        return;
                    }

                    let lot = data.lot;

                    // ✅ AUTO SELECT STORAGE
                    document.getElementById('from_storage').value = lot.storage_id;
                    document.getElementById('from_storage').dispatchEvent(new Event('change'));

                    // ✅ WAIT STORAGE LOAD THEN SET LOT
                    setTimeout(() => {
                        let lotSelect = document.getElementById('from_lot');

                        

                        // add option if not exists
                        let option = new Option(lot.lot_number, lot.id, true, true);
                        lotSelect.append(option);

                        lotSelect.value = lot.id;
                        lotSelect.dispatchEvent(new Event('change'));
                    }, 500);

                });
        }
    });

// =========================
// ACTION BUTTON CLICK
// Auto fill Search Lot Number + load all data
// =========================

document.querySelectorAll('.regenerationBtn').forEach(btn => {

    btn.addEventListener('click', function () {

        let lotNumber = this.dataset.lot;

        // auto fill search box
        document.getElementById('lot_search').value = lotNumber;

        // auto trigger search
        fetch(`/get-lot-by-number?lot_number=${lotNumber}`)
            .then(res => res.json())
            .then(data => {

                if (!data.status) {
                    alert('Lot not found');
                    return;
                }

                let lot = data.lot;

                // =========================
                // AUTO SELECT CROP
                // =========================
                let cropId =
                    lot.crop_id ||
                    lot.crop?.id ||
                    lot.accession?.crop_id ||
                    lot.accession?.crop?.id;
                //console.log('Crop ID:', cropId);
                let cropSelect =
                    document.getElementById('from_crop');

                if (cropId) {

                    cropSelect.value = cropId;

                    // force selected
                    Array.from(cropSelect.options).forEach(option => {

                        if (option.value == cropId) {
                            option.selected = true;
                        }

                    });

                    cropSelect.dispatchEvent(
                        new Event('change')
                    );

                }

                // =========================
                // WAIT ACCESSION LOAD
                // =========================
                setTimeout(() => {

                    // =========================
                    // AUTO SELECT ACCESSION
                    // =========================
                    if (lot.accession_id) {

                        document.getElementById('from_accesstion').value =
                            lot.accession_id;

                        document.getElementById('from_accesstion')
                            .dispatchEvent(new Event('change'));
                    }

                    // =========================
                    // WAIT STORAGE LOAD
                    // =========================
                    setTimeout(() => {

                        // =========================
                        // AUTO SELECT STORAGE
                        // =========================
                        if (lot.storage_id) {

                            document.getElementById('from_storage').value =
                                lot.storage_id;

                            document.getElementById('from_storage')
                                .dispatchEvent(new Event('change'));
                        }

                        // =========================
                        // WAIT LOT LOAD
                        // =========================
                        setTimeout(() => {

                            let lotSelect =
                                document.getElementById('from_lot');

                            lotSelect.value = lot.id;

                            lotSelect.dispatchEvent(
                                new Event('change')
                            );

                            // scroll form
                            document.querySelector('form')
                                .scrollIntoView({
                                    behavior: 'smooth'
                                });

                        }, 800);

                    }, 800);

                }, 800);

            });

    });

});
    
    
    // =========================
            // DATE AUTO CALC
            // =========================

    const expiryInput    = document.getElementById('expiry_date');
    const regenInput     = document.getElementById('recheck_date');
    const regenYearInput = document.getElementById('regen_year');

    @php
        $seasonStart = 0;
        $seasonEnd   = 0;
        if (isset($accession) && $accession && $accession->crop && $accession->crop->season) {
            $seasonStart = (int) $accession->crop->season->start_month;
            $seasonEnd   = (int) $accession->crop->season->end_month;
        }
        if (isset($accession) && $accession && $accession->crop) {
            $seasonStart = (int) $accession->crop->season_start_month_id;
            $seasonEnd   = (int) $accession->crop->season_end_month_id;
        }
    @endphp

    // Season seeded from PHP on edit (crop already selected), updated via AJAX on crop change
    window._cropSeason = {
        season_start_month_id: {{ $accession->crop->season_start_month_id ?? 0 }},
        season_end_month_id: {{ $accession->crop->season_end_month_id ?? 0 }},
        start_month: {{ $seasonStart }},
        end_month: {{ $seasonEnd }}
    };

    console.log("Season from PHP:", window._cropSeason);
    

    function formatDate(d) {
        const y   = d.getFullYear();
        const m   = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    // Check if month is inside season (handles wrap-around e.g. Rabi: Nov–Mar)
    function isMonthInSeason(month, start, end) {
        if (start <= end) {
            return month >= start && month <= end;
        } else {
            return month >= start || month <= end;
        }
    }

    // ✅ EVENTS
    const cropSelect = document.getElementById('from_crop');
        cropSelect.addEventListener('change', function () {

        if (
            !this.options ||
            this.selectedIndex < 0
        ) {
            return;
        }

        const selectedOption =
            this.options[this.selectedIndex];

        if (!selectedOption) {
            return;
        }

        // regen year
        regenYearInput.value =
            selectedOption.dataset.regen || '';

        // crop season
        window._cropSeason = {

            start_month:
                parseInt(selectedOption.dataset.start) || 0,

            end_month:
                parseInt(selectedOption.dataset.end) || 0,
        };

        calculateAllDates();

    });

    /*if (cropSelect) {

        cropSelect.addEventListener('change', function () {

            const selectedOption =
                this.options[this.selectedIndex];

            // regen year
            regenYearInput.value =
                selectedOption.dataset.regen || '';

            // crop season
            window._cropSeason = {

                start_month:
                    parseInt(selectedOption.dataset.start) || 0,

                end_month:
                    parseInt(selectedOption.dataset.end) || 0,
            };

            console.log(window._cropSeason);

            calculateAllDates();

        });

    }*/

    // Auto-fill Regeneration Cut of Year when crop is selected
    /*cropSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const regenYear = selectedOption.getAttribute('data-regen');
        regenYearInput.value = regenYear ?? '';
        // Note: calculateAllDates() is called by the jQuery AJAX handler
        // after the season is loaded, so we don't call it here.
    });*/

    regenYearInput.addEventListener('input', function () {

        if (cropSelect.value === '') {

            alert('Please select a Crop first.');

            this.value = '';

            expiryInput.value = '';
            regenInput.value  = '';

            cropSelect.focus();

            return;
        }

        calculateAllDates();
    });

    /*cropSelect.addEventListener('change', function () {
        const selectedOption =
        this.options[this.selectedIndex];

        // ✅ auto fill regen year
        const regenYear =
            selectedOption.getAttribute('data-regen');

        regenYearInput.value = regenYear ?? '';

        // ✅ calculate after value set
        setTimeout(() => {

            calculateAllDates();

        }, 100);
    });*/

    // Auto-fill on edit page load
    window.addEventListener('load', function () {
        if (cropSelect && cropSelect.value) {
            // Only auto-fill if empty
            if (!regenYearInput.value) {
                const selectedOption = cropSelect.options[cropSelect.selectedIndex];

                regenYearInput.value =
                    selectedOption.getAttribute('data-regen') ?? '';
            }
            // Season is already seeded from PHP (_cropSeason), calculate immediately
            calculateAllDates();
        }
    });

    // CALCULATION LOGIC:
    //   Expiry     = today + regen_years  (same day/month, N years ahead)
    //   Regen Date = expiry month IN season  → same date as expiry
    //              = expiry month OUT of season → (expiry year - 1), season start month, same day
    // Example: today=29-Apr-2026, years=2, Kharif(Jun-Oct)
    //   Expiry = 29-Apr-2028  (Apr is outside Jun-Oct)
    //   Regen  = 29-Jun-2027  (2028-1=2027, start month=Jun, day=29)
    /*window.calculateAllDates = function () {

        const years = parseFloat(regenYearInput.value);

        if (isNaN(years) || years <= 0) {

            // ✅ reset dates when input empty
            expiryInput.value = '';
            regenInput.value  = '';

            return;
        }

        const today = new Date();

        // ✅ convert decimal year to months
        const totalMonths = years * 12;

        // ✅ expiry date
        const expiry = new Date(today);

        expiry.setMonth(
            expiry.getMonth() + parseInt(totalMonths)
        );

        expiryInput.value = formatDate(expiry);

        // ✅ PRIORITY:
        // crop season month first
        let startMonth =
            window._cropSeason.season_start_month_id
            || window._cropSeason.start_month;

        let endMonth =
            window._cropSeason.season_end_month_id
            || window._cropSeason.end_month;

        // ✅ no season
        if (!startMonth || !endMonth) {

            regenInput.value = formatDate(expiry);

            return;
        }

        const expMonth = expiry.getMonth() + 1;

        let regen;

        // ✅ season match
        if (
            isMonthInSeason(
                expMonth,
                startMonth,
                endMonth
            )
        ) {

            regen = new Date(expiry);

        } else {

            // ✅ first day of season month
            const regenYear =
                expiry.getFullYear() - 1;

            const regenMonth =
                startMonth - 1;

            regen = new Date(
                regenYear,
                regenMonth,
                expiry.getDate()
            );
        }
        regenInput.value = formatDate(regen);
    }*/
    window.calculateAllDates = function () {
        const years = parseFloat(regenYearInput.value);
        if (isNaN(years) || years <= 0) {
            return;
        }

        // =========================
        // GET OLD DATES
        // =========================

        const oldExpiry =
            document.getElementById('old1_expiry_date')
            .textContent.trim();

        const oldRecheck =
            document.getElementById('old_recheck_date')
            .textContent.trim();

        // =========================
        // CONVERT DD-MM-YYYY TO DATE
        // =========================

        function parseDMY(dateStr) {
            if (!dateStr || dateStr === '—') {
                return new Date();
            }
            let parts = dateStr.split('-');
            return new Date(
                parts[2],
                parts[1] - 1,
                parts[0]
            );
        }

        let expiry = parseDMY(oldExpiry);
        let regen  = parseDMY(oldRecheck);
        // =========================
        // ADD YEARS
        // =========================

        expiry.setFullYear(
            expiry.getFullYear() + years
        );

        regen.setFullYear(
            regen.getFullYear() + years
        );

        // =========================
        // SET VALUES
        // =========================

        expiryInput.value = formatDate(expiry);
        regenInput.value  = formatDate(regen);

    }

});
</script>
@endpush