@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                        Variety/Seed Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage
                        variety/seed master data</p>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <select id="cropFilter" class="form-select form-select-sm">
                        <option value="">All Crops</option>
                        @foreach ($crops as $crop)
                            <option value="{{ $crop->id }}">
                                {{ $crop->crop_name }}
                            </option>
                        @endforeach
                    </select>

                    <select id="varietyTypeFilter" class="form-select form-select-sm">
                        <option value="">All Variety Type</option>

                        @foreach ($varietyTypes as $type)
                            <option value="{{ $type->id }}">
                                {{ $type->name }}
                            </option>
                        @endforeach

                    </select>
                    <button style="min-width: 125px;" class="btn btn-sm btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#varietyModalimport" id="addVarietyBtnimport">
                        <i class="ri-upload-line me-1"></i>Import Varieties
                    </button>
                    <button style="min-width: 125px;" class="btn btn-sm btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#varietyModal" id="addVarietyBtn">
                        <i class="ri-add-line me-1"></i>New Variety
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($varieties->count())
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Crop</th>
                                        <th>Variety Type</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($varieties as $variety)
                                        <tr data-id="{{ $variety->id }}" data-crop="{{ $variety->crop_id }}"
                                            data-variety-type="{{ $variety->variety_type_id }}">
                                            <td class="fw-500">{{ $variety->variety_name }}</td>

                                            <td>
                                                @if ($variety->variety_code)
                                                    <span class="badge bg-info">{{ $variety->variety_code }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($variety->crop)
                                                    <span class="badge bg-secondary">{{ $variety->crop->crop_name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($variety->varietyType)
                                                    <span class="badge bg-primary">{{ $variety->varietyType->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $variety->description ?? '-' }}</td>
                                            <td>
                                                @if ($variety->variety_status == '1')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- VIEW BUTTON -->
                                                <button class="btn btn-sm btn-outline-info viewVarietyBtn"
                                                    data-name="{{ $variety->variety_name }}"
                                                    data-code="{{ $variety->variety_code }}"
                                                    data-crop="{{ $variety->crop->crop_name ?? '' }}"
                                                    data-type="{{ $variety->varietyType->name ?? '' }}"
                                                    data-breeder="{{ $variety->breeder_name }}"
                                                    data-release_year="{{ $variety->release_year }}"
                                                    data-description="{{ $variety->description }}"
                                                    data-source="{{ $variety->source }}"
                                                    data-country="{{ $variety->country->country_name ?? '' }}"
                                                    data-state="{{ $variety->state->state_name ?? '' }}"
                                                    data-district="{{ $variety->district->district_name ?? '' }}"
                                                    data-maturity="{{ $variety->maturity_duration }}"
                                                    data-height="{{ $variety->plant_height }}"
                                                    data-grain="{{ $variety->grain_type }}"
                                                    data-seed_color="{{ $variety->seed_color }}"
                                                    data-yield="{{ $variety->yield_potential }}"
                                                    data-germination="{{ $variety->germination_percent }}"
                                                    data-purity="{{ $variety->purity_percent }}"
                                                    data-moisture="{{ $variety->moisture_percent }}"
                                                    data-test_weight="{{ $variety->test_weight }}"
                                                    data-disease="{{ $variety->disease_resistance }}"
                                                    data-pest="{{ $variety->pest_resistance }}"
                                                    data-drought="{{ $variety->drought_tolerance }}"
                                                    data-isolation="{{ $variety->isolation_distance }}"
                                                    data-seed_class="{{ $variety->seedClass->name ?? '' }}"
                                                    data-region="{{ $variety->production_region }}"
                                                    data-storage_life="{{ $variety->storage_life }}"
                                                    data-variety_status="{{ $variety->variety_status }}">

                                                    <i class="ri-eye-line"></i>

                                                </button>

                                                <!-- EDIT -->
                                                <button class="btn btn-sm btn-outline-warning editVarietyBtn"
                                                    data-id="{{ $variety->id }}"
                                                    data-name="{{ $variety->variety_name }}"
                                                    data-code="{{ $variety->variety_code }}"
                                                    data-crop-id="{{ $variety->crop_id }}"
                                                    data-variety-type="{{ $variety->variety_type_id }}"
                                                    data-breeder="{{ $variety->breeder_name }}"
                                                    data-release-year="{{ $variety->release_year }}"
                                                    data-description="{{ $variety->description }}"
                                                    data-release-authority="{{ $variety->release_authority }}"
                                                    data-source="{{ $variety->source }}"
                                                    data-country="{{ $variety->country_id }}"
                                                    data-state="{{ $variety->state_id }}"
                                                    data-district="{{ $variety->district_id }}"
                                                    data-maturity="{{ $variety->maturity_duration }}"
                                                    data-height="{{ $variety->plant_height }}"
                                                    data-grain="{{ $variety->grain_type }}"
                                                    data-seed-color="{{ $variety->seed_color }}"
                                                    data-yield="{{ $variety->yield_potential }}"
                                                    data-germination="{{ $variety->germination_percent }}"
                                                    data-purity="{{ $variety->purity_percent }}"
                                                    data-moisture="{{ $variety->moisture_percent }}"
                                                    data-test-weight="{{ $variety->test_weight }}"
                                                    data-disease="{{ $variety->disease_resistance }}"
                                                    data-pest="{{ $variety->pest_resistance }}"
                                                    data-drought="{{ $variety->drought_tolerance }}"
                                                    data-flood="{{ $variety->flood_tolerance }}"
                                                    data-salinity="{{ $variety->salinity_tolerance }}"
                                                    data-isolation="{{ $variety->isolation_distance }}"
                                                    data-seed-class="{{ $variety->seed_class_id }}"
                                                    data-region="{{ $variety->production_region }}"
                                                    data-storage-life="{{ $variety->storage_life }}"
                                                    data-variety-status="{{ $variety->variety_status }}">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <form action="{{ route('varieties.destroy', $variety) }}" method="POST"
                                                    class="d-inline d-none" onsubmit="return confirm('Delete this variety?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>{{ $variety->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <p class="text-slate-500">No varieties found. <a href="#" data-bs-toggle="modal"
                                data-bs-target="#varietyModal">Create one</a></p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Edit button click
             document.querySelectorAll('.editVarietyBtn').forEach(btn => {

        btn.addEventListener('click', function () {
            
            let set = (name, val) => {
                let el = document.querySelector('[name="' + name + '"]');
                if (el) el.value = val ?? '';
            }
            

            // Basic
            set('variety_name', this.dataset.name);
            set('variety_code', this.dataset.code);
            set('breeder_name', this.dataset.breeder);
            set('release_year', this.dataset.releaseYear);
            set('description', this.dataset.description);
            set('release_authority', this.dataset.releaseAuthority);
            set('variety_status', this.dataset.varietyStatus);

            // IMPORTANT FIX (SELECTS)
            let cropSelect = document.querySelector('[name="crop_id"]');

if (cropSelect) {
    let cropId = this.dataset.cropId;

    // Force select option
    Array.from(cropSelect.options).forEach(option => {
        option.selected = (option.value == cropId);
    });

    // Trigger change (important for Bootstrap sometimes)
    cropSelect.dispatchEvent(new Event('change'));
}

            let typeSelect = document.querySelector('[name="variety_type_id"]');
            if (typeSelect) typeSelect.value = this.dataset.varietyType;

            let seedClass = document.querySelector('[name="seed_class_id"]');
            if (seedClass) seedClass.value = this.dataset.seedClass;

            // Identification
            set('source', this.dataset.source);
            set('country_id', this.dataset.country);
            set('state_id', this.dataset.state);
            set('district_id', this.dataset.district);

            // Agronomy
            set('maturity_duration', this.dataset.maturity);
            set('plant_height', this.dataset.height);
            set('grain_type', this.dataset.grain);
            set('seed_color', this.dataset.seedColor);
            set('yield_potential', this.dataset.yield);

            // Quality
            set('germination_percent', this.dataset.germination);
            set('purity_percent', this.dataset.purity);
            set('moisture_percent', this.dataset.moisture);
            set('test_weight', this.dataset.testWeight);

            // Resistance
            set('disease_resistance', this.dataset.disease);
            set('pest_resistance', this.dataset.pest);
            set('drought_tolerance', this.dataset.drought);
            set('flood_tolerance', this.dataset.flood);
            set('salinity_tolerance', this.dataset.salinity);

            // Seed
            set('isolation_distance', this.dataset.isolation);
            set('production_region', this.dataset.region);
            set('storage_life', this.dataset.storageLife);

            // Form Action
            const id = this.dataset.id;
            const form = document.getElementById('varietyForm');

            form.action = `/varieties/${id}`;

            let existing = form.querySelector('input[name="_method"]');
            if (existing) existing.remove();

            let hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = '_method';
            hidden.value = 'PUT';
            form.appendChild(hidden);

            document.getElementById('varietyModalLabel').innerText = "Edit Variety";

            new bootstrap.Modal(document.getElementById('varietyModal')).show();
        });

    });

            // Handle Add button click - reset form
            document.getElementById('addVarietyBtn').addEventListener('click', function() {
                document.getElementById('varietyForm').reset();
                document.getElementById('varietyForm').action = "{{ route('varieties.store') }}";
                document.getElementById('varietyModalLabel').textContent = 'Add Variety';
                document.getElementById('submitBtn').textContent = 'Add Variety';
                const existing = document.getElementById('varietyForm').querySelector(
                    'input[name="_method"]');
                if (existing) existing.remove();
            });
        });

        document.querySelectorAll('.viewVarietyBtn').forEach(btn => {

            btn.addEventListener('click', function() {

                let set = (id, val) => {
                    let el = document.getElementById(id);
                    if (el) el.innerText = val ?? '-';
                }

                set('v_name', this.dataset.name);
                set('v_code', this.dataset.code);
                set('v_crop', this.dataset.crop);
                set('v_type', this.dataset.type);
                set('v_breeder', this.dataset.breeder);
                set('v_release_year', this.dataset.release_year);
                set('v_description', this.dataset.description);

                set('v_source', this.dataset.source);
                set('v_accession', this.dataset.accession);
                set('v_country', this.dataset.country);
                set('v_state', this.dataset.state);
                set('v_district', this.dataset.district);

                set('v_maturity', this.dataset.maturity);
                set('v_height', this.dataset.height);
                set('v_grain', this.dataset.grain);
                set('v_seed_color', this.dataset.seed_color);
                set('v_yield', this.dataset.yield);

                set('v_germination', this.dataset.germination);
                set('v_purity', this.dataset.purity);
                set('v_moisture', this.dataset.moisture);
                set('v_test_weight', this.dataset.test_weight);

                set('v_disease', this.dataset.disease);
                set('v_pest', this.dataset.pest);
                set('v_drought', this.dataset.drought);

                set('v_isolation', this.dataset.isolation);
                set('v_seed_class', this.dataset.seed_class);
                set('v_region', this.dataset.region);

                set('v_variety_status', this.dataset.variety_status);

                new bootstrap.Modal(document.getElementById('viewVarietyModal')).show();

            });

        });


        $(document).ready(function() {

            function filterTable() {

                let crop = $('#cropFilter').val();
                let type = $('#varietyTypeFilter').val();

                $('tbody tr').each(function() {

                    let rowCrop = $(this).data('crop');
                    let rowType = $(this).data('variety-type');

                    let cropMatch = !crop || rowCrop == crop;
                    let typeMatch = !type || rowType == type;

                    if (cropMatch && typeMatch) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }

                });

            }

            $('#cropFilter').on('change', filterTable);
            $('#varietyTypeFilter').on('change', filterTable);

        });
    </script>

@endsection

@section('modals')
    <!-- Variety Modal -->
    <div class="modal fade" id="varietyModal" tabindex="-1" role="dialog" aria-labelledby="varietyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="varietyModalLabel">Add Variety</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="varietyForm" method="POST" action="{{ route('varieties.store') }}">
                    @csrf

                    <div class="modal-body">

                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#basic">Basic</button>
                            </li>

                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#identification">Identification</button>
                            </li>

                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#agronomy">Agronomy</button>
                            </li>

                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#quality">Quality</button>
                            </li>

                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#resistance">Resistance</button>
                            </li>

                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#seed">Seed
                                    Production</button>
                            </li>
                        </ul>


                        <div class="tab-content pt-3">

                            <!-- BASIC -->
                            <div class="tab-pane fade show active" id="basic">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Crop <span class="text-danger">*</span></label>
                                        <select name="crop_id" class="form-select @error('crop_id') is-invalid @enderror"
                                            required>

                                            <option value="">Select Crop</option>

                                            @foreach ($crops as $crop)
                                                <option value="{{ $crop->id }}"
                                                    {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                                                    {{ $crop->crop_name }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('crop_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Variety Name <span class="text-danger">*</span></label>
                                        <input type="text" name="variety_name"
                                            class="form-control @error('variety_name') is-invalid @enderror"
                                            placeholder="Enter variety name" value="{{ old('variety_name') }}" required>
                                        @error('variety_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Variety Code</label>
                                        <input type="text" name="variety_code"
                                            class="form-control @error('variety_code') is-invalid @enderror"
                                            placeholder="e.g. VAR001" value="{{ old('variety_code') }}">
                                        @error('variety_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Variety Type <span class="text-danger">*</span></label>
                                        <select name="variety_type_id" class="form-select" required>
                                            <option value="">Select Variety Type</option>

                                            @foreach ($varietyTypes as $type)
                                                <option value="{{ $type->id }}">
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Breeder Name</label>
                                        <input type="text" name="breeder_name" class="form-control"
                                            placeholder="e.g. ICAR-IIWBR" value="{{ old('breeder_name') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Release Year</label>
                                        <input type="number" name="release_year" class="form-control"
                                            placeholder="e.g. 2022" value="{{ old('release_year') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Release Authority</label>
                                        <input type="text" name="release_authority" class="form-control"
                                            placeholder="e.g. Central Variety Release Committee"
                                            value="{{ old('release_authority') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="variety_status" class="form-select" required>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Description</label>
                                        <input type="text" name="description" class="form-control"
                                            placeholder="e.g. enter description"
                                            value="{{ old('description') }}">
                                    </div>

                                </div>
                            </div>


                            <!-- IDENTIFICATION -->
                            <div class="tab-pane fade" id="identification">

                                <div class="row">

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Source <span class="text-danger">*</span></label>
                                        <select name="source" class="form-select" required>
                                            <option value="">Select Source</option>
                                            <option>ICAR</option>
                                            <option>SAU</option>
                                            <option>Private Company</option>
                                            <option>Farmer Collection</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <select name="country_id" id="country" class="form-select">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">State</label>
                            <select name="state_id" id="state" class="form-select">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state_id', $state->id ?? '') == $state->id ? 'selected' : '' }}>{{ $state->state_name }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">District</label>
                            <select name="district_id" id="district" class="form-select">
                                <option value="">Select District</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id', $district->id ?? '') == $district->id ? 'selected' : '' }}>{{ $district->district_name }}</option>
                                @endforeach

                            </select>
                        </div>

                                </div>
                            </div>


                            <!-- AGRONOMY -->
                            <div class="tab-pane fade" id="agronomy">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Maturity Duration (Days)</label>
                                        <input type="number" name="maturity_duration" class="form-control"
                                            placeholder="e.g. 120" value="{{ old('maturity_duration') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Plant Height</label>
                                        <input type="text" name="plant_height" class="form-control"
                                            placeholder="e.g. 95 cm" value="{{ old('plant_height') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Grain Type / Seed Shape</label>
                                        <input type="text" name="grain_type" class="form-control"
                                            placeholder="e.g. Bold" value="{{ old('grain_type') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Seed Color</label>
                                        <input type="text" name="seed_color" class="form-control"
                                            placeholder="e.g. Amber" value="{{ old('seed_color') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Yield Potential (qtl/ha)</label>
                                        <input type="number" name="yield_potential" class="form-control"
                                            placeholder="e.g. 55" value="{{ old('yield_potential') }}">
                                    </div>

                                </div>
                            </div>


                            <!-- QUALITY -->
                            <div class="tab-pane fade" id="quality">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Germination %</label>
                                        <input type="number" name="germination_percent" class="form-control"
                                            placeholder="e.g. 85" value="{{ old('germination_percent') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Purity %</label>
                                        <input type="number" name="purity_percent" class="form-control"
                                            placeholder="e.g. 98" value="{{ old('purity_percent') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Moisture %</label>
                                        <input type="number" name="moisture_percent" class="form-control"
                                            placeholder="e.g. 12" value="{{ old('moisture_percent') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Test Weight</label>
                                        <input type="number" name="test_weight" class="form-control"
                                            placeholder="e.g. 42" value="{{ old('test_weight') }}">
                                    </div>

                                </div>
                            </div>


                            <!-- RESISTANCE -->
                            <div class="tab-pane fade" id="resistance">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Disease Resistance</label>
                                        <input type="text" name="disease_resistance" class="form-control"
                                            placeholder="e.g. Rust resistant" value="{{ old('disease_resistance') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pest Resistance</label>
                                        <input type="text" name="pest_resistance" class="form-control"
                                            placeholder="e.g. Aphid tolerant" value="{{ old('pest_resistance') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Drought Tolerance</label>
                                        <input type="text" name="drought_tolerance" class="form-control"
                                            placeholder="e.g. Moderate" value="{{ old('drought_tolerance') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Flood Tolerance</label>
                                        <input type="text" name="flood_tolerance" class="form-control"
                                            placeholder="e.g. Low" value="{{ old('flood_tolerance') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Salinity Tolerance</label>
                                        <input type="text" name="salinity_tolerance" class="form-control"
                                            placeholder="e.g. High" value="{{ old('salinity_tolerance') }}">
                                    </div>

                                </div>
                            </div>


                            <!-- SEED PRODUCTION -->
                            <div class="tab-pane fade" id="seed">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Isolation Distance</label>
                                        <input type="text" name="isolation_distance" class="form-control"
                                            placeholder="e.g. 200 m" value="{{ old('isolation_distance') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Seed Class <span class="text-danger">*</span></label>
                                        <select name="seed_class_id" class="form-select" required>

                                            <option value="">Select Seed Class</option>

                                            @foreach ($seedClasses as $class)
                                                <option value="{{ $class->id }}">
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Production Region</label>
                                        <input type="text" name="production_region" class="form-control"
                                            placeholder="e.g. North India" value="{{ old('production_region') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Storage Life (Months)</label>
                                        <input type="number" name="storage_life" class="form-control"
                                            placeholder="e.g. 12" value="{{ old('storage_life') }}">
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Variety</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="varietyModalimport">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Import Varieties</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('varieties.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Upload CSV / Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>

                        <p class="text-muted small">
                            Columns format: <b>crop_id, name, code, description</b>
                        </p>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <!--    view modal -->
    <div class="modal fade" id="viewVarietyModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Variety Details</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Tabs -->
                    <ul class="nav nav-tabs">

                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab"
                                data-bs-target="#view_basic">Basic</button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#view_identification">Identification</button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#view_agronomy">Agronomy</button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#view_quality">Quality</button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#view_resistance">Resistance</button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#view_seed">Seed
                                Production</button>
                        </li>

                    </ul>


                    <div class="tab-content pt-3">

                        <!-- BASIC -->
                        <div class="tab-pane fade show active" id="view_basic">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Variety Name:</th>
                                        <td><span id="v_name"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Variety Code:</th>
                                        <td><span id="v_code"></span></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 30%;">Status:</th>
                                        <td><span class="badge bg-success" id="v_variety_status"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Crop:</th>
                                        <td><span id="v_crop"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Variety Type:</th>
                                        <td><span id="v_type"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Breeder Name:</th>
                                        <td><span id="v_breeder"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Release Year:</th>
                                        <td><span id="v_release_year"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Description:</th>
                                        <td><span id="v_description"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <!-- IDENTIFICATION -->
                        <div class="tab-pane fade" id="view_identification">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Source:</th>
                                        <td><span id="v_source"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Country:</th>
                                        <td><span id="v_country"></span></td>
                                    </tr>
                                    <tr>
                                        <th>State:</th>
                                        <td><span id="v_state"></span></td>
                                    </tr>
                                    <tr>
                                        <th>District:</th>
                                        <td><span id="v_district"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <!-- AGRONOMY -->
                        <div class="tab-pane fade" id="view_agronomy">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Maturity Duration:</th>
                                        <td><span id="v_maturity"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Plant Height:</th>
                                        <td><span id="v_height"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Grain Type:</th>
                                        <td><span id="v_grain"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Seed Color:</th>
                                        <td><span id="v_seed_color"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Yield Potential:</th>
                                        <td><span id="v_yield"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <!-- QUALITY -->
                        <div class="tab-pane fade" id="view_quality">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Germination %:</th>
                                        <td><span id="v_germination"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Purity %:</th>
                                        <td><span id="v_purity"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Moisture %:</th>
                                        <td><span id="v_moisture"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Test Weight:</th>
                                        <td><span id="v_test_weight"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Yield Potential:</th>
                                        <td><span id="v_yield"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <!-- RESISTANCE -->
                        <div class="tab-pane fade" id="view_resistance">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Disease Resistance:</th>
                                        <td><span id="v_disease"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Pest Resistance:</th>
                                        <td><span id="v_pest"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Drought Tolerance:</th>
                                        <td><span id="v_drought"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <!-- SEED -->
                        <div class="tab-pane fade" id="view_seed">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Isolation Distance:</th>
                                        <td><span id="v_isolation"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Seed Class:</th>
                                        <td><span id="v_seed_class"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Production Region:</th>
                                        <td><span id="v_region"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Country → State
    document.getElementById('country').addEventListener('change', function () {
        let countryId = this.value;

        if (!countryId) {
            document.getElementById('state').innerHTML = '<option value="">Select State</option>';
            document.getElementById('district').innerHTML = '<option value="">Select District</option>';
            //document.getElementById('city').innerHTML = '<option value="">Select City</option>';
            return;
        }

        fetch(`/get-states/${countryId}`)
            .then(res => res.json())
            .then(data => {
                let state = document.getElementById('state');
                state.innerHTML = '<option value="">Select State</option>';

                data.forEach(item => {
                    state.innerHTML += `<option value="${item.id}">${item.state_name}</option>`;
                });

                document.getElementById('district').innerHTML = '<option value="">Select District</option>';
                //document.getElementById('city').innerHTML = '<option value="">Select City</option>';
            });
    });

    // State → District
    document.getElementById('state').addEventListener('change', function () {
        let stateId = this.value;

        if (!stateId) return;

        fetch(`/get-districts/${stateId}`)
            .then(res => res.json())
            .then(data => {
                let district = document.getElementById('district');
                district.innerHTML = '<option value="">Select District</option>';

                data.forEach(item => {
                    district.innerHTML += `<option value="${item.id}">${item.district_name}</option>`;
                });

                //document.getElementById('city').innerHTML = '<option value="">Select City</option>';
            });
    });

 });
 </script>   
@endsection