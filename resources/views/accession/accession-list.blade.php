@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 border-bottom border-sage-muted/20 pb-3">

    <!-- Left Section -->
    <div>
        <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight d-flex align-items-center gap-2 mb-1">
            Accession List
        </h3>

        <p class="text-sage-600 dark:text-sage-400 text-sm mb-0" style="color:#777777;">
            Manage and track all germplasm accessions in the inventory
        </p>
    </div>

    <!-- Right Section -->
    <div class="d-flex flex-wrap align-items-end gap-2">

        <!-- Search -->
        <div style="min-width:240px;">
            <label class="form-label small mb-1">Search</label>

            <input type="text"
                class="form-control form-control-sm"
                id="accessionSearch"
                placeholder="Accession No, Name, Crop...">
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

        <!-- Status Filter -->
        <div style="min-width:180px;">
            <label class="form-label small mb-1">Status</label>

            <select class="form-select form-select-sm" id="statusFilter">

                <option value="">All Status</option>

                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="quarantine">Quarantine</option>
                <option value="depleted">Depleted</option>

            </select>
        </div>

        <!-- Reset -->
        <div>
            <button class="btn btn-sm btn-light border"
                id="resetFilters"
                title="Reset Filters">

                <i class="ri-refresh-line"></i>
            </button>
        </div>

        @if(auth()->user()->hasPermission('accession.create'))

            <!-- Add -->
            <div>
                <a href="{{ route('accessionform') }}"
                    class="btn btn-sm btn-primary">

                    <i class="ri-add-line me-1"></i>
                    Add Accession
                </a>
            </div>

            <!-- Import -->
            <div>
                <button class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#importModal">

                    <i class="ri-upload-line me-1"></i>
                    Import
                </button>
            </div>

            <!-- Export -->
            <div>
                <a href="{{ route('accessions.export') }}"
                    class="btn btn-sm btn-success">

                    <i class="ri-download-line me-1"></i>
                    Export
                </a>
            </div>

        @endif

    </div>

</div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-check-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Accession Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="accessionTable">
                            <thead class="table-light">
                                <tr>
                                    <!--<th><input type="checkbox" class="form-check-input" id="selectAll"></th>-->
                                    <th>Accession ID</th>
                                    <th>Source</th>
                                    <th>Storage Time </th>
                                    <th>Sample Id</th>
                                    <th>Accession photo</th>
                                    <th>Crop</th>
                                    <th>Accession Request</th>
                                   
                                    <th>Status</th>
                                    <th>Collection Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accessions as $accession)
                                    <tr class="accessionRow"
    >
                                        {{--<td><input type="checkbox" class="form-check-input row-checkbox"
                                                value="{{ $accession->id }}"></td>--}}

                                        <td><a href="#"
                                                class="text-decoration-none fw-bold">{{ $accession->accession_number }}</a>
                                        </td>
                                        <td>
                                            {{ ucfirst($accession->acc_source ?? '-') }}

                                            @if($accession->acc_source == 'external' && $accession->ext_source)
                                                <br>
                                                <small class="text-muted">
                                                    {{ $accession->ext_source }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $accession->storageTime?->code ?? '-' }}</td>
                                        <td>{{ $accession->sample_id ?? '-' }}</td>
                                        <td>
                                            @if($accession->image_path)
                                                <img src="{{ $accession->photo_url }}"
                                                     alt="Accession Image"
                                                     class="img-thumbnail"
                                                     style="max-width:60px; max-height:60px; object-fit:cover;">
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $accession->crop->crop_name ?? 'N/A' }}</td>
                                        <td>{{ $accession->requester_show == 'yes' ? 'Yes' : 'No' }}</td>
                                        <td>
                                            @if ($accession->status == '1')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $accession->collection_date ? $accession->collection_date->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            
                                            <div class="dropdown">
                                                
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                    More 
                                                </button>
                                               
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item viewAccession"
                                                            data-id="{{ $accession->id }}" data-bs-toggle="modal"
                                                            data-bs-target="#viewModal">
                                                            <i class="ri-eye-line me-2"></i>Details
                                                        </a>
                                                    </li>
                                                    @if($accession->requester_show == 'yes')
                                                    <li>
                                                        <a class="dropdown-item"
                                                        href="{{ route('requests.create', ['accession_id' => $accession->id]) }}">
                                                            <i class="ri-eye-line me-2"></i>Request Accessions
                                                        </a>
                                                    </li>
                                                    @endif

                                                    @if(auth()->user()->hasPermission('accession.edit'))
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('accessions.edit', $accession->id) }}"> 
                                                            <i class="ri-edit-line me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    @endif
                                                    @if(auth()->user()->hasPermission('accession.delete'))
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="ri-qr-code-line me-2"></i>Print Barcode</a></li>
                                                    
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>  <form action="{{ route('accession.deactivate', $accession->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to deactivate this accession?')">

                                                            @csrf

                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-close-circle-line me-2"></i>
                                                                Deactivate
                                                            </button>

                                                        </form></li>
                                                        <li>
                                                            <form action="{{ route('accession.delete', $accession->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Are you sure you want to delete this accession?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="ri-delete-bin-line me-2"></i>
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ri-inbox-line" style="font-size: 3rem;"></i>
                                                <p class="mt-2 mb-0">No accessions found</p>
                                                <a href="{{ route('accessionform') }}" class="btn btn-primary btn-sm mt-2">
                                                    <i class="ri-add-line me-1"></i>Add First Accession
                                                </a>
                                            </div>
                                            <li><a class="dropdown-item" href="#"><i
                                                        class="ri-eye-line me-2"></i>View</a></li>
                                            <li><a class="dropdown-item" href="#"><i
                                                        class="ri-edit-line me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item" href="#"><i
                                                        class="ri-file-copy-line me-2"></i>Duplicate</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item text-danger" href="#"><i
                                                        class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                            </ul>
                                        
                                        </td>
                                    </tr>
                                @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <div>
                        Showing {{ $accessions->firstItem() }} to {{ $accessions->lastItem() }}
                        of {{ $accessions->total() }} results
                    </div>

                    <div>
                        {{ $accessions->links() }}
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const cropFilter = document.getElementById('cropFilter');
    const statusFilter = document.getElementById('statusFilter');
    const accessionSearch = document.getElementById('accessionSearch');
    const resetFilters = document.getElementById('resetFilters');

    const rows = document.querySelectorAll('#accessionTable tbody tr');

    function applyFilters() {

        const cropValue =
            cropFilter.value.toLowerCase().trim();

        const statusValue =
            statusFilter.value.toLowerCase().trim();

        const searchValue =
            accessionSearch.value.toLowerCase().trim();

        rows.forEach(row => {

            const cells = row.querySelectorAll('td');

            if (cells.length < 10) return;

            // correct indexes
            const accessionId =
                cells[0].innerText.toLowerCase();

            const accessionName =
                cells[1].innerText.toLowerCase();

            const crop =
                cells[6].innerText.toLowerCase();

            const status =
                cells[8].innerText.toLowerCase();

            const sampleId =
                cells[4].innerText.toLowerCase();

            // filters
            const cropMatch =
                !cropValue || crop.includes(cropValue);

            const statusMatch =
                !statusValue || status.includes(statusValue);

            const searchMatch =
                !searchValue ||
                accessionId.includes(searchValue) ||
                accessionName.includes(searchValue) ||
                crop.includes(searchValue) ||
                sampleId.includes(searchValue);

            if (
                cropMatch &&
                statusMatch &&
                searchMatch
            ) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }

        });
    }

    cropFilter.addEventListener('change', applyFilters);

    statusFilter.addEventListener('change', applyFilters);

    accessionSearch.addEventListener('keyup', applyFilters);

    resetFilters.addEventListener('click', function () {

        cropFilter.value = '';
        statusFilter.value = '';
        accessionSearch.value = '';

        applyFilters();
    });

        });


        function formatDate(date) {
            if (!date) return '—';
            return new Date(date).toLocaleDateString();
        }

        document.querySelectorAll('.viewAccession').forEach(button => {

            button.addEventListener('click', function() {

                let id = this.dataset.id;
                if (!id) return;

                // Reset fields
                document.querySelectorAll('#viewModal [id^="v_"]').forEach(el => el.innerText = '—');
                document.getElementById('v_photo').src = '';
                document.getElementById('v_photo').style.display = 'none';

                fetch('/accessions/' + id + '/json')
                .then(res => {
                    if (!res.ok) throw new Error('Failed to load');
                    return res.json();
                })
                .then(data => {

                    // Full details button — only show if user has permission
                    const btn = document.getElementById('fullDetailsBtn');
                    if (data.can_view_full) {
                        btn.href = '/accessions/' + data.id;
                        btn.style.display = '';
                    } else {
                        btn.style.display = 'none';
                    }

                    function set(id, value) {
                        let el = document.getElementById(id);
                        if (el) el.innerText = value || '—';
                    }

                    set('v_number',            data.accession_number);
                    set('v_crop',              data.crop_name);
                    set('v_scientific_name',   data.scientific_name);
                    set('v_family',            data.family_name);
                    set('v_genus',             data.genus);
                    set('v_collection_number', data.collection_number);
                    set('v_collection_date',   data.collection_date);
                    set('v_collector_name',    data.collector_name);
                    set('v_donor_name',        data.donor_name);
                    set('v_collection_site',   data.collection_site);
                    set('v_origin_country',    data.country);
                    set('v_state',             data.state);
                    set('v_district',          data.district);
                    set('v_city',              data.city);
                    set('v_quantity',          data.quantity ? data.quantity + ' ' + (data.unit || '') : '—');
                    set('v_storage_time',      data.storage_time);
                    set('v_biological_status', data.biological_status);
                    set('v_sample_type',       data.sample_type);
                    set('v_status',            data.status);
                    set('v_barcode',           data.barcode);
                    set('v_notes',             data.notes);

                    let img = document.getElementById('v_photo');
                    if (data.photo_url) {
                        img.src = data.photo_url;
                        img.style.display = '';
                    }
                })
                .catch(() => {
                    document.getElementById('v_number').innerText = 'Failed to load data.';
                });
            });
        });

    </script>

    <style>
        .table th {
            font-weight: 600;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
        }

        .table td {
            vertical-align: middle;
        }

        .dropdown-menu {
            min-width: 140px;
        }
    </style>
@endsection

@section('modals')
    <!-- Import Modal -->

    <div class="modal fade" id="importModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Accessions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('accessions.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload CSV / Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('accessions.template') }}" class="text-decoration-none">
                                <i class="ri-download-line me-1"></i>Download sample CSV template
                            </a>
                        </div>

                        <p class="text-muted small">
                            Required columns: <b>sample_id, crop_name</b><br>
                            Optional: <b>acc_source, ext_source, year_of_arrival, collection_number, collection_date, collector_name, donor_name, collection_site, country_name, state_name, district_name, city_name, latitude, longitude, pincode, biological_status, sample_type, reproductive_type, barcode_type, barcode, status, notes, storage_time_id</b>
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

    <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="ri-seedling-line me-2"></i>Accession Details</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <a id="fullDetailsBtn" class="btn btn-sm btn-outline-primary float-end mb-2">
                        <i class="ri-external-link-line me-1"></i>Full Details
                    </a>

                    {{-- Basic Info --}}
                    <h6 class="text-muted border-bottom pb-1 mb-2">Basic Information</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <tr><th style="width:35%">Accession Number</th><td id="v_number"></td></tr>
                        <tr><th>Crop</th><td id="v_crop"></td></tr>
                        <tr><th>Scientific Name</th><td id="v_scientific_name"></td></tr>
                        <tr><th>Family</th><td id="v_family"></td></tr>
                        <tr><th>Genus</th><td id="v_genus"></td></tr>
                        <tr><th>Biological Status</th><td id="v_biological_status"></td></tr>
                        <tr><th>Sample Type</th><td id="v_sample_type"></td></tr>
                        <tr><th>Status</th><td id="v_status"></td></tr>
                        <tr><th>Barcode</th><td id="v_barcode"></td></tr>
                    </table>

                    {{-- Quantity & Storage --}}
                    <h6 class="text-muted border-bottom pb-1 mb-2">Quantity & Storage</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <tr><th style="width:35%">Available Quantity</th><td id="v_quantity"></td></tr>
                        <tr><th>Storage Time</th><td id="v_storage_time"></td></tr>
                    </table>

                    {{-- Collection Info --}}
                    <h6 class="text-muted border-bottom pb-1 mb-2">Collection Information</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <tr><th style="width:35%">Collection Number</th><td id="v_collection_number"></td></tr>
                        <tr><th>Collection Date</th><td id="v_collection_date"></td></tr>
                        <tr><th>Collector Name</th><td id="v_collector_name"></td></tr>
                        <tr><th>Donor Name</th><td id="v_donor_name"></td></tr>
                        <tr><th>Collection Site</th><td id="v_collection_site"></td></tr>
                        <tr><th>Country</th><td id="v_origin_country"></td></tr>
                        <tr><th>State</th><td id="v_state"></td></tr>
                        <tr><th>District</th><td id="v_district"></td></tr>
                        <tr><th>City/Village</th><td id="v_city"></td></tr>
                    </table>

                    {{-- Notes & Photo --}}
                    <h6 class="text-muted border-bottom pb-1 mb-2">Notes & Photo</h6>
                    <table class="table table-sm table-bordered mb-2">
                        <tr><th style="width:35%">Notes</th><td id="v_notes"></td></tr>
                    </table>
                    <img id="v_photo" src="" alt="Accession Photo" class="img-thumbnail mt-2" style="max-height:150px;display:none;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
