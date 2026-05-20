@extends('layouts.app')

@section('content')
    <style>
        /* Required field styling */
        .form-label.required::after {
            content: ' *';
            color: #dc3545;
            font-weight: bold;
        }

        /* Validation error styling */
        .is-invalid {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            padding-right: calc(1.5em + 0.75rem);
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Alert styling */
        .alert {
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
        }

        .alert-danger ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }

        .alert-danger li {
            margin-bottom: 0.25rem;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                        {{ isset($accession) ? 'Edit Accession' : 'Add New Accession' }}
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Create a new germplasm
                        accession record</p>                  
                </div>
                <a href="{{ route('accession.accession-list') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>Back to List
                </a>
            </div>

            <form id="accessionForm"
                action="{{ isset($accession) ? route('accessions.update', $accession->id) : route('accessions.store') }}"
                method="POST" enctype="multipart/form-data" class="">
                @csrf
                @if (isset($accession))
                    @method('PUT')
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Error:</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong>Success:</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <!-- 1. Basic Accession Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light text-white">
                                <h5 class="mb-0"><i class="ri-information-line me-2"></i>1. Basic Accession Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">Source</label>

                                        <div class="d-flex gap-3 mb-2">
                                            <div class="form-check form-check-inline">


                                                <input class="form-check-input" type="radio" name="acc_source"
                                                    value="internal" id="sourceInternal" checked
                                                    {{ old('acc_source', $accession->acc_source ?? '') == 'internal' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sourceInternal">Internal</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="acc_source"
                                                    value="external" id="sourceExternal"
                                                    {{ old('ext_source', $accession->ext_source ?? '') == 'external' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sourceExternal">External</label>
                                            </div>
                                        </div>

                                        <!-- Dropdown -->
                                        <select name="ext_source" id="sourceSelect" class="form-select mb-2" style="display:none;">
                                            <option value="">Select Source</option>
                                            <option value="Invoice"
                                                {{ old('ext_source', $accession->ext_source ?? '') == 'Invoice' ? 'selected' : '' }}>
                                                Invoice</option>
                                            <option value="Receive"
                                                {{ old('ext_source', $accession->ext_source ?? '') == 'Receive' ? 'selected' : '' }}>Receive
                                            </option>
                                            <option value="Agreements"
                                                {{ old('ext_source', $accession->ext_source ?? '') == 'Agreements' ? 'selected' : '' }}>
                                                Agreements</option>
                                            <option value="Import License"
                                                {{ old('ext_source', $accession->ext_source ?? '') == 'Import License' ? 'selected' : '' }}>
                                                Import License</option>
                                        </select>
                                        <input type="file" name="source_document" id="sourceDocument" class="form-control" style="display:none;"
                                            accept=".pdf,.doc,.docx,.csv">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">Accession Show to Requester</label>

                                        <div class="d-flex gap-3 mb-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="requester_show"
                                                    value="yes" id="requesterYes"
                                                    {{ old('requester_show', $accession->requester_show ?? '') == 'yes' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="requesterYes">Yes</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="requester_show"
                                                    value="no" id="requesterNo"
                                                    {{ old('requester_show', $accession->requester_show ?? '') == 'no' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="requesterNo">No</label>
                                            </div>
                                        </div>


                                        @error('requester_show')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Year Of Arrival <span class="text-danger">*</span></label>
                                        <select name="year_of_arrival" class="form-select" required>
                                            <option value="">Select Year</option>

    @for($year = 1985; $year <= date('Y'); $year++)
        <option value="{{ $year }}">{{ $year }}</option>
    @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sample ID <span class="text-danger">*</span></label>
                                        <input type="text" name="sample_id" class="form-control"
                                            value="{{ old('sample_id', $accession->sample_id ?? '') }}"
                                            placeholder="Enter sample ID" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">Storage Time </label>
                                        <select name="storage_time_id" class="form-select" required>
                                            <option value="">Select Time</option>
                                            @foreach($storageTime as $time)
                                                <option value="{{ $time->id }}"
                                                    {{ old('storage_time_id', $accession->storage_time_id ?? '') == $time->id ? 'selected' : '' }}>
                                                    {{ $time->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 d-none">
                                        <label class="form-label required">Accession Number</label>
                                        <input type="text" name="accession_number"
                                            class="form-control"
                                            value="{{ $accession->accession_number ?? 'Auto Generate' }}"
                                            readonly>
                                        
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">Accession Id</label>
                                        <input type="text" name="accession_name"
                                            class="form-control @error('accession_name') is-invalid @enderror"
                                            value="{{ old('accession_name', $accession->accession_name ?? '') }}"
                                            placeholder="e.g., HD-2967 Punjab Collection" required>
                                            <small class="text-muted">Accession ID: Auto-generated (e.g., AG-2026-ACC-00001)</small>
                                        @error('accession_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">Crop Name</label>
                                        <select name="crop_id" id="crop_id"
                                            class="form-select @error('crop_id') is-invalid @enderror" required>
                                            <option value="">Select Crop</option>
                                            @foreach ($crops as $crop)
                                                <option value="{{ $crop->id }}"
                                                    data-regen="{{ $crop->regeneration_cut_year }}"
                                                    {{ old('crop_id', $accession->crop_id ?? '') == $crop->id ? 'selected' : '' }}>
                                                    {{ $crop->crop_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('crop_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                   
                                    <div class="col-md-12">
                                        <label class="form-label">Crop Basic Details</label>

                                        <ul class="list-unstyled mb-0 border p-2 rounded">

                                            <li class="w-50 float-start">
                                                <strong>Scientific Name:</strong>
                                                <span
                                                    id="scientificName">{{ old('scientific_name', $accession->scientific_name ?? '') }}</span>
                                            </li>

                                            <li class="w-50 float-start">
                                                <strong>Family:</strong>
                                                <span id="family">{{ old('family', $accession->family ?? '') }}</span>
                                            </li>

                                            <li class="w-50 float-start">
                                                <strong>Genus:</strong>
                                                <span id="genus">{{ old('genus', $accession->genus ?? '') }}
                                            </li>
                                            <li class="w-50 float-start">
                                                <strong>Category:</strong>
                                                <span id="category">{{ old('category', $accession->category ?? '') }}</span>
                                            </li>
                                            <li class="w-50 float-start">
                                                <strong>Crop Category:</strong>
                                                <span id="cropCategory">{{ old('crop_category', $accession->crop_category_id ?? '') }}</span>
                                            </li>
                                            <li>
                                                <strong>Type:</strong>
                                                <span id="type">{{ old('type', $accession->type ?? '') }}</span>
                                            </li>
                                            <li>
                                                <strong>Season:</strong>
                                                <span id="season">{{ old('season', $accession->season_id ?? '') }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <!-- 2. Collection Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light text-white">
                                <h5 class="mb-0"><i class="ri-map-pin-line me-2"></i>2. Collection Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Collection Number</label>
                                        <input type="text" name="collection_number"
                                            class="form-control @error('collection_number') is-invalid @enderror"
                                            value="{{ old('collection_number', $accession->collection_number ?? '') }}"
                                            placeholder="e.g., COL-2026-001">
                                        @error('collection_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Collector Name</label>
                                        <input type="text" name="collector_name"
                                            class="form-control @error('collector_name') is-invalid @enderror"
                                            value="{{ old('collector_name', $accession->collector_name ?? '') }}"
                                            placeholder="e.g., Dr. Sarah Johnson">
                                        @error('collector_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Collection Date</label>
                                        <input type="date" name="collection_date"
                                            class="form-control @error('collection_date') is-invalid @enderror"
                                            value="{{ old('collection_date', $accession->collection_date ?? '') }}" max="{{ date('Y-m-d') }}">
                                        @error('collection_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Donor Name / Source</label>
                                        <input type="text" name="donor_name"
                                            class="form-control @error('donor_name') is-invalid @enderror"
                                            value="{{ old('donor_name', $accession->donor_name ?? '') }}"
                                            placeholder="e.g., Research Station Alpha">
                                        @error('donor_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Collection Site Name</label>
                                        <input type="text" name="collection_site"
                                            class="form-control @error('collection_site') is-invalid @enderror"
                                            value="{{ old('collection_site', $accession->collection_site ?? '') }}"
                                            placeholder="e.g., Punjab Agricultural Research Farm">
                                        @error('collection_site')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Country</label>
                                        <select name="country_id" id="country" class="form-select">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('country_id', $accession->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->country_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">State</label>
                                        <select name="state_id" id="state" class="form-select">
                                            <option value="">Select State</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}"
                                                    {{ old('state_id', $accession->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                                    {{ $state->state_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">District</label>
                                        <select name="district_id" id="district" class="form-select">
                                            <option value="">Select District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}"
                                                    {{ old('district_id', $accession->district_id ?? '') == $district->id ? 'selected' : '' }}>
                                                    {{ $district->district_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">City/Village</label>
                                        <select name="city_id" id="city" class="form-select">
                                            <option value="">Select City/Village</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" data-lat="{{ $city->latitude }}"
                                                    data-lng="{{ $city->longitude }}"
                                                    {{ old('city_id', $accession->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                                    {{ $city->city_village_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-4">
                                        <label class="form-label">Latitude</label>
                                        <input type="text" name="latitude" class="form-control"
                                            placeholder="e.g., 20.43433"
                                            value="{{ old('latitude', $accession->latitude ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Longitude</label>
                                        <input type="text" name="longitude" class="form-control"
                                            placeholder="e.g., 81.12158"
                                            value="{{ old('longitude', $accession->longitude ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Pincode</label>
                                        <input type="text" name="pincode" class="form-control"
                                            value="{{ old('pincode', $accession->pincode ?? '') }}"
                                            placeholder="e.g., 416003">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- 3. Biological / Genetic Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light text-white">
                                <h5 class="mb-0"><i class="ri-dna-line me-2"></i>3. Biological / Genetic Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Biological Status</label>
                                        <select name="biological_status" class="form-select">
                                            <option value="">Select Status</option>
                                            <option value="Wild"
                                                {{ old('biological_status', $accession->biological_status ?? '') == 'Wild' ? 'selected' : '' }}>
                                                Wild</option>
                                            <option value="Landrace"
                                                {{ old('biological_status', $accession->biological_status ?? '') == 'Landrace' ? 'selected' : '' }}>
                                                Landrace</option>
                                            <option value="Breeding Material"
                                                {{ old('biological_status', $accession->biological_status ?? '') == 'Breeding Material' ? 'selected' : '' }}>
                                                Breeding Material</option>
                                            <option value="Improved Variety"
                                                {{ old('biological_status', $accession->biological_status ?? '') == 'Improved Variety' ? 'selected' : '' }}>
                                                Improved Variety</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Sample Type</label>
                                        <select name="sample_type" class="form-select">
                                            <option value="">Select Type</option>
                                            <option value="Seed"
                                                {{ old('sample_type', $accession->sample_type ?? '') == 'Seed' ? 'selected' : '' }}>
                                                Seed</option>
                                            <option value="Plant"
                                                {{ old('sample_type', $accession->sample_type ?? '') == 'Plant' ? 'selected' : '' }}>
                                                Plant</option>
                                            <option value="Tissue"
                                                {{ old('sample_type', $accession->sample_type ?? '') == 'Tissue' ? 'selected' : '' }}>
                                                Tissue</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Reproductive Type</label>
                                        <select name="reproductive_type" class="form-select">
                                            <option value="">Select Type</option>
                                            <option value="Self Pollinated"
                                                {{ old('reproductive_type', $accession->reproductive_type ?? '') == 'Self Pollinated' ? 'selected' : '' }}>
                                                Self Pollinated</option>
                                            <option value="Cross Pollinated"
                                                {{ old('reproductive_type', $accession->reproductive_type ?? '') == 'Cross Pollinated' ? 'selected' : '' }}>
                                                Cross Pollinated</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $passports = old('passport', isset($accession) ? $accession->passports->toArray() : []);
                        @endphp
                        <!-- 7. Passport Data File -->
                        <div class="card mb-3">
                            <div class="card-header bg-light text-white">
                                <h5 class="mb-0"><i class="ri-settings-line me-2"></i>5. Passport Data File</h5>
                            </div>
                            <div class="card-body">
                                <div class="col-md-6">
                                    <label class="form-label">Passport Data File</label>
                                    <input type="file" name="passport_file"
                                        class="form-control @error('passport_file') is-invalid @enderror"
                                        accept=".pdf,.doc,.docx">
                                    @error('passport_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Accepted: PDF, DOC, DOCX (Max 5MB)</small>
                                </div>
                                <div class="mb-3">
                                    <a href="{{ route('accessions.passport-template') }}" class="text-decoration-none">
                                        <i class="ri-download-line me-1"></i>Download sample CSV template
                                    </a>
                                </div>

                                <div class="text-center my-2 fw-bold d-none">OR</div>
                                <div class="col-md-12 d-none">
                                    <label class="form-label fw-bold">Passport Data</label>

                                    <table class="table table-bordered" id="passportTable">
                                        <thead>
                                            <tr>
                                               
                                                <th>Passport No.</th>
                                                 <th>In</th>
                                                <th>Out</th>
                                                <th>Date</th>
                                                <th>Remarks</th>
                                                <th width="50">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($passports))
                                                @foreach($passports as $index => $row)
                                            <tr>
                                                <td>
                                                    <input type="text" name="passport[{{ $index }}][passport_no]"
                                                        value="{{ $row['passport_no'] ?? '' }}" class="form-control">
                                                </td>

                                                <td>
                                                    <input type="text" name="passport[{{ $index }}][sample_name]"
                                                        value="{{ $row['sample_name'] ?? '' }}" class="form-control">
                                                </td>

                                                <td>
                                                    <input type="text" name="passport[{{ $index }}][sample_name_o]"
                                                        value="{{ $row['sample_name_o'] ?? '' }}" class="form-control">
                                                </td>

                                                <td>
                                                    <input type="date" name="passport[{{ $index }}][pass_date]"
                                                        value="{{ $row['pass_date'] ?? '' }}"
                                                        class="form-control" max="{{ date('Y-m-d') }}">
                                                </td>

                                                <td>
                                                    <input type="text" name="passport[{{ $index }}][remarks]"
                                                        value="{{ $row['remarks'] ?? '' }}" class="form-control">
                                                </td>

                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                                {{-- Default empty row (Create mode) --}}
                                                <tr>
                                                    <td><input type="text" name="passport[0][passport_no]" class="form-control"></td>
                                                    <td><input type="text" name="passport[0][sample_name]" class="form-control"></td>
                                                    <td><input type="text" name="passport[0][sample_name_o]" class="form-control"></td>
                                                    <td><input type="date" name="passport[0][pass_date]" class="form-control" max="{{ date('Y-m-d') }}"></td>
                                                    <td><input type="text" name="passport[0][remarks]" class="form-control"></td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>

                                    <button type="button" class="btn btn-success btn-sm" id="addRow">
                                        + Add More
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $accession = $accession ?? null;
                    @endphp
                    <div class="col-md-6 ">
                        
                        <!-- 6. Documentation -->
                        <div class="card mb-3">
                            <div class="card-header bg-light text-white">
                                <h5 class="mb-0"><i class="ri-file-text-line me-2"></i>4. Documentation</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">Barcode Type</label>
                                        <select name="barcode_type" id="barcodeType"
                                            class="form-select @error('barcode_type') is-invalid @enderror" required>
                                            <option value="">Select Barcode Type</option>
                                            <option value="auto" {{ old('barcode_type', optional($accession)->barcode_type) == 'auto' ? 'selected' : '' }}>
                                                Auto Generate</option>
                                            <option value="manual"
                                                {{ old('barcode_type', optional($accession)->barcode_type) == 'manual' ? 'selected' : '' }}>Manual Entry
                                            </option>
                                            <option value="existing"
                                                {{ old('barcode_type', optional($accession)->barcode_type) == 'existing' ? 'selected' : '' }}>Existing / Old
                                                Barcode</option>
                                            <option value="scan" {{ old('barcode_type', optional($accession)->barcode_type) == 'scan' ? 'selected' : '' }}>
                                                Scan Barcode</option>
                                            <option value="none" {{ old('barcode_type', optional($accession)->barcode_type) == 'none' ? 'selected' : '' }}>
                                                None</option>
                                        </select>
                                        @error('barcode_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Barcode Number</label>
                                        <div class="input-group">
                                            <input type="text" name="barcode" id="barcodeNumber"
                                                class="form-control @error('barcode') is-invalid @enderror"
                                                value="{{ old('barcode', $accession->barcode ?? '') }}"
                                                placeholder="ACC-2026-00125">
                                            <button type="button" class="btn btn-primary" id="generateBarcodeBtn">
                                                <i class="ri-qr-code-line me-1"></i>Generate
                                            </button>
                                            @error('barcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Format: ACC-YYYY-XXXXX</small>
                                    </div>

                                    <!-- Barcode/QR Code Preview and Print Section -->
                                    <div class="col-md-12" id="barcodePreviewSection" style="display: none;">
                                        <div class="card border-primary">
                                            <div
                                                class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">Barcode & QR Code Preview</h6>
                                                <button type="button" class="btn btn-sm btn-success"
                                                    id="printBarcodeBtn">
                                                    <i class="ri-printer-line me-1"></i>Print Label
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 text-center">
                                                        <h6 class="text-muted">Barcode</h6>
                                                        <div id="barcodeDisplay" class="mb-2"></div>
                                                        <p class="small text-muted" id="barcodeText"></p>
                                                    </div>
                                                    <div class="col-md-6 text-center">
                                                        <h6 class="text-muted">QR Code</h6>
                                                        <div id="qrcodeDisplay" class="d-inline-block mb-2"></div>
                                                        <p class="small text-muted" id="qrcodeText"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $images = old('images', $accession->images ?? []);
                                        if (is_string($images)) {
                                            $images = json_decode($images, true);
                                        }
                                    @endphp
                                    <div class="col-md-6">
                                        <label class="form-label required">Image Upload <small class="text-muted">(Max 5)</small></label>
                                        <input type="file" name="images[]" id="imageUpload" multiple accept="image/*"
                                            class="form-control @error('images') is-invalid @enderror">
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('images.*')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Max 5 images (JPG, PNG, GIF — max 2MB each)</small>
                                        <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-2">
                                            @if(!empty($images))
                                                @foreach($images as $img)
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/'.$img) }}" width="80" class="rounded border">
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Notes / Remarks</label>
                                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4"
                                            placeholder="Enter any additional notes, observations, or special handling instructions...">{{ old('notes', $accession->notes ?? '') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 9. System Fields -->
                        <div class="card mb-3">
                            <div class="card-header bg-light text-white">
                                <h5 class="mb-0"><i class="ri-settings-line me-2"></i>6. System Update</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label mb-0 mt-2">Entry Date: {{ date('d M Y') }}</label>
                                        <label class="form-label">Entered By: {{ auth()->user()->name }}</label>
                                        <input type="hidden" name="entered_by" value="{{ auth()->id() }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Regeneration Cut of Year <span class="text-danger">*</span></label> 
                                        <input type="number" id="regen_year" name="regen_year" class="form-control"
                                            value="{{ old('regen_year', $accession->regen_year ?? '') }}"
                                            placeholder="Enter number only" min="0.1" max="100" step="0.1">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label required">Status</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror"
                                            required>
                                            <option value="">Select Status</option>
                                            <option value="1" {{ old('status', $accession->status ?? '') == '1' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="0" {{ old('status', $accession->status ?? '') == '0' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>

                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                        <input type="date" id="expiry_date" name="expiry_date" class="form-control"
                                            value="{{ old('expiry_date', now()->addMonth(0)->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Next Regeneration Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" id="recheck_date" name="recheck_date" class="form-control"
                                            value="{{ old('recheck_date', $accession->recheck_date ?? '') }}" min="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- Form Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('accession.accession-list') }}" class="btn btn-secondary">
                                <i class="ri-close-line me-1"></i>Cancel
                            </a>
                            <button type="reset" class="btn btn-warning">
                                <i class="ri-refresh-line me-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i>
                                {{ isset($accession) ? 'Update Accession' : 'Save Accession' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- Barcode & QR Code Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <style>
        /* Print styles for barcode label */
        @media print {
            body * {
                visibility: hidden;
            }

            #printableLabel,
            #printableLabel * {
                visibility: visible;
            }

            #printableLabel {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
        }

        .printable-label {
            display: none;
            border: 2px solid #000;
            padding: 20px;
            max-width: 400px;
            margin: 20px auto;
            background: white;
        }

        .printable-label h4 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .printable-label .barcode-section,
        .printable-label .qrcode-section {
            text-align: center;
            margin: 15px 0;
        }

        .printable-label .info-section {
            margin-top: 15px;
            font-size: 12px;
        }

        .printable-label .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
    </style>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const $ = (id) => document.getElementById(id);

            // OLD VALUES (EDIT MODE)
            let countryId = "{{ old('country_id', $accession->country_id ?? '') }}";
            let stateId = "{{ old('state_id', $accession->state_id ?? '') }}";
            let districtId = "{{ old('district_id', $accession->district_id ?? '') }}";
            let cityId = "{{ old('city_id', $accession->city_id ?? '') }}";

            // =========================
            // LOCATION DROPDOWNS
            // =========================
            const country = $('country');
            const state = $('state');
            const district = $('district');
            const city = $('city');

            const latInput = document.querySelector('[name="latitude"]');
            const lngInput = document.querySelector('[name="longitude"]');
            const pinInput = document.querySelector('[name="pincode"]');

            // COUNTRY → STATE
            if (country) {
                country.addEventListener('change', function() {
                    if (!this.value || !state) return;

                    fetch(`/get-states/${this.value}`)
                        .then(res => res.json())
                        .then(data => {
                            state.innerHTML = '<option value="">Select State</option>';

                            data.forEach(i => {
                                state.innerHTML +=
                                    `<option value="${i.id}" ${stateId == i.id ? 'selected' : ''}>${i.state_name}</option>`;
                            });

                            if (stateId) state.dispatchEvent(new Event('change'));
                        });
                });

                if (countryId) {
                    country.value = countryId;
                    country.dispatchEvent(new Event('change'));
                }
            }

            // STATE → DISTRICT
            if (state) {
                state.addEventListener('change', function() {
                    if (!this.value || !district) return;

                    fetch(`/get-districts/${this.value}`)
                        .then(res => res.json())
                        .then(data => {
                            district.innerHTML = '<option value="">Select District</option>';

                            data.forEach(i => {
                                district.innerHTML += `
                            <option value="${i.id}" 
                                ${districtId == i.id ? 'selected' : ''}>
                                ${i.district_name}
                            </option>`;
                            });

                            if (districtId) district.dispatchEvent(new Event('change'));
                        });
                });
            }

            // DISTRICT → CITY
            if (district) {
                district.addEventListener('change', function() {
                    if (!this.value || !city) return;

                    fetch(`/get-cities/${this.value}`)
                        .then(res => res.json())
                        .then(data => {
                            city.innerHTML = '<option value="">Select City</option>';

                            data.forEach(i => {
                                city.innerHTML += `
                            <option value="${i.id}" 
                                data-lat="${i.latitude}" 
                                data-lng="${i.longitude}" 
                                data-pincode="${i.pincode}"
                                ${cityId == i.id ? 'selected' : ''}>
                                ${i.city_village_name}
                            </option>`;
                            });

                            if (cityId) {
                                city.dispatchEvent(new Event('change'));
                            }
                        });
                });
            }

            // CITY → LAT/LNG/PINCODE ✅ FIXED
            if (city) {
                city.addEventListener('change', function() {
                    let selected = this.options[this.selectedIndex];
                    if (!selected) return;

                    let lat = selected.getAttribute('data-lat');
                    let lng = selected.getAttribute('data-lng');
                    let pin = selected.getAttribute('data-pincode');

                    if (latInput) latInput.value = lat || '';
                    if (lngInput) lngInput.value = lng || '';
                    if (pinInput) pinInput.value = pin || '';
                });
            }

            // =========================
            // BARCODE SECTION
            // =========================
            const barcodeTypeSelect = $('barcodeType');
            const barcodeNumberInput = $('barcodeNumber');
            const generateBtn = $('generateBarcodeBtn');
            const previewSection = $('barcodePreviewSection');

            if (barcodeTypeSelect && barcodeNumberInput && generateBtn) {

                barcodeTypeSelect.addEventListener('change', function() {
                    const type = this.value;

                    barcodeNumberInput.value = '';
                    generateBtn.disabled = false;

                    if (type === 'auto' || type === 'none') {
                        barcodeNumberInput.readOnly = true;
                    } else {
                        barcodeNumberInput.readOnly = false;
                    }

                    if (type === 'none' && previewSection) {
                        previewSection.style.display = 'none';
                    }
                });

                generateBtn.addEventListener('click', function() {
                    let type = barcodeTypeSelect.value;
                    let value = barcodeNumberInput.value.trim();

                    if (type === 'auto') {
                        const year = new Date().getFullYear();
                        const random = Math.floor(Math.random() * 100000).toString().padStart(5, '0');
                        value = `ACC-${year}-${random}`;
                        barcodeNumberInput.value = value;
                    } else if (!value) {
                        return alert('Enter barcode');
                    }

                    generateBarcode(value);
                });

                function generateBarcode(value) {
                    if (!$('barcodeDisplay') || !$('qrcodeDisplay')) return;

                    $('barcodeDisplay').innerHTML = '<svg id="barcodeSvg"></svg>';
                    $('qrcodeDisplay').innerHTML = '';

                    JsBarcode("#barcodeSvg", value, {
                        height: 80
                    });

                    new QRCode($('qrcodeDisplay'), {
                        text: value,
                        width: 150,
                        height: 150
                    });

                    if (previewSection) previewSection.style.display = 'block';
                }
            }

            // =========================
            // SOURCE TOGGLE
            // =========================
            const internal = document.getElementById('sourceInternal');
            const external = document.getElementById('sourceExternal');
            const dropdown = document.getElementById('sourceSelect');
            const sourceDocument = document.getElementById('sourceDocument');

            function toggleSource() {
                if (!dropdown || !sourceDocument) return;

                if (external.checked) {
                    dropdown.style.display = 'block';
                    sourceDocument.style.display = 'block';
                } else {
                    dropdown.style.display = 'none';
                    sourceDocument.style.display = 'none';

                    // reset values (important)
                    dropdown.value = '';
                    sourceDocument.value = '';
                }
            }

            if (internal && external) {
                internal.addEventListener('change', toggleSource);
                external.addEventListener('change', toggleSource);
                toggleSource(); // run on load
            }

            // =========================
            // REQUESTER TOGGLE
            // =========================
            const yes = $('requesterYes');
            const no = $('requesterNo');
            const section = $('requesterSection');

            function toggleRequester() {
                if (!section) return;
                section.style.display = yes && yes.checked ? 'block' : 'none';
            }

            if (yes && no) {
                yes.addEventListener('change', toggleRequester);
                no.addEventListener('change', toggleRequester);
                toggleRequester();
            }



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
    const cropSelect = document.getElementById('crop_id');

    // Auto-fill Regeneration Cut of Year when crop is selected
    cropSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const regenYear = selectedOption.getAttribute('data-regen');
        regenYearInput.value = regenYear ?? '';
        // Note: calculateAllDates() is called by the jQuery AJAX handler
        // after the season is loaded, so we don't call it here.
    });

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

    cropSelect.addEventListener('change', function () {
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
    });

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
    window.calculateAllDates = function () {

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
                1
            );
        }
        

        regenInput.value = formatDate(regen);
    }

    



           


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

            const addBtn = document.getElementById('addRow');
            const tableBody = document.getElementById('passportTable');

            let rowIndex = tableBody.querySelectorAll('tr').length;

            addBtn.addEventListener('click', function () {

                let row = `
                <tr>
                    <td><input type="text" name="passport[${rowIndex}][passport_no]" class="form-control"></td>
                    <td><input type="text" name="passport[${rowIndex}][sample_name]" class="form-control"></td>
                    <td><input type="text" name="passport[${rowIndex}][sample_name_o]" class="form-control"></td>
                    <td><input type="date" name="passport[${rowIndex}][pass_date]" class="form-control"></td>
                    <td><input type="text" name="passport[${rowIndex}][remarks]" class="form-control"></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                    </td>
                </tr>`;

                tableBody.insertAdjacentHTML('beforeend', row);
                rowIndex++;
            });

            tableBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow')) {
                    e.target.closest('tr').remove();
                }
            });



          /*  document.querySelector('form').addEventListener('submit', function(e) {

                let crop = document.getElementById('crop_id').value;
                let variety = document.getElementById('variety_id').value;

                if (!crop || !variety) return;

                fetch(`/check-accession?crop_id=${crop}&variety_id=${variety}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.exists) {
                            e.preventDefault();
                            alert('This crop and variety already created.');
                        }
                    });

            });*/

            document.getElementById('backToAccession')?.addEventListener('click', function() {
                document.getElementById('accessionForm').style.display = 'block';
                document.getElementById('regenetionForm').style.display = 'none';
            });

            // ── Multiple image preview & max-5 validation ──────────────
            const imageUpload = document.getElementById('imageUpload');
            const imagePreview = document.getElementById('imagePreview');

            if (imageUpload) {
                imageUpload.addEventListener('change', function () {
                    imagePreview.innerHTML = '';
                    const files = Array.from(this.files);

                    if (files.length > 5) {
                        alert('Maximum 5 images allowed. Only the first 5 will be uploaded.');
                    }

                    files.slice(0, 5).forEach((file, i) => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const wrap = document.createElement('div');
                            wrap.style.cssText = 'position:relative;display:inline-block';
                            wrap.innerHTML = `
                                <img src="${e.target.result}" style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:2px solid ${i===0?'#0d6efd':'#dee2e6'}">
                                ${i===0?'<span style="position:absolute;top:2px;left:2px;background:#0d6efd;color:#fff;font-size:9px;padding:1px 4px;border-radius:3px">Primary</span>':''}
                            `;
                            imagePreview.appendChild(wrap);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }

        });
    </script>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        $('#crop_id').on('change', function() {

            let crop_id = $(this).val();

            /* ----------------------------
               LOAD VARIETIES
            -----------------------------*/

            //$('#variety_id').html('<option value="">Loading...</option>');

            if (crop_id) {

                /*$.ajax({
                    url: '/get-varieties/' + crop_id,
                    type: 'GET',
                    success: function(data) {

                        let options = '<option value="">Select Variety</option>';

                        data.forEach(function(variety) {
                            options +=
                                `<option value="${variety.id}">${variety.variety_name}</option>`;
                        });

                        $('#variety_id').html(options);

                    },
                    error: function() {
                        $('#variety_id').html(
                            '<option value="">Error loading varieties</option>');
                    }
                });*/


                /* ----------------------------
                   LOAD CROP DETAILS
                -----------------------------*/

                $.ajax({
                    url: '/get-crop-details/' + crop_id,
                    type: 'GET',
                    success: function(data) {
                        $('#scientificName').text(data.scientific_name ?? '-');
                        $('#family').text(data.family_name ?? '-');
                        $('#genus').text(data.genus ?? '-');
                        $('#category').text(data.category ? data.category.name : '-');
                        $('#cropCategory').text(data.crop_category ? data.crop_category.name : '-');
                        $('#type').text(data.crop_type ? data.crop_type.name : '-');
                        $('#season').text(data.season ? data.season.name : '-');
                        $('#regen_year').val(data.regeneration_cut_year ?? '');

                        // ✅ Update season for date calculation
                        if (data.season && data.season.start_month && data.season.end_month) {
                            window._cropSeason.start_month = parseInt(data.season.start_month);
                            window._cropSeason.end_month   = parseInt(data.season.end_month);
                        } else {
                            window._cropSeason.start_month = 0;
                            window._cropSeason.end_month   = 0;
                        }
                        console.log("Season updated:", window._cropSeason);

                        // Recalculate dates now that season + regen_year are both set
                        const regenYearEl = document.getElementById('regen_year');
                        if (regenYearEl && regenYearEl.value) {
                            calculateAllDates();
                        }                    },
                    error: function() {
                        $('#scientificName').text('-');
                        $('#family').text('-');
                        $('#genus').text('-');
                        $('#category').text('-');
                        $('#cropCategory').text('-');
                        $('#type').text('-');
                        $('#season').text('-');
                        window._cropSeason.start_month = 0;
                        window._cropSeason.end_month   = 0;
                    }
                });

            } else {
                $('#scientificName').text('-');
                $('#family').text('-');
                $('#genus').text('-');
                $('#category').text('-');
                $('#cropCategory').text('-');
                $('#type').text('-');
                window._cropSeason.start_month = 0;
                window._cropSeason.end_month   = 0;
            }

        });

    });
</script>
