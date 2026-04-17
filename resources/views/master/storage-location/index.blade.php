@extends('layouts.app')

@section('content')
    
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Storage Location Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage storage location master data</p>
                </div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#locationModal" id="addLocationBtn">
                    <i class="ri-add-line me-1"></i>New Location
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($locations->count())
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Warehouse</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($locations as $location)
                                <tr data-id="{{ $location->id }}">
                                    <td class="fw-500">{{ $location->name }}</td>
                                    <td>
                                        @if($location->code)
                                            <span class="badge bg-info">{{ $location->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($location->warehouse)
                                            <span class="badge bg-secondary">{{ $location->warehouse->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $location->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $location->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $location->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editLocationBtn" data-id="{{ $location->id }}"
                                                data-name="{{ $location->name }}" 
                                                data-code="{{ $location->code }}"
                                                data-warehouse-id="{{ $location->warehouse_id }}"
                                                data-description="{{ $location->description }}">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <form action="{{ route('storage-locations.destroy', $location) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this location?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="ri-delete-bin-line"></i> 
                                            </button>
                                        </form>
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
                    <p class="text-slate-500">No storage locations found. <a href="#" data-bs-toggle="modal" data-bs-target="#locationModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Edit button click
    document.querySelectorAll('.editLocationBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const code = this.getAttribute('data-code');
            const warehouseId = this.getAttribute('data-warehouse-id');
            const description = this.getAttribute('data-description');

            document.getElementById('locationName').value = name;
            document.getElementById('locationCode').value = code || '';
            document.getElementById('locationWarehouse').value = warehouseId || '';
            document.getElementById('locationDescription').value = description || '';
            document.getElementById('locationModalLabel').textContent = 'Edit Storage Location';
            document.getElementById('submitBtn').textContent = 'Update Location';

            const form = document.getElementById('locationForm');
            const hiddenMethod = document.createElement('input');
            hiddenMethod.type = 'hidden';
            hiddenMethod.name = '_method';
            hiddenMethod.value = 'PUT';
            
            // Remove any existing hidden method input
            const existing = form.querySelector('input[name="_method"]');
            if (existing) existing.remove();
            form.appendChild(hiddenMethod);

            form.action = `/storage-locations/${id}`;
            const modal = bootstrap.Modal.getOrCreateInstance(locationModal);
            modal.show();
        });
    });

    // Handle Add button click - reset form
    document.getElementById('addLocationBtn').addEventListener('click', function() {
        document.getElementById('locationForm').reset();
        document.getElementById('locationForm').action = "{{ route('storage-locations.store') }}";
        document.getElementById('locationModalLabel').textContent = 'Add Storage Location';
        document.getElementById('submitBtn').textContent = 'Add Location';
        const existing = document.getElementById('locationForm').querySelector('input[name="_method"]');
        if (existing) existing.remove();
    });
});
</script>

@endsection

@section('modals')
<!-- Storage Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Add Storage Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="locationForm" method="POST" action="{{ route('storage-locations.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="locationName" class="form-label">Location Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="locationName" name="name" placeholder="Enter location name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="locationCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="locationCode" name="code" placeholder="e.g., SL001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="locationWarehouse" class="form-label">Warehouse</label>
                        <select class="form-select" id="locationWarehouse" name="warehouse_id">
                            <option value="">-- Select Warehouse --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="locationStatus" class="form-label">Status</label>
                        <select class="form-select" id="locationStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="locationDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="locationDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Location</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection