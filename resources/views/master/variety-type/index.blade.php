@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Variety Type Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage variety type master data</p>
                </div>
                <button class="btn btn-sm btn-primary" id="addVarietyTypeBtn">
                    <i class="ri-add-line me-1"></i>New Variety Type
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($varietyTypes->count())
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
                                @foreach($varietyTypes as $varietyType)
                                <tr data-id="{{ $varietyType->id }}">
                                    <td class="fw-500">{{ $varietyType->name }}</td>
                                    <td>
                                        @if($varietyType->code)
                                            <span class="badge bg-info">{{ $varietyType->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $varietyType->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $varietyType->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $varietyType->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editVarietyTypeBtn" data-id="{{ $varietyType->id }}"
                                                data-name="{{ $varietyType->name }}" 
                                                data-code="{{ $varietyType->code }}"
                                                data-status="{{ $varietyType->status }}"
                                                data-description="{{ $varietyType->description }}">
                                            <i class="ri-edit-line"></i> 
                                        </button>
                                        <form action="{{ route('variety-types.destroy', $varietyType) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Deactivate this variety type?');">
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
                    <p class="text-slate-500">No variety types found. <a href="#" data-bs-toggle="modal" data-bs-target="#varietyTypeModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>

@endsection
@section('modals')
<!-- Variety Type Modal -->
<div class="modal fade" id="varietyTypeModal" tabindex="-1" role="dialog" aria-labelledby="varietyTypeModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varietyTypeModalLabel">Add Variety Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="varietyTypeForm" method="POST" action="{{ route('variety-types.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="varietyTypeName" class="form-label">Variety Type Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="varietyTypeName" name="name" placeholder="Enter variety type name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="varietyTypeCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="varietyTypeCode" name="code" placeholder="e.g., VT001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="varietyTypeStatus" class="form-label">Status</label>
                        <select class="form-select" id="varietyTypeStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="varietyTypeDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="varietyTypeDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Variety Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('varietyTypeModal');
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);

    document.getElementById('addVarietyTypeBtn').addEventListener('click', function () {
        document.getElementById('varietyTypeForm').reset();
        document.getElementById('varietyTypeForm').action = "{{ route('variety-types.store') }}";
        document.getElementById('varietyTypeModalLabel').textContent = 'Add Variety Type';
        document.getElementById('submitBtn').textContent = 'Add Variety Type';
        const ex = document.getElementById('varietyTypeForm').querySelector('input[name="_method"]');
        if (ex) ex.remove();
        modal.show();
    });

    document.querySelectorAll('.editVarietyTypeBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('varietyTypeName').value        = d.name        || '';
            document.getElementById('varietyTypeCode').value        = d.code        || '';
            document.getElementById('varietyTypeStatus').value      = d.status      ?? '1';
            document.getElementById('varietyTypeDescription').value = d.description || '';
            document.getElementById('varietyTypeModalLabel').textContent = 'Edit Variety Type';
            document.getElementById('submitBtn').textContent              = 'Update Variety Type';
            const form = document.getElementById('varietyTypeForm');
            const ex = form.querySelector('input[name="_method"]');
            if (ex) ex.remove();
            const m = document.createElement('input');
            m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
            form.appendChild(m);
            form.action = `/variety-types/${d.id}`;
            modal.show();
        });
    });
});
</script>
@endpush
