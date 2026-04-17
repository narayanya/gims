@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                        Accession List
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Manage and track all
                        germplasm accessions in the inventory</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="col-md-2 pt-4">
                        <select class="form-select form-select-sm" id="cropFilter">
                            <option value="">All Crops</option>

                            @foreach ($crops as $crop)
                                <option value="{{ strtolower($crop->crop_name) }}">{{ $crop->crop_name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-2 pt-4">
                        <select class="form-select form-select-sm" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="quarantine">Quarantine</option>
                            <option value="depleted">Depleted</option>
                        </select>
                    </div>
                    <div class="col-md-3 pt-4">
                        <select class="form-select form-select-sm" id="warehouseFilter">
                            <option value="">All Warehouses</option>

                            @foreach ($warehouses as $warehouse)
                                <option value="{{ strtolower($warehouse->name) }}">{{ $warehouse->name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-5 d-flex align-items-end gap-2">
                        <button class="" id="resetFilters">
                            <i class="ri-refresh-line"></i>
                        </button>
                        <a href="{{ route('accessions.export') }}" class="btn btn-sm btn-primary">
                            <i class="ri-download-line me-1"></i>Export
                        </a>
                        @if(auth()->user()->hasPermission('accession.create'))
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="ri-upload-line me-1"></i>Import
                        </button>
                        <a href="{{ route('accessionform') }}" class="btn btn-sm btn-primary">
                            <i class="ri-add-line me-1"></i>Add Accession
                        </a>
                        @endif
                    </div>
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
                                    <th><input type="checkbox" class="form-check-input" id="selectAll"></th>
                                    <th>Accession ID</th>
                                    <th>Accession Name</th>
                                    <th>Accession photo</th>
                                    <th>Crop</th>
                                    <th>Accession Request</th>
                                   
                                    <th>Status</th>
                                    <th>Collection Date</th>
                                    <th>Expiry Date</th>
                                    <th>Recheck Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accessions as $accession)
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input row-checkbox"
                                                value="{{ $accession->id }}"></td>

                                        <td><a href="#"
                                                class="text-decoration-none fw-bold">{{ $accession->accession_number }}</a>
                                        </td>
                                        <td>{{ $accession->accession_name ?? '-' }}</td>
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
                                            {{ $accession->expiry_date ? $accession->expiry_date->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            <b>{{ $accession->recheck_date ? $accession->recheck_date->format('d M Y') : 'N/A' }}</b>
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
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="ri-qr-code-line me-2"></i>Print Barcode</a></li>
                                                    @if(auth()->user()->hasPermission('accession.delete'))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i
                                                                class="ri-delete-bin-line me-2"></i>Delete</a></li>
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
                                        </div>
                                        </td>
                                    </tr>
                                @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Accession pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const cropFilter = document.getElementById('cropFilter');
            const statusFilter = document.getElementById('statusFilter');
            const warehouseFilter = document.getElementById('warehouseFilter');
            const resetFilters = document.getElementById('resetFilters');

            const rows = document.querySelectorAll('#accessionTable tbody tr');

            function applyFilters() {

                const cropValue = cropFilter.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();
                const warehouseValue = warehouseFilter.value.toLowerCase();

                rows.forEach(row => {

                    const cells = row.querySelectorAll('td');

                    if (cells.length < 9) return;

                    const crop = cells[2].innerText.toLowerCase();
                    const warehouse = cells[6].innerText.toLowerCase();
                    const status = cells[8].innerText.toLowerCase();

                    const cropMatch = !cropValue || crop.includes(cropValue);
                    const warehouseMatch = !warehouseValue || warehouse.includes(warehouseValue);
                    const statusMatch = !statusValue || status.includes(statusValue);

                    if (cropMatch && warehouseMatch && statusMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }

                });
            }

            cropFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
            warehouseFilter.addEventListener('change', applyFilters);

            resetFilters.addEventListener('click', function() {

                cropFilter.value = '';
                statusFilter.value = '';
                warehouseFilter.value = '';

                rows.forEach(row => row.style.display = '');

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

                fetch('/accessions/' + id, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {

                    document.getElementById('fullDetailsBtn').href = '/accessions/' + data.id;

                    function setText(id, value) {
                        let el = document.getElementById(id);
                        if (el) el.innerText = value ?? 'N/A';
                    }

                    setText('v_number', data.accession_name ?? data.accession_number);
                    setText('v_crop', data.crop?.crop_name);
                    setText('v_scientific_name', data.crop?.scientific_name);
                    setText('v_family', data.crop?.family_name);
                    setText('v_genus', data.crop?.genus);
                    setText('v_collection_number', data.collection_number);
                    setText('v_collection_date', formatDate(data.collection_date));
                    setText('v_donor_name', data.donor_name);
                    setText('v_collector_name', data.collector_name);
                    setText('v_origin_country', data.country?.name);
                    setText('v_quantity', data.quantity);
                    setText('v_warehouse', data.warehouse?.name);
                    setText('v_status', data.status);
                    setText('v_date', formatDate(data.collection_date));

                    let img = document.getElementById('v_photo');
                    
                    if (img) img.src = data.photo_url || '/placeholder.png';

                });

            });

        });

    </script>

    <style>
        .table th {
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
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
                            Columns format:
                            <b>accession_number, crop, variety, source, origin_country, collection_date, remarks</b>
                        </p>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Accession Details</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    
                    <a id="fullDetailsBtn" class="btn btn-sm btn-primary float-end">
                        Click to Full Details
                    </a>
                    <table class="table table-bordered">
                        <tr>
                            <th>Accession Number</th>
                            <td id="v_number"></td>
                        </tr>
                        <tr>
                            <th>Accession Name</th>
                            <td id="v_number"></td>
                        </tr>
                        
                        <tr>
                            <th>Crop</th>
                            <td id="v_crop"></td>
                        </tr>
                        <tr>
                            <th>Scientific Name</th>
                            <td id="v_scientific_name"></td>
                        </tr>
                        <tr>
                            <th>Family</th>
                            <td id="v_family"></td>
                        </tr>
                        <tr>
                            <th>Genus</th>
                            <td id="v_genus"></td>
                        </tr>
                        
                        <tr>
                            <th>Photo</th>
                            <td><img id="v_photo" src="/placeholder.png" alt="Accession Photo" class="img-fluid"></td>  
                        </tr>
                        <tr>
                            <th>Collection Date</th>
                            <td id="v_collection_date"></td>
                        </tr>
                        <tr>
                            <th>Collector Name</th>
                            <td id="v_collector_name"></td>
                        </tr>
                        <tr>
                            <th>Donor Name</th>
                            <td id="v_donor_name"></td>
                        </tr>
                        <tr>
                            <th>Origin Country</th>
                            <td id="v_origin_country"></td>
                        </tr>
                        <tr>
                            <th>Collection Number</th>
                            <td id="v_collection_number"></td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td id="v_status"></td>
                        </tr>

                        <tr>
                            <th>Collection Date</th>
                            <td id="v_date"></td>
                        </tr>

                    </table>

                </div>

            </div>
        </div>
    </div>
@endsection
