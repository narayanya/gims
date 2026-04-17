@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Crop Type Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage crop type master data</p>
                </div>
                <button class="btn btn-sm btn-primary" id="addCropTypeBtn">
                    <i class="ri-add-line me-1"></i>New Crop Type
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($cropTypes->count())
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cropTypes as $cropType)
                                <tr data-id="{{ $cropType->id }}">
                                    <td class="fw-500">{{ $cropType->name }}</td>
                                    <td>
                                        @if($cropType->code)
                                            <span class="badge bg-info">{{ $cropType->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $cropType->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $cropType->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $cropType->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editCropTypeBtn" data-id="{{ $cropType->id }}"
                                                data-name="{{ $cropType->name }}" 
                                                data-code="{{ $cropType->code }}"
                                                data-status="{{ $cropType->status }}"
                                                data-description="{{ $cropType->description }}">
                                            <i class="ri-edit-line"></i> 
                                        </button>
                                        <form action="{{ route('crop-types.destroy', $cropType) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Deactivate this crop type?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="ri-forbid-line"></i> 
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
                    <p class="text-slate-500">No crop types found. <a href="#" data-bs-toggle="modal" data-bs-target="#cropTypeModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>

@endsection

@section('modals')
<!-- Crop Type Modal -->
<div class="modal fade" id="cropTypeModal" tabindex="-1" role="dialog" aria-labelledby="cropTypeModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropTypeModalLabel">Add Crop Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cropTypeForm" method="POST" action="{{ route('crop-types.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cropTypeName" class="form-label">Crop Type Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cropTypeName" name="name" placeholder="Enter crop type name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="cropTypeCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="cropTypeCode" name="code" placeholder="e.g., CT001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="cropTypeStatus" class="form-label">Status</label>
                        <select class="form-select" id="cropTypeStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cropTypeDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="cropTypeDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Crop Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('cropTypeModal');
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);

    document.getElementById('addCropTypeBtn').addEventListener('click', function () {
        document.getElementById('cropTypeForm').reset();
        document.getElementById('cropTypeForm').action = "{{ route('crop-types.store') }}";
        document.getElementById('cropTypeModalLabel').textContent = 'Add Crop Type';
        document.getElementById('submitBtn').textContent = 'Add Crop Type';
        const ex = document.getElementById('cropTypeForm').querySelector('input[name="_method"]');
        if (ex) ex.remove();
        modal.show();
    });

    document.querySelectorAll('.editCropTypeBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('cropTypeName').value        = d.name        || '';
            document.getElementById('cropTypeCode').value        = d.code        || '';
            document.getElementById('cropTypeStatus').value      = d.status      ?? '1';
            document.getElementById('cropTypeDescription').value = d.description || '';
            document.getElementById('cropTypeModalLabel').textContent = 'Edit Crop Type';
            document.getElementById('submitBtn').textContent          = 'Update Crop Type';
            const form = document.getElementById('cropTypeForm');
            const ex = form.querySelector('input[name="_method"]');
            if (ex) ex.remove();
            const m = document.createElement('input');
            m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
            form.appendChild(m);
            form.action = `/crop-types/${d.id}`;
            modal.show();
        });
    });
});
</script>
@endpush
