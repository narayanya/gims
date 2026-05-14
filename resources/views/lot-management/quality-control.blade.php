@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Lot Quality Control</h3>
                <p class="text-muted mb-0" style="font-size:13px">Edit quality control information for lots</p>
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
                <div class="col-md-12">
                    <div class="card h-100">
                  
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Search Lot Number </label>
                                    <input type="text" id="lot_search" class="form-control" placeholder="Search lot number" />
                                    <small class="text-muted">e.g.: 1564-2017-2018/2-MB-1483-02</small>
                                </div>
                                <div class="col-md-12 mb-3 text-center">
                                    or
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Crop </label>
                                    <select id="from_crop" class="form-select">
                                        <option value="">Select Crop</option>
                                        @foreach($crops as $c)
                                            <option value="{{ $c->id }}">{{ $c->crop_name }}-{{ $c->crop_code }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Accession </label>
                                    <select id="from_accesstion" class="form-select">
                                        <option value="">Select Accession</option>
                                        @foreach($accessions as $ac)
                                            <option value="{{ $ac->id }}">{{ $ac->accession_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Storage <span class="text-danger">*</span></label>
                                    <select name="from_storage_id" id="from_storage" class="form-select">
                                        <option value="">Select Storage</option>
                                        @foreach($storages as $s)
                                            <option value="{{ $s->id }}">{{ $s->storage_id }} — {{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Select Lot <span class="text-danger">*</span></label>
                                    <select name="from_lot_id" id="from_lot" class="form-select" required>
                                        <option value="">Select Lot</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div id="from_storageInfo" class="card border bg-light mb-3 d-none ">
                                        <div class="card-body py-2 small">
                                            <h5 class="border-bottom">Storage Information</h5>
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
                                </div>
                                <div class="col-md-6">
                                    <div id="from_lotInfo" class="card border bg-light d-none ">
                                        <div class="card-body py-2 small">
                                            <h5 class="border-bottom">Lot Information</h5>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ri-seedling-line me-2"></i>Seed Quality Information
                            </h5>
                            <button type="button" id="addSeedRowBtn" class="btn btn-sm btn-primary">
                                + Add More
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-1" id="seedContainer">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Germination %</th>
                                    <th>Moisture %</th>
                                    <th>Purity %</th>
                                    <th>Chlorophyll %</th>
                                    <th>Water Level</th>
                                    <th>Viability Date</th>
                                    <th>Health Status</th>
                                    <th>Researcher</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                <td id="fl_germination">—</td>
                                <td id="fl_moisture">—</td>
                                <td id="fl_purity">—</td>
                                <td id="fl_chlorophyll">—</td>
                                <td id="fl_water_level">—</td>
                                <td id="fl_viability_date">—</td>
                                <td id="fl_health_status">—</td>
                                <td id="fl_researcher">—</td>
                                <td id="fl_quality_date">—</td>
                                </tr>
                                <!-- Default Row -->


                                        {{--@foreach ($rows as $index => $row)
                                            <tr>
                                                <td>
                                                    <input type="number" step="0.01" name="germination_percentage[]"
                                                        class="form-control @error('germination_percentage.' . $index) is-invalid @enderror"
                                                        value="{{ $row->germination_percentage ?? '' }}"
                                                        placeholder="e.g. 85.50">

                                                    @error('germination_percentage.' . $index)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td>
                                                    <input type="number" step="0.01" name="moisture_content[]"
                                                        class="form-control @error('moisture_content.' . $index) is-invalid @enderror"
                                                        value="{{ $row->moisture_content ?? '' }}"
                                                        placeholder="e.g. 12.00">
                                                </td>

                                                <td>
                                                    <input type="number" step="0.01" name="purity_percentage[]"
                                                        class="form-control @error('purity_percentage.' . $index) is-invalid @enderror"
                                                        value="{{ $row->purity_percentage ?? '' }}"
                                                        placeholder="e.g. 98.00">
                                                </td>

                                                <td>
                                                    <input type="date" name="viability_test_date[]"
                                                        class="form-control"
                                                        value="{{ $row->viability_test_date ?? '' }}" >
                                                </td>

                                                <td>
                                                    <select name="seed_health_status[]" class="form-select">
                                                        <option value="">Select</option>
                                                        @foreach (['Healthy', 'Infected', 'Damaged', 'Under Treatment'] as $status)
                                                            <option value="{{ $status }}"
    {{ ($row->seed_health_status ?? '') == $status ? 'selected' : '' }}>
                                                                {{ $status }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="researcher_id[]"
                                                        class="form-select researcher-select @error('researcher_id.' . $index) is-invalid @enderror">

                                                        <option value="">Select</option>

                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->id }}"
    {{ ($row->researcher_id ?? '') == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }}
                                                            </option>
                                                        @endforeach

                                                        <option value="Other"
                                                            {{ ($row->researcher_id ?? '') == 'Other' ? 'selected' : '' }}>
                                                            Other
                                                        </option>
                                                    </select>

                                                    @error('researcher_id.' . $index)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror

                                                    <!-- Other Input -->

                                                    <input type="text" name="researcher_other[]"
                                                        class="form-control mt-1 other-input"
                                                        placeholder="Enter researcher name"
                                                        value="{{ $row->researcher_other ?? '' }}"
                                                        style="{{ ($row->researcher_id ?? '') == 'Other' ? '' : 'display:none;' }}">
                                                </td>
                                                <td>
                                                    <input type="date" name="research_date[]"
                                                        class="form-control"
                                                        value="{{ $row->research_date ?? '' }}" max="{{ date('Y-m-d') }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm">X</button>
                                                </td>
                                            </tr>
                                        @endforeach--}}
                            </tbody>
                        </table>
                    </div>
                </div>              
                </div>                

            </div>
        </form>
    </div>
    <div class="col-md-12 ">

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
                    const quality = lot.seed_qualities?.[0] ?? {};
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
                        
                        data-germination="${quality.germination_percentage || '—'}"
            data-moisture="${quality.moisture_content || '—'}"
            data-purity="${quality.purity_percentage || '—'}"
            data-chlorophyll="${quality.chlorophyll_percentage || '—'}"
            data-water_level="${quality.water_level_percentage || '—'}"
            data-viability_date="${quality.viability_test_date || '—'}"
            data-health_status="${quality.seed_health_status || '—'}"
            data-researcher="${quality.researcher?.name || '—'}"
            data-quality_date="${quality.research_date || '—'}"
                        >
                        ${lot.lot_number} (Avail: ${qtyShow} ${unit})
                    </option>`;
                });
                console.log(d.lots);
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
        document.getElementById('fl_rack').textContent       = sel.dataset.rack    || '—';
        document.getElementById('fl_bin').textContent       = sel.dataset.bin    || '—';
        document.getElementById('fl_container').textContent       = sel.dataset.container    || '—';

        document.getElementById('fl_germination').textContent =
        sel.dataset.germination || '—';

    document.getElementById('fl_moisture').textContent =
        sel.dataset.moisture || '—';

    document.getElementById('fl_purity').textContent =
        sel.dataset.purity || '—';

    document.getElementById('fl_chlorophyll').textContent =
        sel.dataset.chlorophyll || '—';

    document.getElementById('fl_water_level').textContent =
        sel.dataset.water_level || '—';

    document.getElementById('fl_viability_date').textContent =
        formatDate(sel.dataset.viability_date || '—');

    document.getElementById('fl_health_status').textContent =
        sel.dataset.health_status || '—';

    document.getElementById('fl_researcher').textContent =
        sel.dataset.researcher || '—';

    document.getElementById('fl_quality_date').textContent =
        formatDate(sel.dataset.quality_date || '—');
        box.classList.remove('d-none');

    });

    
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

    function formatDate(dateString) {

        if (!dateString || dateString === '—') {
            return '—';
        }

        const d = new Date(dateString);

        return d.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }



});
</script>
@endpush
