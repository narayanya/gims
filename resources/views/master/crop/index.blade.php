@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                        Crop Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage crop
                        master data</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="">
                        <select id="categoryFilter" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!--<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#cropModalimport">
                        <i class="ri-upload-line me-1"></i>Import Crops
                    </button>-->
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newCropRequestModal">
                        <i class="ri-add-line me-1"></i>New Crop
                    </button>
                    <a href="{{ route('cropRequests.index') }}" class="btn btn-sm btn-secondary ">Crop Request List</a>
                    <button class="btn btn-sm btn-primary d-none" data-bs-toggle="modal" data-bs-target="#cropModal"
                       id="addCropBtn">
                        <i class="ri-add-line me-1"></i>New Crop
                    </button>
                </div>
            </div>


            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($crops->count())
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Effective Date</th>
                                        <th>Scientific Name</th>
                                        <th>Crop Category </th>
                                        <th>Crop Type</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                        <th>Update Status</th>
                                        <th>Updated Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($crops as $crop)
                                        <tr data-id="{{ $crop->id }}" data-category="{{ $crop->category_id }}">
                                            <td class="fw-500">{{ $crop->crop_name }}</td>
                                            <td>
                                                @if ($crop->crop_code)
                                                    <span class="badge bg-info">{{ $crop->crop_code }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $crop->effective_date ? \Carbon\Carbon::parse($crop->effective_date)->format('d-m-Y') : '-' }}</td>
                                            <td>{{ $crop->scientific_name ?? '-' }}</td>
                                            <td>{{ $crop->cropCategory->name ?? '-' }}</td>
                                            <td>{{ $crop->cropType->name ?? '-' }}</td>
                                            <td>{{ $crop->description ?? '-' }}</td>
                                            <td>
                                                @if ($crop->is_active == '1')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- VIEW BUTTON -->
                                                <button class="btn btn-sm btn-outline-info viewCropBtn"
                                                    data-name="{{ $crop->crop_name }}" data-code="{{ $crop->crop_code }}"
                                                    data-is_active="{{ $crop->is_active }}"
                                                    data-scientific="{{ $crop->scientific_name }}"
                                                    data-common="{{ $crop->common_name }}"
                                                    data-category="{{ $crop->category->name ?? '' }}"
                                                    data-cropcategory="{{ $crop->cropCategory->name ?? '' }}"
                                                    data-type="{{ $crop->cropType->name ?? '' }}"
                                                    data-description="{{ $crop->description }}"

                                                    data-vertical_id="{{ $crop->vertical_id }}"
                                                    data-numeric_code="{{ $crop->numeric_code }}"
                                                    data-effective_date="{{ $crop->effective_date }}"
                                                    data-crop_flag="{{ $crop->crop_flag }}"
                                                    data-focus_code="{{ $crop->focus_code }}"
                                                    data-family="{{ $crop->family_name }}"
                                                    data-genus="{{ $crop->genus }}" data-species="{{ $crop->species }}"
                                                    data-season="{{ $crop->season->name ?? '' }}"
                                                    data-duration="{{ $crop->duration_days }}"
                                                    data-sowing="{{ $crop->sowing_time }}"
                                                    data-harvest="{{ $crop->harvest_time }}"
                                                    data-climate="{{ $crop->climate_requirement }}"
                                                    data-soil="{{ $crop->soilType->name ?? '' }}"
                                                    data-isolation="{{ $crop->isolation_distance }}"
                                                    data-yield="{{ $crop->expected_yield }}">

                                                    <i class="ri-eye-line"></i>

                                                </button>

                                                <button class="btn btn-sm btn-outline-warning editCropBtn"
                                                    data-id="{{ $crop->id }}" data-name="{{ $crop->crop_name }}"
                                                    data-code="{{ $crop->crop_code }}"
                                                    data-is_active="{{ $crop->is_active }}"
                                                    data-scientific="{{ $crop->scientific_name }}"
                                                    data-common="{{ $crop->common_name }}"
                                                    data-category="{{ $crop->category_id }}"
                                                    data-cropcategory="{{ $crop->crop_category_id }}"
                                                    data-type="{{ $crop->crop_type_id }}"
                                                    data-season="{{ $crop->season_id }}"
                                                    data-description="{{ $crop->description }}"

                                                    data-vertical_id="{{ $crop->vertical_id }}"
                                                    data-numeric_code="{{ $crop->numeric_code }}"
                                                    data-effective_date="{{ $crop->effective_date }}"
                                                    data-crop_flag="{{ $crop->crop_flag }}"
                                                    data-focus_code="{{ $crop->focus_code }}"
                                                    data-soil="{{ $crop->soil_type_id }}"
                                                    data-family="{{ $crop->family_name }}"
                                                    data-genus="{{ $crop->genus }}" data-species="{{ $crop->species }}"
                                                    data-season="{{ $crop->season->name ?? '' }}"
                                                    data-duration="{{ $crop->duration_days }}"
                                                    data-sowing="{{ $crop->sowing_time }}"
                                                    data-harvest="{{ $crop->harvest_time }}"
                                                    data-climate="{{ $crop->climate_requirement }}"
                                                    data-soil="{{ $crop->soilType->name ?? '' }}"
                                                    data-isolation="{{ $crop->isolation_distance }}"
                                                    data-yield="{{ $crop->expected_yield }}">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <form action="{{ route('crops.destroy', $crop) }}" method="POST"
                                                    class="d-inline d-none" onsubmit="return confirm('Delete this crop?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </td>
                                           <td>
                                                @if ($crop->update_status == '1')
                                                    <span class="badge bg-success">Activated</span>
                                                @else
                                                    <span class="badge bg-danger">Deactivate</span>
                                                @endif
                                            </td>
                                           <td>
                                                {{ $crop->updated_at ? \Carbon\Carbon::parse($crop->updated_at)->format('d-m-Y') : '-' }}
                                           </td>
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
                        <p class="text-slate-500">No crops found. <a href="#" data-bs-toggle="modal"
                                data-bs-target="#cropModal">Create one</a></p>
                    </div>
                </div>
            @endif
            <div class="mt-2">
                {{ $crops->links() }}
            </div>
        </div>
        
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));

            document.querySelectorAll('.editCropBtn').forEach(btn => {

                btn.addEventListener('click', function() {

                    let id = this.dataset.id;

                    document.getElementsByName('crop_name')[0].value = this.dataset.name;
                    document.getElementsByName('crop_code')[0].value = this.dataset.code || '';
                    document.getElementsByName('scientific_name')[0].value = this.dataset
                        .scientific || '';
                    document.getElementsByName('common_name')[0].value = this.dataset.common || '';

                    document.getElementsByName('is_active')[0].value = this.dataset.is_active;
                    document.getElementsByName('category_id')[0].value = this.dataset.category;
                    document.getElementsByName('crop_category_id')[0].value = this.dataset
                        .cropcategory;
                    document.getElementsByName('crop_type_id')[0].value = this.dataset.type;
                    document.getElementsByName('season_id')[0].value = this.dataset.season;

                    document.getElementsByName('description')[0].value = this.dataset.description ||
                        '';
                    document.getElementsByName('vertical_id')[0].value = this.dataset.vertical_id || '';
                    document.getElementsByName('numeric_code')[0].value = this.dataset.numeric_code || '';
                    document.getElementsByName('effective_date')[0].value = this.dataset.effective_date || '';
                    document.getElementsByName('crop_flag')[0].value = this.dataset.crop_flag || '';
                    document.getElementsByName('focus_code')[0].value = this.dataset.focus_code || '';
                    document.getElementsByName('soil_type_id')[0].value = this.dataset.soil;
                    document.getElementsByName('family_name')[0].value = this.dataset.family || '';
                    document.getElementsByName('genus')[0].value = this.dataset.genus || '';
                    document.getElementsByName('species')[0].value = this.dataset.species || '';
                    document.getElementsByName('duration_days')[0].value = this.dataset.duration ||
                        '';
                    document.getElementsByName('sowing_time')[0].value = this.dataset.sowing || '';
                    document.getElementsByName('harvest_time')[0].value = this.dataset.harvest ||
                    '';
                    document.getElementsByName('climate_requirement')[0].value = this.dataset
                        .climate || '';
                    document.getElementsByName('isolation_distance')[0].value = this.dataset
                        .isolation || '';
                    document.getElementsByName('expected_yield')[0].value = this.dataset.yield ||
                    '';
                    
                    document.getElementsByName('crop_name')[0].readOnly = true;
                    document.getElementsByName('crop_code')[0].readOnly = true;

                    document.getElementById('cropModalLabel').innerText = "Edit Crop";

                    let form = document.getElementById('cropForm');

                    form.action = "/crops/" + id;

                    let method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PUT';

                    let existing = form.querySelector('input[name="_method"]');
                    if (existing) existing.remove();

                    let status = this.dataset.update_status;

                    if (status !== undefined) {
                        document.querySelectorAll('input[name="update_status"]').forEach(r => {
                            r.checked = (r.value === status);
                        });
                    }

                    form.appendChild(method);

                    cropModal.show();

                });

            });


            document.getElementById('addCropBtn').addEventListener('click', function() {

                let form = document.getElementById('cropForm');

                form.reset();

                form.action = "{{ route('crops.store') }}";

                let existing = form.querySelector('input[name="_method"]');
                if (existing) existing.remove();

                document.getElementsByName('crop_name')[0].readOnly = false;
                document.getElementsByName('crop_code')[0].readOnly = false;
                document.getElementById('cropModalLabel').innerText = "Add Crop";

            });

        });


        document.querySelectorAll('.viewCropBtn').forEach(btn => {

            btn.addEventListener('click', function() {

                let set = (id, val) => {
                    let el = document.getElementById(id);
                    if (el) el.innerText = val ?? '-';
                }

                set('c_name', this.dataset.name);
                set('c_code', this.dataset.code);
                set('c_scientific', this.dataset.scientific);
                set('c_common', this.dataset.common);

                set('c_category', this.dataset.category);
                set('c_cropcategory', this.dataset.cropcategory);
                set('c_type', this.dataset.type);
                set('c_description', this.dataset.description);

                set('c_vertical_id', this.dataset.vertical_id);
                set('c_numeric_code', this.dataset.numeric_code);
                set('c_effective_date', this.dataset.effective_date);
                set('c_crop_flag', this.dataset.crop_flag);
                set('c_focus_code', this.dataset.focus_code);

                set('c_family', this.dataset.family);
                set('c_genus', this.dataset.genus);
                set('c_species', this.dataset.species);

                set('c_season', this.dataset.season);
                set('c_duration', this.dataset.duration);
                set('c_sowing', this.dataset.sowing);
                set('c_harvest', this.dataset.harvest);
                set('c_climate', this.dataset.climate);
                set('c_soil', this.dataset.soil);

                set('c_seedrate', this.dataset.seedrate);
                set('c_germination', this.dataset.germination);
                set('c_isolation', this.dataset.isolation);
                set('c_yield', this.dataset.yield);

                new bootstrap.Modal(document.getElementById('viewCropModal')).show();

            });

        });

        $(document).ready(function() {

            $('#categoryFilter').on('change', function() {

                let category = $(this).val();

                $('tbody tr').each(function() {

                    let rowCategory = $(this).data('category');

                    if (category === "" || rowCategory == category) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            });

        });
    </script>

@endsection


@section('modals')
    <!-- Crop Modal -->
    <div class="modal fade" id="newCropRequestModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content rounded-3 border-0 shadow-lg">

                <!-- Header -->
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        Add New Crop Request
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <form action="{{ route('crop.request.store') }}" method="POST">
                     @csrf
                <div class="modal-body pt-2">
                    <!-- Info Box -->
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-light border">
                        <div>
                            <h6 class="mb-1 fw-semibold">Submit New Crop Request</h6>
                            <p class="text-muted mb-0 small">
                                If the crop is not available in the system, you can request it to be added to the <b>Core Database</b>.
                            </p>
                        </div>
                    </div>
                    
                    <div class="row ">
                        <div class="col-md-6 mt-3">
                            <label class="form-label">Crop Name <span class="text-danger">*</span></label>
                            <input type="text" name="req_crop_name"
                                class="form-control"
                                placeholder="Enter crop name" required>
                            @error('req_crop_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="req_crop_code" class="form-control"
                                placeholder="e.g. CR001" value="" required>
                            @error('req_crop_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea  name="description" class="form-control" placeholder="Enter your decription" ></textarea>
                        </div>
                    </div>
                    <!-- Email Box -->
                    <div class="mt-4 p-3 rounded-3 border bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">Send request to</small>
                            <strong class="text-dark">corecrop@vspl.com</strong>
                        </div>

                        <div class="d-flex gap-2">
                            <!--<button class="btn btn-sm btn-outline-primary" onclick="copyEmail()">
                                <span class="material-symbols-outlined fs-6">content_copy</span>
                            </button>-->

                            <a href="mailto:corecrop@vspl.com" class="btn btn-sm btn-primary">
                                <span class="material-symbols-outlined fs-6">Mail</span>
                            </a>
                        </div>
                    </div>

                    <!-- Note -->
                    <p class="text-muted small mt-3 mb-0">
                        <span class="material-symbols-outlined align-middle fs-6">info</span>
                        After Approval may take some time after submission.
                    </p>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Submit Request
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cropModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="cropModalLabel">Add Crop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="cropForm" method="POST" action="{{ route('crops.store') }}">
                    @csrf

                    <div class="modal-body">

                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#basic">Basic</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#core">Core Details</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#classification">Classification</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#agronomy">Agronomy</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#seed">Seed
                                    Production</button>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <!-- ================= BASIC ================= -->
                            <div class="tab-pane fade show active" id="basic">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Crop Name <span class="text-danger">*</span></label>
                                        <input disabled type="text" name="crop_name"
                                            class="form-control @error('crop_name') is-invalid @enderror"
                                            placeholder="Enter crop name" value="{{ old('crop_name') }}" required>
                                        @error('crop_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Code <span class="text-danger">*</span></label>
                                        <input disabled type="text" name="crop_code" class="form-control"
                                            placeholder="e.g. CR001" value="{{ old('crop_code') }}" required>
                                        @error('crop_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Scientific Name</label>
                                        <input type="text" name="scientific_name" class="form-control"
                                            placeholder="e.g. Triticum aestivum" value="{{ old('scientific_name') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Common Name</label>
                                        <input type="text" name="common_name" class="form-control"
                                            placeholder="e.g. Wheat" value="{{ old('common_name') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                        <select name="category_id"
                                            class="form-select @error('category_id') is-invalid @enderror" required>

                                            <option value="">Select Category</option>

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Crop Category <span class="text-danger">*</span></label>
                                        <select name="crop_category_id"
                                            class="form-select @error('crop_category_id') is-invalid @enderror" required>

                                            <option value="">Select Crop Category</option>

                                            @foreach ($cropcategories as $cropcategory)
                                                <option value="{{ $cropcategory->id }}"
                                                    {{ old('crop_category_id') == $cropcategory->id ? 'selected' : '' }}>
                                                    {{ $cropcategory->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('crop_category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Crop Type <span class="text-danger">*</span></label>
                                        <select name="crop_type_id"
                                            class="form-select @error('crop_type_id') is-invalid @enderror" required>

                                            <option value="">Select Type</option>

                                            @foreach ($types as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('crop_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Crop Status <span class="text-danger">*</span></label>
                                        <select name="is_active"
                                            class="form-select @error('is_active') is-invalid @enderror" required>
                                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>


                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3" placeholder="Enter crop description">{{ old('description') }}</textarea>
                                    </div>

                                </div>
                            </div>

                            <!-- ================= Core ================= -->
                            <div class="tab-pane fade" id="core">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Vertical ID</label>
                                        <input readonly disabled type="text" name="vertical_id" class="form-control"
                                            placeholder="" value="{{ old('vertical_id') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Numeric Code</label>
                                        <input readonly disabled type="text" name="numeric_code" class="form-control"
                                            placeholder="" value="{{ old('numeric_code') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Effective Date</label>
                                        <input readonly disabled type="text" name="effective_date" class="form-control"
                                            placeholder="" value="{{ old('effective_date') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Crop Flag</label>
                                        <input readonly disabled type="text" name="crop_flag" class="form-control"
                                            placeholder="" value="{{ old('crop_flag') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Focus Code</label>
                                        <input readonly disabled type="text" name="focus_code" class="form-control"
                                            placeholder="" value="{{ old('focus_code') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- ================= CLASSIFICATION ================= -->
                            <div class="tab-pane fade" id="classification">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Family Name</label>
                                        <input type="text" name="family_name" class="form-control"
                                            placeholder="e.g. Poaceae" value="{{ old('family_name') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Genus</label>
                                        <input type="text" name="genus" class="form-control"
                                            placeholder="e.g. Triticum" value="{{ old('genus') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Species</label>
                                        <input type="text" name="species" class="form-control"
                                            placeholder="e.g. aestivum" value="{{ old('species') }}">
                                    </div>

                                </div>
                            </div>

                            <!-- ================= AGRONOMY ================= -->
                            <div class="tab-pane fade" id="agronomy">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Season <span class="text-danger">*</span></label>
                                        <select name="season_id"
                                            class="form-select @error('season_id') is-invalid @enderror" required>

                                            <option value="">Select Season</option>

                                            @foreach ($seasons as $season)
                                                <option value="{{ $season->id }}"
                                                    {{ old('season_id') == $season->id ? 'selected' : '' }}>
                                                    {{ $season->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Duration (Days to Maturity)</label>
                                        <input type="number" name="duration_days" class="form-control"
                                            placeholder="e.g. 120" value="{{ old('duration_days') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sowing Time</label>
                                        <input type="text" name="sowing_time" class="form-control"
                                            placeholder="e.g. June-July" value="{{ old('sowing_time') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Harvest Time</label>
                                        <input type="text" name="harvest_time" class="form-control"
                                            placeholder="e.g. October" value="{{ old('harvest_time') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Climate Requirement</label>
                                        <input type="text" name="climate_requirement" class="form-control"
                                            placeholder="e.g. Warm and humid" value="{{ old('climate_requirement') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Soil Type <span class="text-danger">*</span>
                                        </label>

                                        <select name="soil_type_id"
                                            class="form-select @error('soil_type_id') is-invalid @enderror" required>

                                            <option value="">Select Soil Type</option>

                                            @foreach ($soiltypes as $soil)
                                                <option value="{{ $soil->id }}"
                                                    {{ old('soil_type_id') == $soil->id ? 'selected' : '' }}>

                                                    {{ $soil->name }}

                                                </option>
                                            @endforeach

                                        </select>

                                        @error('soil_type_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                </div>
                            </div>

                            <!-- ================= SEED PRODUCTION ================= -->
                            <div class="tab-pane fade" id="seed">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Isolation Distance (meters)</label>
                                        <input type="number" name="isolation_distance" class="form-control"
                                            placeholder="e.g. 200" value="{{ old('isolation_distance') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Expected Yield (qtl/acre)</label>
                                        <input type="number" step="0.01" name="expected_yield" class="form-control"
                                            placeholder="e.g. 18" value="{{ old('expected_yield') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Regenaration Cut of Year</label>
                                        <input type="number" name="" class="form-control"
                                            placeholder="Enter year" value="">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Update Status</label>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="update_status" value="1"
                                                {{ old('update_status', $crop->update_status ?? '') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label">Yes</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="update_status" value="0"
                                                {{ old('update_status', $crop->update_status ?? '') == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Save Crop</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- View Modal -->

    <div class="modal fade" id="viewCropModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Crop Details</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <ul class="nav nav-tabs">

                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#v_basic">Basic</button>
                        </li>

                        <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#v_core">Core Details</button>
                            </li>

                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#v_classification">Classification</button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#v_agronomy">Agronomy</button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#v_seed">Seed
                                Production</button>
                        </li>

                    </ul>

                    <div class="tab-content pt-3">

                        <!-- BASIC -->
                        <div class="tab-pane fade show active" id="v_basic">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <!-- BASIC -->
                                    <tr>
                                        <th style="width: 30%;">Name:</th>
                                        <td><span id="c_name"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Code:</th>
                                        <td><span id="c_code"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Scientific:</th>
                                        <td><span id="c_scientific"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Common:</th>
                                        <td><span id="c_common"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Category:</th>
                                        <td><span id="c_category"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Crop Category:</th>
                                        <td><span id="c_cropcategory"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td><span id="c_type"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Description:</th>
                                        <td><span id="c_description"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- CLASSIFICATION -->
                        <div class="tab-pane fade" id="v_core">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <!-- BASIC -->
                                    <tr>
                                        <th style="width: 30%;">Vertical ID:</th>
                                        <td><span id="c_vertical_id"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Numeric Code:</th>
                                        <td><span id="c_numeric_code"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Effective Date:</th>
                                        <td><span id="c_effective_date"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Crop Flag:</th>
                                        <td><span id="c_crop_flag"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Focus Code:</th>
                                        <td><span id="c_focus_code"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- CLASSIFICATION -->
                        <div class="tab-pane fade" id="v_classification">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <!-- BASIC -->
                                    <tr>
                                        <th style="width: 30%;">Family:</th>
                                        <td><span id="c_family"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Genus:</th>
                                        <td><span id="c_genus"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Species:</th>
                                        <td><span id="c_species"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- AGRONOMY -->
                        <div class="tab-pane fade" id="v_agronomy">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Season:</th>
                                        <td><span id="c_season"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Duration:</th>
                                        <td><span id="c_duration"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Sowing Time:</th>
                                        <td><span id="c_sowing"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Harvest Time:</th>
                                        <td><span id="c_harvest"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Climate:</th>
                                        <td><span id="c_climate"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Soil Type:</th>
                                        <td><span id="c_soil"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- SEED -->
                        <div class="tab-pane fade" id="v_seed">
                            <table class="table table-bordered table-striped p-0">
                                <tbody>
                                    <tr>
                                        <th>Germination %:</th>
                                        <td><span id="c_germination"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Isolation Distance:</th>
                                        <td><span id="c_isolation"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Expected Yield:</th>
                                        <td><span id="c_yield"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Import Modal -->

    <div class="modal fade" id="cropModalimport">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Import Crops</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('crops.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Upload CSV / Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>

                        <p class="text-muted small">
                            File format columns: <b>name, code, description</b>
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
    <script>
function copyEmail() {
    navigator.clipboard.writeText("corecrop@vspl.com");
}
</script>
@endsection
