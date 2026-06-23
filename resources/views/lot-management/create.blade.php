@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
                <div>
                    <h3 class="text-xl font-bold">Arrival Management</h3>
                    <p class="text-muted mb-0" style="font-size:13px">Create and manage germplasm lots</p>
                </div>
                <a href="{{ route('lot-management') }}" class="btn btn-primary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i> Back to list
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Lot List --}}
            <div class="card">
                <form method="POST"
                    action="{{ isset($lot) ? route('lot-management.update', $lot->id) : route('lot-management.store') }}">
                    @csrf
                    @if (isset($lot))
                        @method('PUT')
                    @endif
                    <div class="card-header">
                        <h5 class="modal-title" id="addLotModalLabel">
                            <i class="ri-stack-line me-2"></i>Add New Arrival
                        </h5>
                    </div>
                    <div class="card-body" style="overflow-y:auto; flex:1;">
                        <div class="row g-3">

                            {{-- ── Section 1: Lot Info ── --}}
                            <div class="col-12">
                                <h6 class="text-muted border-bottom pb-1">Arrival Information</h6>
                            </div>
                            {{-- Lot Number (auto-generated) --}}
                            {{-- <div class="col-md-8 d-none">
                                <div class="alert alert-info py-2 d-flex align-items-center gap-3 mb-0">
                                    <i class="ri-barcode-line fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Auto-generated Number</small>
                                        <strong id="lotNumberDisplay" class="fs-5">{{ $nextLotNo }}</strong>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="col-md-3 mt-2">
                                <label class="form-label">Arrival Type <span class="text-danger">*</span></label>
                                <select name="arrival_type" id="arrivalType" class="form-select" required
                                    {{ isset($lot) ? 'disabled' : '' }}>
                                    <option value="">Select Arrival Type</option>
                                    @foreach (\App\Models\ArrivalType::where('status', 1)->orderBy('name')->get() as $type)
                                        <option value="{{ $type->name }}"
                                            {{ old('arrival_type', $lot->arrival_type ?? '') == $type->name ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Keep value on submit when disabled --}}
                                @if(isset($lot))
                                    <input type="hidden" name="arrival_type" value="{{ $lot->arrival_type }}">
                                @endif
                            </div>

                            {{-- Edit mode: show current lot number as read-only --}}
                            @if(isset($lot))
                            <div class="col-md-3 mt-2">
                                <label class="form-label">Lot Number</label>
                                <input type="text" class="form-control" value="{{ $lot->lot_number }}"
                                    readonly style="background-color:#e9ecef; font-weight:600; color:#198754;">
                            </div>
                            @endif
                            <div class="col-md-3 mt-2" id="dispatchnField" style="display:none;">
                                <label class="form-label">
                                    Dispatch Number or Request Number <span class="text-danger">*</span>
                                </label>
                                <select name="dispatch_id" id="dispatchSelect" class="form-select">
                                    <option value="">Select Dispatch Number</option>
                                    @foreach ($dispatches as $dispatch)
                                        <option value="{{ $dispatch->id }}"
                                            {{ old('dispatch_id', $lot->dispatch_id ?? '') == $dispatch->id ? 'selected' : '' }}
                                            data-request="{{ $dispatch->request->request_number ?? '' }}"
                                            data-lot="{{ $dispatch->lot->lot_number ?? '' }}">
                                            {{ $dispatch->dispatch_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5 mt-2" id="requestnField" style="display:none;">
                                <div class="row">
                                    <div class="col-md-5">
                                        <select name="request_id" id="requestSelect" class="form-select"
                                            style="margin-top:27px;">
                                            <option value="">Select Request Number</option>
                                            @foreach ($dispatches as $dispatch)
                                                <option value="{{ $dispatch->request_id }}"
                                                    data-dispatch-id="{{ $dispatch->id }}"
                                                    data-lot="{{ $dispatch->lot->lot_number ?? '' }}"
                                                    @selected(old('request_id', $lot->request_id ?? '') == $dispatch->request_id)>
                                                    {{ $dispatch->request->request_number ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" id="lotNumber" class="form-control" readonly
                                            placeholder="Lot Number"
                                            value="{{ old('rejuvenation_program', $lot->rejuvenation_program ?? '') }}"
                                            style="margin-top:27px;">
                                        {{-- Hidden: lot number stored as rejuvenation_program --}}
                                        <input type="hidden" name="rejuvenation_program" id="rffRejuvHidden"
                                            value="{{ old('rejuvenation_program', $lot->rejuvenation_program ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Only shown when Arrival Type = Rejuvenation --}}
                            <div class="col-md-2 mt-2" id="rejuvenationFields" style="display:none;">
                                <label class="form-label">Rejuvenation Program <span class="text-danger">*</span></label>
                                <input type="text" name="rejuvenation_program" id="rejuvenation_program"
                                    class="form-control"
                                    value="{{ old('rejuvenation_program', $lot->rejuvenation_program ?? '') }}"
                                    placeholder="e.g. 2017-2018"
                                    {{ isset($lot) ? 'readonly style=background-color:#e9ecef;' : '' }}>
                            </div>
                            <div class="col-md-2 mt-2" id="prefixField" style="display:none;">
                                <label class="form-label">Prefix <span class="text-danger">*</span></label>
                                <input type="text" name="prefix" id="prefix" class="form-control"
                                    value="{{ old('prefix', $lot->prefix ?? '') }}" placeholder="e.g. MB"
                                    {{ isset($lot) ? 'readonly style=background-color:#e9ecef;' : '' }}>
                            </div>


                            {{-- Source Lot Number — shown only for Return From Field --}}
                            <div class="col-md-4 mt-2" id="rffLotField" style="display:none;">
                                <label class="form-label">Source Lot Number</label>
                                <div class="input-group">
                                    <input type="text" id="rffLotInput" name="rff_lot_number" class="form-control"
                                        placeholder="Type or paste lot number…"
                                        value="{{ old('rff_lot_number', isset($lot) && $lot->arrival_type === 'Return From Field' ? $lot->rejuvenation_program : '') }}">
                                    <button type="button" class="btn btn-outline-secondary" id="rffLotSearchBtn">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                                <div id="rffLotFeedback" class="mt-1 small"></div>
                            </div>

                            {{-- Lot Number Preview --}}
                            <div class="col-md-3 mt-2" id="lotPreviewBox" style="display:none;">
                                <label class="form-label">Lot Number Preview</label>
                                <div class="alert alert-info py-2 mb-0 small">
                                    <i class="ri-barcode-line me-1"></i>
                                    <strong id="lotNumberPreview">—</strong>
                                </div>
                            </div>

                            @php
                                $selectedStorage = old('storage_id', $lot->storage_id ?? '');
                                $selectedAccession = old('accession_id', $lot->accession_id ?? '');
                                $selectedSection = old('section_id', $lot->section_id ?? '');
                                $selectedRack = old('rack_id', $lot->rack_id ?? '');
                                $selectedBin = old('bin_id', $lot->bin_id ?? '');
                                $selectedContainer = old('container_id', $lot->container_id ?? '');
                            @endphp
                            {{-- ── Section 2: Accession ── --}}
                            <div class="col-12 mt-3">
                                <h6 class="text-muted border-bottom pb-1">Accession</h6>
                            </div>

                            <div class="col-md-4 mt-2">
                                <label class="form-label">Accession ID <span class="text-danger">*</span></label>
                                <select name="accession_id" id="accessionSelect" class="form-select" required>
                                    <option value="">Select Accession</option>
                                    @foreach ($accessions as $acc)
                                        <option value="{{ $acc->id }}"
                                            data-regen="{{ optional($acc->crop)->regeneration_cut_year }}"
                                            data-season-start="{{ optional(optional($acc->crop)->season)->start_month ?? 0 }}"
                                            data-season-end="{{ optional(optional($acc->crop)->season)->end_month ?? 0 }}"
                                            {{ $selectedAccession == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->accession_number }}</option>
                                    @endforeach
                                </select>
                                <input hidden type="text" id="sample_id_input" name="sample_id"
                                    value="{{ old('sample_id', $accession->sample_id ?? '') }}">
                            </div>

                            <div class="col-md-8 mt-2">
                                {{-- Accession Details Card --}}
                                <div id="accessionDetails" class="card border bg-light mb-0 d-none">
                                    <div class="card-body py-2 px-3">
                                        <div class="row g-1 small">
                                            <div class="col-4"><span class="text-muted">Crop:</span> <span
                                                    id="ad_crop">—</span></div>
                                            <div class="col-4"><span class="text-muted">Time:</span> <span
                                                    id="ad_time">—</span></div>
                                            <div class="col-4"><span class="text-muted">Scientific:</span> <span
                                                    id="ad_scientific">—</span></div>
                                            <div class="col-4"><span class="text-muted">Status:</span> <span
                                                    id="ad_status">—</span></div>
                                            <div class="col-4"><span class="text-muted">Collected:</span> <span
                                                    id="ad_collected">—</span></div>
                                            <div class="col-4"><span class="text-muted">Sample ID:</span> <span
                                                    id="ad_sample_id">—</span></div>
                                            <div class="col-4"><span class="text-muted">Regeneration Cut of year:</span>
                                                <span id="ad_regeneration">—</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                if (old('quantity') !== null) {
                                    // Validation error
                                    $qtyRows = collect(old('quantity'))->map(function ($val, $i) {
                                        return (object) [
                                            'quantity' => $val,
                                            'unit_id' => old('unit_id.' . $i),
                                            'reference_number' => old('reference_number.' . $i),
                                            'number_of_seeds' => old('number_of_seeds.' . $i),
                                            'number_of_bags' => old('number_of_bags.' . $i),
                                            'per_seed_weight' => old('per_seed_weight.' . $i),
                                            'quantity_show' => old('quantity_show.' . $i),
                                            'min_quantity' => old('min_quantity.' . $i),
                                        ];
                                    });
                                } elseif (isset($lot) && $lot->seedQuantities->count()) {
                                    // EDIT MODE — only rows belonging to this lot
                                    $qtyRows = $lot->seedQuantities->where('lot_id', $lot->id)->values();
                                } else {
                                    // CREATE MODE
                                    $qtyRows = collect([(object) []]);
                                }
                            @endphp

                            <div class="col-md-12">
                                <!-- 4. Seed / Material Quantity -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light text-white">
                                        <h5 class="mb-0"><i class="ri-scales-line me-2"></i>Seed / Material Quantity
                                        </h5>
                                    </div>
                                    <div class="card-body p-1">
                                        <div class="invalid-feedback">
                                            Duplicate reference number not allowed
                                        </div>
                                        <table class="table table-bordered" id="quantityTable">
                                            <thead>
                                                <tr>
                                                    <th>Reference No.</th>
                                                    <th>Number of Seeds</th>
                                                    <th>Number of Pouch</th>
                                                    <th>Per Seed Weight (avg)</th>
                                                    <th>Quantity </th>
                                                    <th>Unit</th>
                                                    <th>Show User %</th>
                                                    <th>Min Stock Balance</th>
                                                    <th>Available for User</th>
                                                    <th class="lot-preview-col" style="display:none;">Lot No. Preview</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBodyQuantity">
                                                @foreach ($qtyRows as $index => $row)
                                                    <tr>

                                                        <td>
                                                            <input type="text" name="reference_number[]"
                                                                class="form-control form-control-sm refNumber"
                                                                placeholder="Enter Reference Number"
                                                                value="{{ $row->reference_number ?? '' }}"
                                                                {{ isset($lot) ? 'readonly style=background-color:#e9ecef;' : '' }}>
                                                        </td>

                                                        <td>
                                                            <input type="number" name="number_of_seeds[]"
                                                                class="form-control form-control-sm"
                                                                placeholder="e.g. 100" min="0" max="4"
                                                                value="{{ $row->number_of_seeds ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="number" name="number_of_bags[]"
                                                                class="form-control form-control-sm"
                                                                placeholder="e.g. 100" min="0" max="3"
                                                                value="{{ $row->number_of_bags ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="number" step="0.001" name="per_seed_weight[]"
                                                                class="form-control form-control-sm"
                                                                placeholder="e.g. 0.5"
                                                                value="{{ $row->per_seed_weight ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="number" step="0.01" name="quantity[]"
                                                                class="form-control form-control-sm quantity"
                                                                placeholder="e.g. 1000"
                                                                value="{{ $row->quantity ?? '' }}" required>
                                                        </td>

                                                        <td>
                                                            <select name="unit_id[]" class="form-select form-select-sm">
                                                                <option value="">Unit</option>
                                                                @foreach ($units as $unit)
                                                                    <option value="{{ $unit->id }}"
                                                                        {{ ($row->unit_id ?? $row->capacity_unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                                                        {{ $unit->name }} ({{ $unit->code }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <select class="form-select form-select-sm percent">
                                                                <option value="">%</option>
                                                                @foreach ([10, 20, 30, 40, 50, 60, 70, 80, 90, 100] as $p)
                                                                    <option value="{{ $p }}">
                                                                        {{ $p }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <input type="text" name="min_quantity[]"
                                                                class="form-control form-control-sm min"
                                                                placeholder="Auto calculated"
                                                                value="{{ $row->min_quantity ?? '' }}" readonly>
                                                        </td>

                                                        <td>
                                                            <input type="text" name="quantity_show[]"
                                                                class="form-control form-control-sm userQty"
                                                                placeholder="Auto calculated"
                                                                value="{{ $row->quantity_show ?? '' }}" readonly>
                                                        </td>

                                                        {{-- Per-row lot number preview — shown only when arrival type is selected --}}
                                                        <td class="lot-preview-col" style="display:none;">
                                                            <div class="alert alert-info py-1 px-2 mb-0 small text-nowrap">
                                                                <i class="ri-barcode-line me-1"></i>
                                                                <span class="row-lot-preview">—</span>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-success btn-sm addRowQ">+</button>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm removeRowQ">-</button>
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @error('reference_number.*')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                        <div class="mt-2 text-muted">
                                            Last Reference No.: <strong class="me-4">{{ $lastRef ?? 'N/A' }},</strong>

                                            Total Quantity: <strong
                                                id="totalQuantity">{{ isset($lot) ? $lot->seedQuantities->sum('quantity') : '0' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ── Section 4: Lot Details ── --}}
                            <div class="col-12 mt-3">
                                <h6 class="text-muted border-bottom pb-1">SLOC Details</h6>
                            </div>

                            {{-- Warehouse --}}
                            <div class="col-md-3 mt-2">
                                <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select id="warehouseSelect" class="form-select">
                                    <option value="">Select Warehouse</option>
                                    @foreach ($warehouses as $wh)
                                        <option value="{{ $wh->id }}"
                                            {{ isset($lot) && $lot->storage && $lot->storage->warehouse_id == $wh->id ? 'selected' : '' }}>
                                            {{ $wh->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Storage (filtered by warehouse) --}}
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Storage <span class="text-danger">*</span></label>
                                <select name="storage_id" id="storageSelect" class="form-select" required>
                                    <option value="">Select Storage</option>
                                    @foreach ($storages as $s)
                                        <option value="{{ $s->id }}" data-warehouse="{{ $s->warehouse_id }}"
                                            {{ $selectedStorage == $s->id ? 'selected' : '' }}>
                                            {{ $s->storage_id }} — {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Storage Details Card --}}
                            <div class="col-md-5 mt-2">
                                <div id="storageDetails" class="card border bg-light mb-0 d-none">
                                    <div class="card-body py-2 px-3">
                                        <div class="row g-1 small">
                                            <div class="col-4"><span class="text-muted">Warehouse:</span> <span
                                                    id="sd_warehouse">—</span></div>
                                            <div class="col-4"><span class="text-muted">Type:</span> <span
                                                    id="sd_type">—</span></div>
                                            <div class="col-4"><span class="text-muted">Condition:</span> <span
                                                    id="sd_condition">—</span></div>
                                            <div class="col-4"><span class="text-muted">Time:</span> <span
                                                    id="sd_time">—</span></div>
                                            <div class="col-4"><span class="text-muted">Capacity:</span> <span
                                                    id="sd_capacity">—</span></div>
                                            <div class="col-4"><span class="text-muted">Available:</span> <span
                                                    id="sd_available">—</span></div>
                                            <div class="col-4"><span class="text-muted">Temp:</span> <span
                                                    id="sd_temp">—</span></div>
                                            <div class="col-4"><span class="text-muted">Humidity:</span> <span
                                                    id="sd_humidity">—</span></div>
                                            <div class="col-12 mt-1 pt-1 border-top">
                                                <span class="text-muted">Balance (after lot qty):</span>
                                                <strong id="sd_balance" class="ms-1">—</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            {{-- Rack (filtered by section) --}}
                            <div class="col-md-3 mt-2">
                                <label class="form-label">Shelf/Rack <span class="text-danger">*</span></label>
                                <select name="rack_id" id="rackSelect" class="form-select" required>
                                    <option value="">Select Rack</option>
                                    @foreach ($racks as $rack)
                                        <option value="{{ $rack->id }}" data-storage="{{ $rack->storage_id }}"
                                            {{ $selectedRack == $rack->id ? 'selected' : '' }}>
                                            {{ $rack->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Bin (filtered by rack) --}}
                            <div class="col-md-3 mt-2">
                                <label class="form-label">Bin (Compartment)<span class="text-danger">*</span></label>
                                <select name="bin_id" id="binSelect" class="form-select" required>
                                    <option value="">Select Bin</option>
                                    @foreach ($bins as $bin)
                                        <option value="{{ $bin->id }}" data-rack="{{ $bin->rack_id }}"
                                            {{ $selectedBin == $bin->id ? 'selected' : '' }}>
                                            {{ $bin->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Container (filtered by bin) --}}
                            <div class="col-md-3 mt-2">
                                <label class="form-label">Container (Actual seed unit - box/tray)<span
                                        class="text-danger">*</span></label>
                                <select name="container_id" id="containerSelect" class="form-select" required>
                                    <option value="">Select Container</option>
                                    @foreach ($containers as $container)
                                        <option value="{{ $container->id }}"
                                            data-bin="{{ $container->bin_id }}"
                                            data-rack="{{ $container->rack_id }}"
                                            {{ $selectedContainer == $container->id ? 'selected' : '' }}>
                                            {{ $container->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @php
                                if (old('germination_percentage') !== null) {
                                    // Validation error → old input
                                    $rows = collect(old('germination_percentage'))->map(function ($val, $i) {
                                        return (object) [
                                            'germination_percentage' => $val,
                                            'moisture_content' => old('moisture_content.' . $i),
                                            'purity_percentage' => old('purity_percentage.' . $i),
                                            'chlorophyll_percentage' => old('chlorophyll_percentage.' . $i),
                                            'water_level_percentage' => old('water_level_percentage.' . $i),
                                            'viability_test_date' => old('viability_test_date.' . $i),
                                            'seed_health_status' => old('seed_health_status.' . $i),
                                            'researcher_id' => old('researcher_id.' . $i),
                                            'researcher_other' => old('researcher_other.' . $i),
                                            'research_date' => old('research_date.' . $i),
                                        ];
                                    });
                                } elseif (isset($lot) && $lot->seedQualities->count()) {
                                    // ✅ EDIT MODE
                                    $rows = $lot->seedQualities;
                                } else {
                                    // CREATE MODE
                                    $rows = collect([(object) []]);
                                }
                            @endphp

                            <div class="col-md-12">
                                <!-- 6. Seed Quality Information -->
                                <div class="card mb-3">
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
                                                    <th>Chlorophyll % </th>
                                                    <th>Water level %</th>
                                                    <th>Viability Date</th>
                                                    <th>Health Status</th>
                                                    <th>Researcher</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Default Row -->
                                                @foreach ($rows as $index => $row)
                                                    <tr>
                                                        <td>
                                                            <input type="number" step="0.01"
                                                                name="germination_percentage[]"
                                                                class="form-control form-control-sm @error('germination_percentage.' . $index) is-invalid @enderror"
                                                                value="{{ $row->germination_percentage ?? '' }}"
                                                                placeholder="e.g. 85.50">

                                                            @error('germination_percentage.' . $index)
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>

                                                        <td>
                                                            <input type="number" step="0.01"
                                                                name="moisture_content[]"
                                                                class="form-control form-control-sm @error('moisture_content.' . $index) is-invalid @enderror"
                                                                value="{{ $row->moisture_content ?? '' }}"
                                                                placeholder="e.g. 12.00">
                                                        </td>

                                                        <td>
                                                            <input type="number" step="0.01"
                                                                name="purity_percentage[]"
                                                                class="form-control form-control-sm @error('purity_percentage.' . $index) is-invalid @enderror"
                                                                value="{{ $row->purity_percentage ?? '' }}"
                                                                placeholder="e.g. 98.00">
                                                        </td>

                                                        <td>
                                                            <input type="number" step="0.01"
                                                                name="chlorophyll_percentage[]"
                                                                class="form-control form-control-sm @error('chlorophyll_percentage.' . $index) is-invalid @enderror"
                                                                value="{{ $row->chlorophyll_percentage ?? '' }}"
                                                                placeholder="e.g. 50.00">
                                                        </td>

                                                        <td>
                                                            <input type="number" step="0.01"
                                                                name="water_level_percentage[]"
                                                                class="form-control form-control-sm @error('water_level_percentage.' . $index) is-invalid @enderror"
                                                                value="{{ $row->water_level_percentage ?? '' }}"
                                                                placeholder="e.g. 80.00">
                                                        </td>

                                                        <td>
                                                            <input type="date" name="viability_test_date[]"
                                                                class="form-control form-control-sm @error('viability_test_date.' . $index) is-invalid @enderror"
                                                                value="{{ $row->viability_test_date ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <select name="seed_health_status[]"
                                                                class="form-select form-select-sm">
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
                                                                class="form-select form-select-sm researcher-select @error('researcher_id.' . $index) is-invalid @enderror">

                                                                <option value="">Select</option>

                                                                @foreach ($users as $user)
                                                                    <option value="{{ $user->id }}"
                                                                        {{ ($row->researcher_id ?? '') == $user->id ? 'selected' : '' }}>
                                                                        {{ $user->name }}
                                                                    </option>
                                                                @endforeach

                                                                @php
                                                                    // "Other" is active when:
                                                                    // 1. researcher_id is null but researcher_other has a value (DB state)
                                                                    // 2. researcher_id is literally 'Other' (old() state)
                                                                    $isOther = (($row->researcher_id ?? null) === null && !empty($row->researcher_other ?? null))
                                                                            || ($row->researcher_id ?? '') === 'Other';
                                                                @endphp

                                                                <option value="Other" {{ $isOther ? 'selected' : '' }}>
                                                                    Other
                                                                </option>
                                                            </select>

                                                            @error('researcher_id.' . $index)
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror

                                                            <!-- Other Input -->
                                                            <input type="text" name="researcher_other[]"
                                                                class="form-control form-control-sm mt-1 other-input"
                                                                placeholder="Enter researcher name"
                                                                value="{{ $row->researcher_other ?? '' }}"
                                                                style="{{ $isOther ? '' : 'display:none;' }}">
                                                        </td>
                                                        <td>
                                                            <input type="date" name="research_date[]"
                                                                class="form-control form-control-sm"
                                                                value="{{ $row->research_date ?? '' }}"
                                                                max="{{ date('Y-m-d') }}">
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm">X</button>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <label class="form-label">Regeneration Cut of Year <span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="regen_year" name="regen_year" class="form-control"
                                            value="{{ old('regen_year', $lot->regen_year ?? '') }}"
                                            placeholder="Enter number only" min="0" max="999"
                                            oninput=" if(this.value.length > 3) this.value = this.value.slice(0,3)">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select" required>
                                            <option value="">Select Status</option>

                                            <option value="active"
                                                {{ old('status', $lot->status ?? '') == 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>

                                            <option value="inactive"
                                                {{ old('status', $lot->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>

                                            <option value="quarantine"
                                                {{ old('status', $lot->status ?? '') == 'quarantine' ? 'selected' : '' }}>
                                                Quarantine
                                            </option>

                                            <option value="depleted"
                                                {{ old('status', $lot->status ?? '') == 'depleted' ? 'selected' : '' }}>
                                                Depleted
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label text-danger">Expiry Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" id="expiry_date" name="expiry_date" class="form-control"
                                            value="{{ old('expiry_date', $lot->expiry_date ?? '') }}"
                                            min="{{ date('Y-m-d') }}">
                                    </div>

                                    <div class="col-md-6 mt-2">
                                        <label class="form-label text-success">Next Regeneration Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" id="recheck_date" name="recheck_date" class="form-control"
                                            value="{{ old('recheck_date', $lot->regeneration_date ?? '') }}"
                                            min="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <label class="form-label">Description / Notes</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Optional notes about this lot">{{ old('description', $lot->description ?? '') }}</textarea>
                            </div>

                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> {{ isset($lot) ? 'Update Lot' : 'Create Lot' }}
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── Arrival Type → show/hide fields + lot number preview ──────────
            const arrivalType = document.getElementById('arrivalType');
            const rejuvenationFields = document.getElementById('rejuvenationFields');
            const prefixField = document.getElementById('prefixField');
            const dispatchnField = document.getElementById('dispatchnField');
            const requestnField = document.getElementById('requestnField');
            const rffLotField = document.getElementById('rffLotField');
            const rffLotInput = document.getElementById('rffLotInput');
            const rffRejuvHidden = document.getElementById('rffRejuvHidden');
            const rffLotFeedback = document.getElementById('rffLotFeedback');
            const rejuvInput = document.getElementById('rejuvenation_program');
            const prefixInput = document.getElementById('prefix');
            const lotPreviewBox = document.getElementById('lotPreviewBox');
            const lotNumberPreview = document.getElementById('lotNumberPreview');
            const dispatchSelect = document.getElementById('dispatchSelect');
            const requestSelect = document.getElementById('requestSelect');
            const lotNumber = document.getElementById('lotNumber');


            // Build lot number for a specific row (1-based rowNum)
            function buildRowLotNumber(ref, rowNum) {
                const type = arrivalType ? arrivalType.value : '';
                const sampleId = document.getElementById('sample_id_input')?.value || '{SID}';
                const rejuv = rejuvInput?.value || '{RP}';
                const pfx = prefixInput?.value || '{PFX}';
                const seq = String(rowNum).padStart(2, '0');

                switch (type) {
                    case 'Rejuvenation':
                        return `${ref||'{REF}'}-${rejuv}/${rowNum}-${pfx}-${sampleId}-${seq}`;
                    case 'Accession Arrival':
                        return `${ref||'{REF}'}-AccA/${rowNum}-${sampleId}-${seq}`;
                    case 'Return From Field':
                        return `${ref||'{REF}'}-${rejuv}/${rowNum}-${pfx}-${sampleId}-${seq}-RF`;
                    default:
                        return '—';
                }
            }



            // Dispatch Change
            if (dispatchSelect) {
                dispatchSelect.addEventListener('change', function() {

                    let selected = this.options[this.selectedIndex];

                    let requestNumber = selected.dataset.request;
                    let lot = selected.dataset.lot;

                    // Auto select request
                    Array.from(requestSelect.options).forEach(option => {

                        option.selected = option.text.trim() === requestNumber?.trim();

                    });

                    // Show lot number
                    lotNumber.value = lot ?? '';

                    // Fetch lot details
                    fetchLotDetails(lot);

                });
            }

            // Request Change
            requestSelect.addEventListener('change', function() {

                let selected = this.options[this.selectedIndex];

                let dispatchId = selected.dataset.dispatchId;
                let lot = selected.dataset.lot;


                // Auto select dispatch
                dispatchSelect.value = dispatchId ?? '';

                // Show lot number
                lotNumber.value = lot ?? '';

                // Fetch lot details
                fetchLotDetails(lot);

            });

            function fetchLotDetails(lotNumberValue) {

                if (!lotNumberValue) return;

                fetch(`/get-lot-details?lot_number=${encodeURIComponent(lotNumberValue)}`)
                    .then(res => res.json())
                    .then(data => {

                        if (data.status) {

                            let lot = data.lot;

                            // Show fields
                            rejuvenationFields.style.display = '';
                            prefixField.style.display = '';

                            // Auto fill values
                            if (rejuvInput) {
                                rejuvInput.value = lot.rejuvenation_program ?? '';
                            }

                            if (prefixInput) {
                                prefixInput.value = lot.prefix ?? '';
                            }

                            // sample_id auto fill
                            let sampleInput = document.getElementById('sample_id_input');

                            if (sampleInput) {
                                sampleInput.value = lot.sample_id ?? '';
                            }

                            // hidden field
                            if (rffRejuvHidden) {
                                rffRejuvHidden.value = lot.lot_number ?? '';
                            }

                            // rebuild preview
                            buildLotPreview();

                        }
                    })
                    .catch(err => {
                        console.log(err);
                    });

            }

            // ── Return From Field: lot number search ─────────────────────────────
            function fetchRffLot(lotNum) {
                if (!lotNum) return;
                rffLotFeedback.innerHTML = '<span class="text-muted">Searching…</span>';

                fetch(`/get-lot-by-number?lot_number=${encodeURIComponent(lotNum)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.status && data.lot) {
                            const lot = data.lot;
                            // Store lot number into the single hidden rejuvenation_program field
                            rffRejuvHidden.value = lot.lot_number;
                            rffLotInput.value = lot.lot_number;
                            rffLotFeedback.innerHTML =
                                `<span class="text-success"><i class="ri-check-line"></i> Found: <strong>${lot.lot_number}</strong>` +
                                (lot.crop ? ` | Crop: ${lot.crop}` : '') +
                                (lot.accession ? ` | Acc: ${lot.accession}` : '') +
                                `</span>`;
                            buildLotPreview();
                        } else {
                            rffRejuvHidden.value = '';
                            rffLotFeedback.innerHTML =
                                '<span class="text-danger"><i class="ri-close-line"></i> Lot not found.</span>';
                        }
                    })
                    .catch(() => {
                        rffLotFeedback.innerHTML = '<span class="text-danger">Error fetching lot.</span>';
                    });
            }

            if (document.getElementById('rffLotSearchBtn')) {
                document.getElementById('rffLotSearchBtn').addEventListener('click', function() {
                    fetchRffLot(rffLotInput?.value?.trim());
                });
            }

            if (rffLotInput) {
                // Search on Enter key
                rffLotInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        fetchRffLot(this.value.trim());
                    }
                });
                // Sync typed value directly to hidden field as fallback
                rffLotInput.addEventListener('input', function() {
                    rffRejuvHidden.value = this.value;
                });
            }

            // Sync Rejuvenation visible input → single hidden field
            if (rejuvInput) {
                rejuvInput.addEventListener('input', function() {
                    if (arrivalType?.value === 'Rejuvenation') {
                        rffRejuvHidden.value = this.value;
                    }
                });
            }

            // Update all row previews + the header preview box
            function buildLotPreview() {
                const type = arrivalType ? arrivalType.value : '';
                const hasType = type !== '';

                // Show/hide the preview column header + all cells
                document.querySelectorAll('.lot-preview-col').forEach(el => {
                    el.style.display = hasType ? '' : 'none';
                });

                // Show/hide top preview box (uses first row's ref)
                if (lotPreviewBox) lotPreviewBox.style.display = hasType ? '' : 'none';

                // Update each row's preview
                const rows = document.querySelectorAll('#tableBodyQuantity tr');
                rows.forEach((row, i) => {
                    const ref = row.querySelector('.refNumber')?.value || '';
                    const preview = row.querySelector('.row-lot-preview');
                    if (preview) {
                        preview.textContent = hasType ? buildRowLotNumber(ref, i + 1) : '—';
                    }
                });

                // Also update the top header preview (row 1)
                if (lotNumberPreview) {
                    const firstRef = document.querySelector('#tableBodyQuantity .refNumber')?.value || '';
                    lotNumberPreview.textContent = hasType ? buildRowLotNumber(firstRef, 1) : '—';
                }
            }

            function toggleArrivalFields() {
                const type = arrivalType ? arrivalType.value : '';
                const isRejuv = type === 'Rejuvenation';
                const isRFF = type === 'Return From Field';

                if (rejuvenationFields) {
                    rejuvenationFields.style.display = (isRejuv || isRFF) ? '' : 'none';
                }

                if (prefixField) {
                    prefixField.style.display = (isRejuv || isRFF) ? '' : 'none';
                }

                if (dispatchnField) dispatchnField.style.display = isRFF ? '' : 'none';
                if (requestnField) requestnField.style.display = isRFF ? '' : 'none';
                if (rffLotField) rffLotField.style.display = isRFF ? '' : 'none';

                if (rejuvInput) rejuvInput.required = isRejuv;
                if (prefixInput) prefixInput.required = isRejuv;

                if (dispatchSelect) dispatchSelect.required = isRFF;
                if (requestSelect) requestSelect.required = isRFF;

                // Sync hidden field based on active type
                if (isRejuv && rejuvInput && rffRejuvHidden) {
                    rffRejuvHidden.value = rejuvInput.value;
                }
                if (isRFF && rffLotInput && rffRejuvHidden) {
                    rffRejuvHidden.value = rffLotInput.value;
                }
                if (!isRejuv && !isRFF && rffRejuvHidden) {
                    rffRejuvHidden.value = '';
                }

                buildLotPreview();
            }

            // Edit mode auto load
            if (dispatchSelect && dispatchSelect.value) {

                let selected = dispatchSelect.options[dispatchSelect.selectedIndex];

                let lot = selected.dataset.lot || '';

                if (lot) {

                    lotNumber.value = lot;

                    fetchLotDetails(lot);
                }
            }

            if (arrivalType) {
                arrivalType.addEventListener('change', toggleArrivalFields);
                toggleArrivalFields(); // run on page load for edit mode
            }

            // Update previews when any relevant field changes
            document.addEventListener('input', function(e) {
                const name = e.target.name;
                if (['reference_number[]', 'rejuvenation_program', 'prefix'].includes(name) ||
                    e.target.id === 'sample_id_input') {
                    buildLotPreview();
                }
            });

            // EDIT MODE AUTO SELECT
            if (dispatchSelect && dispatchSelect.value) {

                let selectedDispatch =
                    dispatchSelect.options[dispatchSelect.selectedIndex];

                let requestNumber =
                    selectedDispatch.dataset.request || '';

                let lot =
                    selectedDispatch.dataset.lot || '';

                // Auto select request
                Array.from(requestSelect.options).forEach(option => {

                    option.selected =
                        option.text.trim() === requestNumber.trim();

                });

                // Show lot
                if (lotNumber) {
                    lotNumber.value = lot;
                }

                // Hidden field
                if (rffRejuvHidden) {
                    rffRejuvHidden.value = lot;
                }

                // Fetch details
                fetchLotDetails(lot);
            }

            // ── Edit Lot ──────────────────────────────────────────────────────────
            document.querySelectorAll('.editLotBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const d = this.dataset;
                    document.getElementById('edit_accession_id').value = d.accession_id || '';
                    document.getElementById('edit_storage_id').value = d.storage_id || '';
                    document.getElementById('edit_expiry').value = d.expiry || '';
                    document.getElementById('edit_germination').value = d.germination || '';
                    document.getElementById('edit_moisture').value = d.moisture || '';
                    document.getElementById('edit_purity').value = d.purity || '';
                    document.getElementById('edit_chlorophyll').value = d.chlorophyll || '';
                    document.getElementById('edit_waterLevel').value = d.waterLevel || '';
                    document.getElementById('edit_status').value = d.status || 'active';
                    document.getElementById('edit_description').value = d.description || '';
                    document.getElementById('editLotForm').action = `/lot-management/${d.id}`;
                    document.getElementById('formMethod').value = 'PUT';
                });
            });
            let laddBtn = document.getElementById('addLotBtn');

            if (laddBtn) {
                laddBtn.addEventListener('click', function() {
                    document.getElementById('editLotForm').action = `/lot-management`;
                    document.getElementById('formMethod').value = 'POST';
                });
            }

            // ── Accession select → load details ──────────────────────────────────
            document.getElementById('accessionSelect').addEventListener('change', function() {
                const id = this.value;
                const box = document.getElementById('accessionDetails');
                _accessionData = null;

                // clear auto-filled fields
                //document.getElementById('expiryDate').value     = '';

                if (!id) {
                    box.classList.add('d-none');
                    updateBalance();
                    return;
                }

                fetch(`/lot-management/accession/${id}`)
                    .then(r => r.json())
                    .then(d => {
                        _accessionData = d;
                        document.getElementById('ad_crop').textContent = d.crop || '—';
                        document.getElementById('ad_time').textContent = d.storage_time || '—';
                        document.getElementById('ad_scientific').textContent = d.scientific_name || '—';
                        document.getElementById('ad_status').textContent = d.status || '—';
                        document.getElementById('ad_collected').textContent = d.collection_date || '—';
                        document.getElementById('ad_sample_id').textContent = d.sample_id || '—';
                        document.getElementById('ad_regeneration').textContent = d.regen_year || '—';
                        document.getElementById('regen_year').value = d.regen_year || '—';
                        box.classList.remove('d-none');

                        document.getElementById('sample_id_input').value = d.sample_id || '';

                        buildLotPreview();
                        updateBalance();
                    })
                    .catch(() => box.classList.add('d-none'));
            });

            // ── Storage select → load details ────────────────────────────────────
            let _storageData = null;
            let _accessionData = null;

            function getTotalQty() {
                let total = 0;
                document.querySelectorAll('#tableBodyQuantity .quantity').forEach(el => {
                    total += parseFloat(el.value || 0);
                });
                return total;
            }

            function updateBalance() {
                const balEl = document.getElementById('sd_balance');
                const totalEl = document.getElementById('totalQuantity');
                const available = _storageData ? parseFloat(_storageData.available || 0) : null;
                const unit = _storageData ? (_storageData.unit || '') : '';
                const totalQty = getTotalQty();

                // Update total quantity display
                if (totalEl) {
                    totalEl.textContent = totalQty.toFixed(2);
                }

                if (!_storageData || !balEl) return;

                const balance = available - totalQty;
                balEl.textContent = `${balance.toFixed(2)} ${unit}`;
                balEl.className = 'ms-1 fw-bold ' + (balance < 0 ? 'text-danger' : 'text-success');

                // Highlight quantity inputs that push over capacity
                document.querySelectorAll('#tableBodyQuantity .quantity').forEach(input => {
                    const val = parseFloat(input.value || 0);
                    if (balance < 0 && val > 0) {
                        input.classList.add('is-invalid');
                        input.title = `Total exceeds available capacity (${available} ${unit})`;
                    } else {
                        input.classList.remove('is-invalid');
                        input.title = '';
                    }
                });
            }

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('quantity')) {
                    updateBalance();
                }
            });




            const units = @json($units);

            // Filter unit dropdowns based on storage capacity unit
            // storageUnitCode: the storage's own unit (g, kg, mg, ton…)
            // capacityInGrams: capacity converted to grams for threshold logic
            function filterUnitsByCapacity(capacityInGrams, storageUnitCode) {
                const code = (storageUnitCode || '').toLowerCase();

                // Determine which units are compatible
                // Rule: show units that are <= the storage unit scale
                // e.g. storage in kg → show kg, g, mg
                //      storage in g  → show g, mg
                //      storage in mg → show mg only
                const scaleOrder = ['mg', 'g', 'kg', 'ton'];
                const storageScale = scaleOrder.indexOf(code);

                document.querySelectorAll('select[name="unit_id[]"]').forEach(select => {
                    const current = select.value;
                    select.innerHTML = '<option value="">Unit</option>';

                    units.forEach(unit => {
                        const uCode = unit.code.toLowerCase();
                        const uScale = scaleOrder.indexOf(uCode);

                        // If storage unit is known, show units at same or smaller scale
                        // If unknown, show all
                        const show = storageScale === -1 || uScale <= storageScale;

                        if (show) {
                            const opt = document.createElement('option');
                            opt.value = unit.id;
                            opt.textContent = `${unit.name} (${unit.code})`;
                            select.appendChild(opt);
                        }
                    });

                    // Restore previous selection if still available
                    if ([...select.options].some(o => o.value == current)) {
                        select.value = current;
                    }
                });
            }

            // On edit: trigger unit filter for the pre-selected storage
            @if (isset($lot) && $lot->storage_id)
                (function() {
                    // Capture server-rendered selected values BEFORE the filter rebuilds options
                    const preSelected = {};
                    document.querySelectorAll('select[name="unit_id[]"]').forEach((sel, i) => {
                        // Read the selected option value directly from the DOM (set by server)
                        preSelected[i] = sel.querySelector('option[selected]')?.value
                                      ?? sel.value
                                      ?? '';
                    });

                    fetch(`/lot-management/storage/{{ $lot->storage_id }}`)
                        .then(r => r.json())
                        .then(d => {
                            filterUnitsByCapacity(d.capacity_in_grams, d.unit_code);
                            // Restore server-rendered selections after filter rebuild
                            document.querySelectorAll('select[name="unit_id[]"]').forEach((sel, i) => {
                                const saved = preSelected[i];
                                if (saved && [...sel.options].some(o => o.value == saved)) {
                                    sel.value = saved;
                                }
                            });
                        });
                })();
            @endif

            // ── Cascade data ─────────────────────────────────────────────────────
            const allStoragesData = @json($storages->map(fn($s) => ['id' => $s->id, 'warehouse_id' => $s->warehouse_id]));
            const allSectionsData = @json($sections->map(fn($s) => ['id' => $s->id, 'storage_id' => $s->storage_id]));
            const allRacksData = @json($racks->map(fn($r) => ['id' => $r->id, 'storage_id' => $r->storage_id]));
            const allBinsData = @json($bins->map(fn($b) => ['id' => $b->id, 'rack_id' => $b->rack_id]));

            // ── Helper: show/hide options ─────────────────────────────────────────
            function filterOptions(selectId, dataAttr, parentVal, resetVal = true) {
                const sel = document.getElementById(selectId);
                if (!sel) return;
                const prev = sel.value;
                Array.from(sel.options).forEach(opt => {
                    if (!opt.value) return; // keep placeholder
                    opt.hidden = parentVal ? (opt.dataset[dataAttr] != parentVal) : false;
                });
                // Reset if current selection is now hidden
                if (resetVal && sel.options[sel.selectedIndex]?.hidden) {
                    sel.value = '';
                    sel.dispatchEvent(new Event('change'));
                }
            }

            // ── Warehouse → filter Storage ────────────────────────────────────────
            document.getElementById('warehouseSelect').addEventListener('change', function() {
                const wid = this.value;
                filterOptions('storageSelect', 'warehouse', wid);
                // If storage was cleared, cascade down
                if (!document.getElementById('storageSelect').value) {
                    filterOptions('rackSelect', 'storage', '', false);
                    filterOptions('binSelect', 'rack', '', false);
                    filterOptions('containerSelect', 'bin', '', false);
                    document.getElementById('storageDetails').classList.add('d-none');
                    _storageData = null;
                }
            });

            // ── Storage → load details + filter Section ───────────────────────────
            document.getElementById('storageSelect').addEventListener('change', function() {
                const id = this.value;
                const box = document.getElementById('storageDetails');
                _storageData = null;

                // Sync warehouse select to match chosen storage
                if (id) {
                    const stObj = allStoragesData.find(s => s.id == id);
                    if (stObj) {
                        const whSel = document.getElementById('warehouseSelect');
                        if (whSel && whSel.value != stObj.warehouse_id) {
                            whSel.value = stObj.warehouse_id;
                            // Re-filter storage options without resetting the current value
                            filterOptions('storageSelect', 'warehouse', stObj.warehouse_id, false);
                        }
                    }
                }

                // Filter racks by this storage
                filterOptions('rackSelect', 'storage', id);
                // Cascade reset bin and container when storage changes
                filterOptions('binSelect', 'rack', '', false);
                filterOptions('containerSelect', 'bin', '', false);

                if (!id) {
                    box.classList.add('d-none');
                    updateBalance();
                    return;
                }

                fetch(`/lot-management/storage/${id}`)
                    .then(r => r.json())
                    .then(d => {
                        _storageData = d;
                        document.getElementById('sd_warehouse').textContent = d.warehouse || '—';
                        document.getElementById('sd_type').textContent = d.storage_type || '—';
                        document.getElementById('sd_condition').textContent = d.storage_condition ||
                            '—';
                        document.getElementById('sd_time').textContent = d.storage_time || '—';
                        document.getElementById('sd_capacity').textContent = d.capacity ?
                            `${d.capacity} ${d.unit || ''}` : '—';
                        document.getElementById('sd_available').textContent = d.available ?
                            `${d.available} ${d.unit || ''}` : '—';
                        document.getElementById('sd_temp').textContent = d.temperature ?
                            `${d.temperature} °C` : '—';
                        document.getElementById('sd_humidity').textContent = d.humidity ?
                            `${d.humidity} %` : '—';
                        box.classList.remove('d-none');
                        filterUnitsByCapacity(d.capacity_in_grams, d.unit_code);
                        updateBalance();
                    })
                    .catch(() => box.classList.add('d-none'));
            });

            // ── Rack → filter Bin ─────────────────────────────────────────────────
            document.getElementById('rackSelect').addEventListener('change', function() {
                filterOptions('binSelect', 'rack', this.value);
                // Clear container when rack changes
                filterOptions('containerSelect', 'bin', '', false);
            });

            // ── Bin → filter Container ────────────────────────────────────────────
            document.getElementById('binSelect').addEventListener('change', function() {
                filterOptions('containerSelect', 'bin', this.value);
            });

            // ── Container → auto-fill Rack & Bin ─────────────────────────────────
            document.getElementById('containerSelect').addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (!this.value) return;

                const rackId = opt.dataset.rack || '';
                const binId  = opt.dataset.bin  || '';

                const rackSel = document.getElementById('rackSelect');
                const binSel  = document.getElementById('binSelect');

                // ── Auto-fill Rack if container has one ───────────────────────
                if (rackId && rackSel.value !== rackId) {
                    rackSel.value = rackId;
                    // Re-filter bins for this rack (no reset so bin stays selectable)
                    filterOptions('binSelect', 'rack', rackId, false);
                }

                // ── Auto-fill Bin if container has one ────────────────────────
                if (binId && binSel.value !== binId) {
                    binSel.value = binId;
                }
            });

            // ── On page load: restore cascade state (edit mode) ───────────────────
            (function initCascade() {
                const whSel  = document.getElementById('warehouseSelect');
                const stSel  = document.getElementById('storageSelect');
                const rkSel  = document.getElementById('rackSelect');
                const bnSel  = document.getElementById('binSelect');
                const cnSel  = document.getElementById('containerSelect');

                // If container is pre-selected but rack/bin are not,
                // derive them from the container's data attributes
                if (cnSel.value && (!rkSel.value || !bnSel.value)) {
                    const cnOpt = cnSel.options[cnSel.selectedIndex];
                    if (!rkSel.value && cnOpt.dataset.rack) rkSel.value = cnOpt.dataset.rack;
                    if (!bnSel.value && cnOpt.dataset.bin)  bnSel.value = cnOpt.dataset.bin;
                }

                // Restore warehouse from pre-selected storage
                if (stSel.value && !whSel.value) {
                    const stObj = allStoragesData.find(s => s.id == stSel.value);
                    if (stObj) whSel.value = stObj.warehouse_id;
                }

                // Apply warehouse filter on storage options (no reset)
                if (whSel.value) filterOptions('storageSelect', 'warehouse', whSel.value, false);

                // Apply section filter on rack options (no reset)
                if (stSel.value) filterOptions('rackSelect', 'storage', stSel.value, false);

                // Apply rack filter on bin options (no reset)
                if (rkSel.value) filterOptions('binSelect', 'rack', rkSel.value, false);

                // Apply bin filter on container options (no reset)
                if (bnSel.value) filterOptions('containerSelect', 'bin', bnSel.value, false);

                // Load storage details if pre-selected
                if (stSel.value) stSel.dispatchEvent(new Event('change'));
            })();


            // =========================
            // SEED QUANTITY (FIXED DUPLICATE)
            // =========================
            const quantityInput = document.getElementById('quantity');
            const percentSelect = document.getElementById('showUserPercent');
            const userField = document.getElementById('userQuantity');
            const minField = document.getElementById('minQuantity');

            function calculateUserQuantity() {
                let quantity = parseFloat(quantityInput.value) || 0;
                let percent = parseFloat(percentSelect.value) || 0;

                let userQty = (quantity * percent) / 100;
                let minQty = quantity - userQty;

                // Set Available for User
                if (userField) {
                    userField.value = userQty ? userQty.toFixed(2) : '';
                }

                // Set Min Stock Balance
                if (minField) {
                    minField.value = minQty ? minQty.toFixed(2) : '';
                }
            }

            if (quantityInput && percentSelect) {
                quantityInput.addEventListener('input', calculateUserQuantity);
                percentSelect.addEventListener('change', calculateUserQuantity);
            }

            const tableBody = document.querySelector('#seedContainer tbody');
            const addBtn = document.getElementById('addSeedRowBtn');

            // =========================
            // ADD ROW
            // =========================


            if (addBtn && tableBody) {
                addBtn.addEventListener('click', function() {

                    let firstRow = tableBody.querySelector('tr');
                    if (!firstRow) return;

                    let newRow = firstRow.cloneNode(true);

                    // Clear values
                    newRow.querySelectorAll('input, select').forEach(el => {
                        if (el.tagName === 'SELECT') {
                            el.selectedIndex = 0;
                        } else {
                            el.value = '';
                        }
                    });

                    // Hide other input
                    newRow.querySelectorAll('.other-input').forEach(el => {
                        el.style.display = 'none';
                    });

                    tableBody.appendChild(newRow);
                });
            }

            // =========================
            // REMOVE ROW (WORKS ALWAYS)
            // =========================


            tableBody.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-danger')) {

                    let row = e.target.closest('tr');

                    if (tableBody.rows.length > 1) {
                        row.remove();
                    } else {
                        alert('At least one row required');
                    }
                }
            });

            // =========================
            // TOGGLE OTHER (WORKS FOR ALL ROWS)
            // =========================
            tableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('researcher-select')) {

                    let select = e.target;
                    let td = select.closest('td');
                    let otherInput = td.querySelector('.other-input');

                    if (!otherInput) return;

                    if (select.value === 'Other') {
                        otherInput.style.display = 'block';
                    } else {
                        otherInput.style.display = 'none';
                        otherInput.value = '';
                    }
                }
            });

            // =========================
            // EDIT MODE FIX 🔥
            // =========================
            document.querySelectorAll('.researcher-select').forEach(select => {

                let td = select.closest('td');
                let otherInput = td.querySelector('.other-input');

                if (!otherInput) return;

                // IMPORTANT FIX
                if (select.value === 'Other' || otherInput.value !== '') {
                    select.value = 'Other';
                    otherInput.style.display = 'block';
                } else {
                    otherInput.style.display = 'none';
                }
            });

            const ptableBody = document.querySelector('#passportTable tbody');
            const paddBtn = document.getElementById('addRow');

            let rowIndex = ptableBody ? ptableBody.rows.length : 1;

            // =========================
            // ADD ROW (WITH INDEX FIX)
            // =========================
            if (paddBtn && ptableBody) {
                paddBtn.addEventListener('click', function() {

                    let firstRow = ptableBody.querySelector('tr');
                    if (!firstRow) return;

                    let newRow = firstRow.cloneNode(true);

                    // Update index in name=""
                    newRow.querySelectorAll('input').forEach(input => {

                        let name = input.getAttribute('name');

                        if (name) {
                            let newName = name.replace(/\[\d+\]/, `[${rowIndex}]`);
                            input.setAttribute('name', newName);
                        }

                        input.value = '';
                    });

                    ptableBody.appendChild(newRow);
                    rowIndex++;
                });
            }

            // =========================
            // DELETE ROW (WORKING FIX)
            // =========================
            if (ptableBody) {
                ptableBody.addEventListener('click', function(e) {

                    if (e.target.classList.contains('removeRow')) {

                        let row = e.target.closest('tr');

                        if (ptableBody.rows.length > 1) {
                            row.remove();
                        } else {
                            alert('At least one row required');
                        }
                    }
                });
            }

            ///

            const tableBodyQuantity = document.getElementById('tableBodyQuantity');

            // ADD ROW
            tableBodyQuantity.addEventListener('click', function(e) {
                if (e.target.classList.contains('addRowQ')) {

                    // Block adding row if already over capacity
                    if (_storageData) {
                        const available = parseFloat(_storageData.available || 0);
                        const total = getTotalQty();
                        if (total >= available) {
                            alert(
                                `Storage capacity reached. Available: ${available} ${_storageData.unit || ''}. Cannot add more rows.`
                                );
                            return;
                        }
                    }

                    let newRow = tableBodyQuantity.rows[0].cloneNode(true);

                    // Clear values
                    newRow.querySelectorAll('input').forEach(input => input.value = '');
                    newRow.querySelectorAll('select').forEach(select => select.value = '');

                    // Clear the lot preview text in the new row
                    const previewSpan = newRow.querySelector('.row-lot-preview');
                    if (previewSpan) previewSpan.textContent = '—';

                    tableBodyQuantity.appendChild(newRow);

                    // Re-apply unit filter to the new row's unit select
                    const storageId = document.getElementById('storageSelect').value;
                    if (storageId) {
                        fetch(`/lot-management/storage/${storageId}`)
                            .then(r => r.json())
                            .then(d => filterUnitsByCapacity(d.capacity_in_grams, d.unit_code));
                    }

                    // Rebuild all row previews with updated row numbers
                    buildLotPreview();
                }

                // REMOVE ROW
                if (e.target.classList.contains('removeRowQ')) {
                    if (tableBodyQuantity.rows.length > 1) {
                        e.target.closest('tr').remove();
                        // Rebuild previews so row numbers stay correct
                        buildLotPreview();
                    } else {
                        alert('At least one row required');
                    }
                }
            });

            // AUTO CALCULATION — handles both quantity input and percent select change
            function recalcRow(row) {
                let qty = parseFloat(row.querySelector('.quantity').value) || 0;
                let percent = parseFloat(row.querySelector('.percent').value) || 0;
                let userQty = (qty * percent) / 100;
                let minQty = qty - userQty;
                row.querySelector('.userQty').value = userQty ? userQty.toFixed(2) : '';
                row.querySelector('.min').value = minQty ? minQty.toFixed(2) : '';
            }

            tableBodyQuantity.addEventListener('input', function(e) {
                recalcRow(e.target.closest('tr'));
            });

            tableBodyQuantity.addEventListener('change', function(e) {
                if (e.target.classList.contains('percent')) {
                    recalcRow(e.target.closest('tr'));
                }
            });

            $(document).on('input', '.refNumber', function() {

                let values = [];
                let duplicateFound = false;

                $('.refNumber').each(function() {
                    let val = $(this).val().trim();

                    if (val !== '') {
                        if (values.includes(val)) {
                            duplicateFound = true;
                            $(this).addClass('is-invalid');
                        } else {
                            values.push(val);
                            $(this).removeClass('is-invalid');
                        }
                    }
                });

                // remove invalid from non-duplicates
                $('.refNumber').each(function() {
                    let val = $(this).val().trim();
                    if (values.filter(v => v === val).length === 1) {
                        $(this).removeClass('is-invalid');
                    }
                });

            });

            $('form').on('submit', function(e) {

                // ── Sync rejuvenation_program hidden field ──────────────────────
                const type = arrivalType?.value;
                if (type === 'Rejuvenation' && rejuvInput) {
                    rffRejuvHidden.value = rejuvInput.value;
                } else if (type === 'Return From Field' && rffLotInput) {
                    rffRejuvHidden.value = rffLotInput.value;
                } else {
                    rffRejuvHidden.value = '';
                }

                // ── Capacity check ──────────────────────────────────────────────
                if (_storageData) {
                    const available = parseFloat(_storageData.available || 0);
                    const total = getTotalQty();
                    if (total > available) {
                        e.preventDefault();
                        alert(
                            `Total quantity (${total.toFixed(2)}) exceeds available storage capacity (${available.toFixed(2)} ${_storageData.unit || ''}). Please reduce quantities.`
                            );
                        return;
                    }
                }

                // ── Duplicate reference check ───────────────────────────────────
                let values = [];
                let hasDuplicate = false;

                $('.refNumber').each(function() {
                    let val = $(this).val().trim();
                    if (val !== '') {
                        if (values.includes(val)) {
                            hasDuplicate = true;
                            $(this).addClass('is-invalid');
                        } else {
                            values.push(val);
                        }
                    }
                });

                if (hasDuplicate) {
                    e.preventDefault();
                    alert('Duplicate reference numbers found!');
                }

            });

            // =========================
            // DATE AUTO CALC - regeneration cut of year
            // =========================

            const expiryInput = document.getElementById('expiry_date');
            const regenInput = document.getElementById('recheck_date');
            const regenYearInput = document.getElementById('regen_year');

            @php
                $seasonStart = 0;
                $seasonEnd = 0;
                if (isset($accession) && $accession && $accession->crop && $accession->crop->season) {
                    $seasonStart = (int) $accession->crop->season->start_month;
                    $seasonEnd = (int) $accession->crop->season->end_month;
                }
                if (isset($accession) && $accession && $accession->crop) {
                    $seasonStart = (int) $accession->crop->season_start_month_id;
                    $seasonEnd = (int) $accession->crop->season_end_month_id;
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
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
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
            const cropSelect = document.getElementById('accessionSelect');

            cropSelect.addEventListener('change', function() {

                const selectedOption =
                    this.options[this.selectedIndex];

                // AUTO FILL REGEN YEAR
                const regenYear =
                    selectedOption.dataset.regen || '';

                regenYearInput.value = regenYear;

                // AUTO FILL SEASON
                window._cropSeason.start_month =
                    parseInt(selectedOption.dataset.seasonStart || 0);

                window._cropSeason.end_month =
                    parseInt(selectedOption.dataset.seasonEnd || 0);

                console.log('Season Start:',
                    window._cropSeason.start_month);

                console.log('Season End:',
                    window._cropSeason.end_month);

                // AUTO CALCULATE
                calculateAllDates();

            });

            regenYearInput.addEventListener('input', function() {

                if (cropSelect.value === '') {

                    alert('Please select a Accession first.');

                    this.value = '';

                    expiryInput.value = '';
                    regenInput.value = '';

                    cropSelect.focus();

                    return;
                }

                calculateAllDates();
            });

            /* cropSelect.addEventListener('change', function () {
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
            /* window.addEventListener('load', function () {
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
             });*/

            // CALCULATION LOGIC:
            //   Expiry     = today + regen_years  (same day/month, N years ahead)
            //   Regen Date = expiry month IN season  → same date as expiry
            //              = expiry month OUT of season → (expiry year - 1), season start month, same day
            // Example: today=29-Apr-2026, years=2, Kharif(Jun-Oct)
            //   Expiry = 29-Apr-2028  (Apr is outside Jun-Oct)
            //   Regen  = 29-Jun-2027  (2028-1=2027, start month=Jun, day=29)
            window.calculateAllDates = function() {

                const years =
                    parseFloat(regenYearInput.value);

                if (isNaN(years) || years <= 0) {

                    expiryInput.value = '';
                    regenInput.value = '';

                    return;
                }

                const today = new Date();

                // =========================
                // EXPIRY DATE
                // =========================

                const expiry = new Date(today);

                expiry.setFullYear(
                    expiry.getFullYear() + years
                );

                expiryInput.value =
                    formatDate(expiry);

                // =========================
                // SEASON DATA
                // =========================

                let startMonth =
                    parseInt(window._cropSeason.start_month || 0);

                let endMonth =
                    parseInt(window._cropSeason.end_month || 0);

                console.log('Checking Season:',
                    startMonth,
                    endMonth);

                // If season missing
                if (!startMonth || !endMonth) {

                    regenInput.value =
                        formatDate(expiry);

                    return;
                }

                // =========================
                // CHECK EXPIRY MONTH
                // =========================

                const expiryMonth =
                    expiry.getMonth() + 1;

                let regenerationDate;

                // expiry month inside season
                if (
                    isMonthInSeason(
                        expiryMonth,
                        startMonth,
                        endMonth
                    )
                ) {

                    regenerationDate =
                        new Date(expiry);

                } else {

                    // move to season start month
                    regenerationDate = new Date(
                        expiry.getFullYear() - 1,
                        startMonth - 1,
                        1
                    );
                }

                regenInput.value =
                    formatDate(regenerationDate);
            };



        });
    </script>
@endpush
