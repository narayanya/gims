@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Lot Management</h3>
                <p class="text-muted mb-0" style="font-size:13px">Create and manage germplasm lots</p>
            </div>
            <a href="{{ route('lot-management') }}" class="btn btn-primary btn-sm">
                <i class="ri-arrow-left-line me-1"></i> Back to list
            </a>
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
                <form method="POST" action="{{ isset($lot) ? route('lot-management.update', $lot->id) : route('lot-management.store') }}">
                @csrf
                @if(isset($lot))
                    @method('PUT')
                @endif
                <div class="card-header">
                    <h5 class="modal-title" id="addLotModalLabel">
                        <i class="ri-stack-line me-2"></i>Add New Lot
                    </h5>
                </div>
                <div class="card-body" style="overflow-y:auto; flex:1;">
                    <div class="row g-3">

                        {{-- ── Section 1: Lot Info ── --}}
                        <div class="col-12"><h6 class="text-muted border-bottom pb-1">Lot Information</h6></div>
                        {{-- Lot Number (auto-generated) --}}
                        {{--<div class="col-md-8 d-none">
                        <div class="alert alert-info py-2 d-flex align-items-center gap-3 mb-0">
                            <i class="ri-barcode-line fs-4"></i>
                            <div>
                                <small class="text-muted d-block">Auto-generated Number</small>
                                <strong id="lotNumberDisplay" class="fs-5">{{ $nextLotNo }}</strong>
                            </div>
                        </div>
                    </div>--}}
                    
                        
                        <div class="col-md-3 mt-2">
                            <label class="form-label">Rejuvenation Program <span class="text-danger">*</span></label>
                            <input type="text" name="rejuvenation_program" class="form-control" value="{{ old('rejuvenation_program', $lot->rejuvenation_program ?? '') }}" placeholder="Enter rejuvenation program" {{ isset($lot) ? 'readonly style=background-color:#e9ecef;' : '' }} required>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-label">Prefix <span class="text-danger">*</span></label>
                            <input type="text" name="prefix" class="form-control" value="{{ old('prefix', $lot->prefix ?? '') }}" placeholder="Enter prefix" {{ isset($lot) ? 'readonly style=background-color:#e9ecef;' : '' }} required>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-label">Sample ID <span class="text-danger">*</span></label>
                            <input type="text" name="sample_id" class="form-control" value="{{ old('sample_id', $lot->sample_id ?? '') }}" placeholder="Enter sample ID" {{ isset($lot) ? 'readonly style=background-color:#e9ecef;' : '' }} required>
                        </div>

                        <div class="col-md-3 mt-2">
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

                        {{-- ── Section 3: Storage ── --}}
                        <div class="col-12 mt-3"><h6 class="text-muted border-bottom pb-1">Storage</h6></div>
                        @php
                            $selectedStorage = old('storage_id', $lot->storage_id ?? '');
                            $selectedAccession = old('accession_id', $lot->accession_id ?? '');
                            $selectedSection   = old('section_id', $lot->section_id ?? '');
                            $selectedRack      = old('rack_id', $lot->rack_id ?? '');
                            $selectedBin       = old('bin_id', $lot->bin_id ?? '');
                            $selectedContainer = old('container_id', $lot->container_id ?? '');
                        @endphp
                        <div class="col-md-4 mt-2">
                            <label class="form-label">Storage <span class="text-danger">*</span></label>
                            <select name="storage_id" id="storageSelect" class="form-select" required>
                                <option value="">Select Storage</option>
                                @foreach($storages as $s)
                                    <option value="{{ $s->id }}" {{ $selectedStorage == $s->id ? 'selected' : '' }}>{{ $s->storage_id }} — {{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        

                        <div class="col-md-8 mt-2">
                            {{-- Storage Details Card --}}
                            <div id="storageDetails" class="card border bg-light mb-0 d-none">
                                <div class="card-body py-2 px-3">
                                    <div class="row g-1 small">
                                        <div class="col-4"><span class="text-muted">Warehouse:</span> <span id="sd_warehouse">—</span></div>
                                        <div class="col-4"><span class="text-muted">Type:</span> <span id="sd_type">—</span></div>
                                        <div class="col-4"><span class="text-muted">Condition:</span> <span id="sd_condition">—</span></div>
                                        <div class="col-4"><span class="text-muted">Time:</span> <span id="sd_time">—</span></div>
                                        <div class="col-4"><span class="text-muted">Capacity:</span> <span id="sd_capacity">—</span></div>
                                        <div class="col-4"><span class="text-muted">Available:</span> <span id="sd_available">—</span></div>
                                        <div class="col-4"><span class="text-muted">Temp:</span> <span id="sd_temp">—</span></div>
                                        <div class="col-4"><span class="text-muted">Humidity:</span> <span id="sd_humidity">—</span></div>
                                        <div class="col-12 mt-1 pt-1 border-top">
                                            <span class="text-muted">Balance (after lot qty):</span>
                                            <strong id="sd_balance" class="ms-1">—</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ── Section 2: Accession ── --}}
                        <div class="col-12 mt-3"><h6 class="text-muted border-bottom pb-1">Accession</h6></div>

                        
                        <div class="col-md-4 mt-2">
                            <label class="form-label">Accession Number <span class="text-danger">*</span></label>
                            <select name="accession_id" id="accessionSelect" class="form-select" required>
                                <option value="">Select Accession</option>
                                @foreach($accessions as $acc)
                                    <option value="{{ $acc->id }}" {{ $selectedAccession == $acc->id ? 'selected' : '' }}>{{ $acc->accession_number }} — {{ $acc->accession_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-8 mt-2">
                            {{-- Accession Details Card --}}
                            <div id="accessionDetails" class="card border bg-light mb-0 d-none">
                                <div class="card-body py-2 px-3">
                                    <div class="row g-1 small">
                                        <div class="col-4"><span class="text-muted">Crop:</span> <span id="ad_crop">—</span></div>
                                        <div class="col-4"><span class="text-muted">Time:</span> <span id="ad_time">—</span></div>
                                        <div class="col-4"><span class="text-muted">Scientific:</span> <span id="ad_scientific">—</span></div>
                                        <div class="col-4"><span class="text-muted">Status:</span> <span id="ad_status">—</span></div>
                                        <div class="col-4"><span class="text-muted">Collected:</span> <span id="ad_collected">—</span></div>
                                        <div class="col-4"><span class="text-muted">Barcode:</span> <span id="ad_barcode">—</span></div>
                                        <div class="col-4"><span class="text-muted">Expiry Date:</span> <span id="ad_expiryDate">—</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 4: Lot Details ── --}}
                        <div class="col-12 mt-3"><h6 class="text-muted border-bottom pb-1">Lot Details</h6></div>

                        <div class="col-md-3 mt-2">
                            <label class="form-label">Section (Category/Zone)<span class="text-danger">*</span></label>
                            <select name="section_id" id="sectionSelect" class="form-select" required>
                                <option value="">Select Section</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" {{ $selectedSection == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-label">Shelf/Rack <span class="text-danger">*</span></label>
                            <select name="rack_id" id="rackSelect" class="form-select" required>
                                <option value="">Select Rack</option>
                                @foreach ($racks as $rack)
                                    <option value="{{ $rack->id }}" data-section="{{ $rack->section_id }}" {{ $selectedRack == $rack->id ? 'selected' : '' }}>
                                        {{ $rack->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <label class="form-label">Bin (Compartment)<span class="text-danger">*</span></label>
                            <select name="bin_id" id="binSelect" class="form-select" required>
                                    <option value="">Select Bin</option>
                                    @foreach ($bins as $bin)
                                        <option value="{{ $bin->id }}" 
                                                data-section="{{ $bin->section_id }}" 
                                                data-rack="{{ $bin->rack_id }}" {{ $selectedBin == $bin->id ? 'selected' : '' }}>
                                        {{ $bin->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mt-2">
                            <label class="form-label">Container (Actual seed unit - box/tray)<span class="text-danger">*</span></label>
                            <select name="container_id" id="containerSelect" class="form-select" required>
                                <option value="">Select Container</option>
                                @foreach ($containers as $container)
                                    <option value="{{ $container->id }}" 
                                            data-section="{{ $container->section_id }}" 
                                            data-rack="{{ $container->rack_id }}" 
                                            data-bin="{{ $container->bin_id }}" {{ $selectedContainer == $container->id ? 'selected' : '' }}>
                                        {{ $container->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @php
                        if (old('quantity') !== null) {
                            // Validation error
                            $qtyRows = collect(old('quantity'))->map(function ($val, $i) {
                                return (object)[
                                    'quantity' => $val,
                                    'unit_id' => old('unit_id.' . $i),
                                    'reference_number' => old('reference_number.' . $i),
                                    'number_of_seeds' => old('number_of_seeds.' . $i),
                                    'per_seed_weight' => old('per_seed_weight.' . $i),
                                ];
                            });
                        } elseif(isset($lot) && $lot->seedQuantities->count()) {
                            // ✅ EDIT MODE (THIS WAS MISSING)
                            $qtyRows = $lot->seedQuantities;
                        } else {
                            // CREATE MODE
                            $qtyRows = collect([(object)[]]);
                        }
                        @endphp

                        <div class="col-md-12">
                        <!-- 4. Seed / Material Quantity -->
                        <div class="card mb-3">
                            <div class="card-header bg-light text-white">
                                <h5 class="mb-0"><i class="ri-scales-line me-2"></i>Seed / Material Quantity</h5>
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
                                                <th>Per Seed Weight (avg)</th>
                                                <th>Quantity </th>
                                                <th>Unit</th>
                                                <th>Show User %</th>
                                                <th>Min Stock Balance</th>
                                                <th>Available for User</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBodyQuantity">
@foreach ($qtyRows as $index => $row)
<tr>

    <td>
        <input type="text" name="reference_number[]" class="form-control refNumber" placeholder="Enter Reference Number"
            value="{{ $row->reference_number ?? '' }}" {{ isset($lot) ? 'readonly style=background-color:#e9ecef;' : '' }}>
    </td>

    <td>
        <input type="number" name="number_of_seeds[]" class="form-control" placeholder="e.g. 100"
            value="{{ $row->number_of_seeds ?? '' }}">
    </td>

    <td>
        <input type="number" step="0.001" name="per_seed_weight[]" class="form-control" placeholder="e.g. 0.5"
            value="{{ $row->per_seed_weight ?? '' }}">
    </td>

    <td>
        <input type="number" step="0.01" name="quantity[]" class="form-control quantity" placeholder="e.g. 1000"
            value="{{ $row->quantity ?? '' }}" required>
    </td>

    <td>
        <select name="unit_id[]" class="form-select">
            <option value="">Unit</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}"
                    {{ ($row->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->name }} ({{ $unit->code }})
                </option>
            @endforeach
        </select>
    </td>

    <td>
        <select class="form-select percent">
            <option value="">%</option>
            @foreach([10,20,30,40,50,60,70,80,90,100] as $p)
                <option value="{{ $p }}">{{ $p }}</option>
            @endforeach
        </select>
    </td>

    <td>
        <input type="text" name="min_quantity[]" class="form-control min" placeholder="Auto calculated"
            value="{{ $row->min_quantity ?? '' }}" readonly>
    </td>

    <td>
        <input type="text" name="quantity_show[]" class="form-control userQty" placeholder="Auto calculated"
            value="{{ $row->quantity_show ?? '' }}" readonly>
    </td>

    <td>
        <button type="button" class="btn btn-success btn-sm addRowQ">+</button>
        <button type="button" class="btn btn-danger btn-sm removeRowQ">-</button>
    </td>

</tr>
@endforeach
</tbody>
                                    </table>
                                @error('reference_number.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <div class="mt-2 text-muted">
                                    Last Reference No.: <strong>{{ $lastRef ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                        </div>

                        @php
                        if (old('germination_percentage') !== null) {
                            // Validation error → old input
                            $rows = collect(old('germination_percentage'))->map(function ($val, $i) {
                                return (object)[
                                    'germination_percentage' => $val,
                                    'moisture_content' => old('moisture_content.' . $i),
                                    'purity_percentage' => old('purity_percentage.' . $i),
                                    'viability_test_date' => old('viability_test_date.' . $i),
                                    'seed_health_status' => old('seed_health_status.' . $i),
                                    'researcher_id' => old('researcher_id.' . $i),
                                    'researcher_other' => old('researcher_other.' . $i),
                                    'research_date' => old('research_date.' . $i),
                                ];
                            });
                        } elseif(isset($lot) && $lot->seedQualities->count()) {
                            // ✅ EDIT MODE
                            $rows = $lot->seedQualities;
                        } else {
                            // CREATE MODE
                            $rows = collect([(object)[]]);
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
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </div>
                        <div class="col-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="text" id="expiry_input" class="form-control" value="" readonly>
                        </div>
                        <div class="col-9">
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
                            <div class="col-md-3"><span class="text-muted d-block">Lot Master</span><strong id="vl_lot_master"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Lot Type</span><strong id="vl_lot_type"></strong></div>
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
                            <div class="col-md-3"><span class="text-muted d-block">Warehouse</span><strong id="vl_acc_warehouse"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Status</span><strong id="vl_acc_status"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Barcode</span><strong id="vl_acc_barcode"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Expiry Date</span><strong id="vl_acc_expiry"></strong></div>
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
                        </div>
                    </div>
                </div>

                {{-- Storage Details --}}
                <div class="card mb-0">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-archive-line me-1"></i>Storage Details</strong></div>
                    <div class="card-body">
                        <div class="row g-3 small" id="vl_storage_section">
                            <div class="col-md-3"><span class="text-muted d-block">Storage Name</span><strong id="vl_st_name"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Storage ID</span><strong id="vl_st_id"></strong></div>
                            <div class="col-md-3"><span class="text-muted d-block">Warehouse</span><strong id="vl_st_warehouse"></strong></div>
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

    //const $ = (id) => document.getElementById(id);

    // ── Edit Lot ──────────────────────────────────────────────────────────
    document.querySelectorAll('.editLotBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('edit_accession_id').value  = d.accession_id  || '';
            document.getElementById('edit_storage_id').value    = d.storage_id    || '';
            document.getElementById('edit_expiry').value        = d.expiry        || '';
            document.getElementById('edit_germination').value   = d.germination   || '';
            document.getElementById('edit_moisture').value      = d.moisture      || '';
            document.getElementById('edit_purity').value        = d.purity        || '';
            document.getElementById('edit_status').value        = d.status        || 'active';
            document.getElementById('edit_description').value   = d.description   || '';
            document.getElementById('editLotForm').action = `/lot-management/${d.id}`;
            document.getElementById('formMethod').value = 'PUT';
        });
    });
    let laddBtn = document.getElementById('addLotBtn');

    if (laddBtn) {
        laddBtn.addEventListener('click', function () {
            document.getElementById('editLotForm').action = `/lot-management`;
            document.getElementById('formMethod').value = 'POST';
        });
    }

    // ── Accession select → load details ──────────────────────────────────
    document.getElementById('accessionSelect').addEventListener('change', function () {
        const id = this.value;
        const box = document.getElementById('accessionDetails');
        _accessionData = null;

        // clear auto-filled fields
        //document.getElementById('expiryDate').value     = '';

        if (!id) { box.classList.add('d-none'); updateBalance(); return; }

        fetch(`/lot-management/accession/${id}`)
            .then(r => r.json())
            .then(d => {
                _accessionData = d;
                document.getElementById('ad_crop').textContent       = d.crop             || '—';
                document.getElementById('ad_time').textContent       = d.storage_time    || '—';
                document.getElementById('ad_scientific').textContent = d.scientific_name  || '—';
                document.getElementById('ad_status').textContent     = d.status           || '—';
                document.getElementById('ad_collected').textContent  = d.collection_date  || '—';
                document.getElementById('ad_barcode').textContent    = d.barcode          || '—';
                document.getElementById('ad_expiryDate').textContent     = d.expiry_date     || '-';
                document.getElementById('expiry_input').value = d.expiry_date || '';
                box.classList.remove('d-none');

                updateBalance();
            })
            .catch(() => box.classList.add('d-none'));
    });

    // ── Storage select → load details ────────────────────────────────────
    let _storageData    = null;
    let _accessionData  = null;

    function updateBalance() {
        const balEl = document.getElementById('sd_balance');
        if (!_storageData || !balEl) return;

        let totalQty = 0;

        document.querySelectorAll('.quantity').forEach(el => {
            totalQty += parseFloat(el.value || 0);
        });

        const balance = (_storageData.available || 0) - totalQty;

        balEl.textContent = `${balance.toFixed(2)} ${_storageData.unit || ''}`;
        balEl.className   = 'ms-1 ' + (balance < 0 ? 'text-danger' : 'text-success');
    }

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity')) {
            updateBalance();
        }
    });

   document.getElementById('storageSelect').addEventListener('change', function () {
        const id  = this.value;
        const box = document.getElementById('storageDetails');
        const accessionSelect = document.getElementById('accessionSelect');

        _storageData = null;

        // Reset accession dropdown
        accessionSelect.innerHTML = '<option value="">Select Accession</option>';

        if (!id) {
            box.classList.add('d-none');
            updateBalance();
            return;
        }

        // 1️⃣ Fetch storage details
        fetch(`/lot-management/storage/${id}`)
            .then(r => r.json())
            .then(d => {
                _storageData = d;

                document.getElementById('sd_warehouse').textContent  = d.warehouse         || '—';
                document.getElementById('sd_type').textContent       = d.storage_type      || '—';
                document.getElementById('sd_condition').textContent  = d.storage_condition || '—';
                document.getElementById('sd_time').textContent       = d.storage_time      || '—';
                document.getElementById('sd_capacity').textContent   = d.capacity  ? `${d.capacity} ${d.unit || ''}`  : '—';
                document.getElementById('sd_available').textContent  = d.available ? `${d.available} ${d.unit || ''}` : '—';
                document.getElementById('sd_temp').textContent       = d.temperature ? `${d.temperature} °C` : '—';
                document.getElementById('sd_humidity').textContent   = d.humidity   ? `${d.humidity} %`      : '—';

                box.classList.remove('d-none');
                updateBalance();

                // 2️⃣ Fetch filtered accessions (🔥 NEW)
                return fetch(`/lot-management/accessions-by-storage/${id}`);
            })
            .then(r => r.json())
            .then(accessions => {
                accessions.forEach(a => {
                    let option = document.createElement('option');
                    option.value = a.id;
                    option.textContent = a.accession_number;
                    accessionSelect.appendChild(option);
                });
            })
            .catch(() => {
                box.classList.add('d-none');
            });
    });

    

    // Section change
    $('#sectionSelect').on('change', function () {
        let sectionId = $(this).val();

        $('#rackSelect option').show();
        $('#binSelect option').show();
        //$('#containerSelect option').show();

        if (sectionId) {
            $('#rackSelect option').each(function () {
                if ($(this).data('section') != sectionId && $(this).val() !== "") {
                    $(this).hide();
                }
            });

            $('#binSelect option').each(function () {
                if ($(this).data('section') != sectionId && $(this).val() !== "") {
                    $(this).hide();
                }
            });

            
        }
    });


    // Rack change
    $('#rackSelect').on('change', function () {
        let rackId = $(this).val();

        $('#binSelect option').show();
        //$('#containerSelect option').show();

        if (rackId) {
            $('#binSelect option').each(function () {
                if ($(this).data('rack') != rackId && $(this).val() !== "") {
                    $(this).hide();
                }
            });

            
        }
    });


    // Bin change → filter container + auto select parents
    $('#binSelect').on('change', function () {
        let selected = $(this).find('option:selected');

        let sectionId = selected.attr('data-section');
        let rackId = selected.attr('data-rack');

        // Debug (check in console)
        console.log('Section:', sectionId);
        console.log('Rack:', rackId);

        // Auto select parent
        if (sectionId) {
            $('#sectionSelect').val(sectionId).trigger('change');
        }

        if (rackId) {
            $('#rackSelect').val(rackId).trigger('change');
        }

        // Filter containers
       
    });


    // =========================
    // SEED QUANTITY (FIXED DUPLICATE)
    // =========================
    const quantityInput = document.getElementById('quantity');
    const percentSelect = document.getElementById('showUserPercent');
    const userField = document.getElementById('userQuantity');
    const minField = document.getElementById('minQuantity');

    function calculateUserQuantity() {
        let quantity = parseFloat(quantityInput.value) || 0;
        let percent  = parseFloat(percentSelect.value) || 0;

        let userQty = (quantity * percent) / 100;
        let minQty  = quantity - userQty;

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

            if (select.value === 'other') {
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
        if (select.value === 'other' || otherInput.value !== '') {
            select.value = 'other';
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
            let newRow = tableBodyQuantity.rows[0].cloneNode(true);

            // Clear values
            newRow.querySelectorAll('input').forEach(input => input.value = '');
            newRow.querySelectorAll('select').forEach(select => select.value = '');

            tableBodyQuantity.appendChild(newRow);
        }

        // REMOVE ROW
        if (e.target.classList.contains('removeRowQ')) {
            if (tableBodyQuantity.rows.length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('At least one row required');
            }
        }
    });

    // AUTO CALCULATION
    tableBodyQuantity.addEventListener('input', function(e) {
        let row = e.target.closest('tr');

        let qty = parseFloat(row.querySelector('.quantity').value) || 0;
        let percent = parseFloat(row.querySelector('.percent').value) || 0;

        let userQty = (qty * percent) / 100;
        let minQty = qty - userQty;

        row.querySelector('.userQty').value = userQty.toFixed(2);
        row.querySelector('.min').value = minQty.toFixed(2);
    });

    $(document).on('input', '.refNumber', function () {

        let values = [];
        let duplicateFound = false;

        $('.refNumber').each(function () {
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
        $('.refNumber').each(function () {
            let val = $(this).val().trim();
            if (values.filter(v => v === val).length === 1) {
                $(this).removeClass('is-invalid');
            }
        });

    });

    $('form').on('submit', function(e){

        let values = [];
        let hasDuplicate = false;

        $('.refNumber').each(function () {
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


});
</script>
@endpush
