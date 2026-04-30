@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Arrival Type Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage arrival type master data</p>
                </div>
                <button class="btn btn-sm btn-primary" id="addArrivalTypeBtn">
                    <i class="ri-add-line me-1"></i>New Arrival Type
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($arrivalTypes->count())
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
                                @foreach($arrivalTypes as $arrivalType)
                                <tr data-id="{{ $arrivalType->id }}">
                                    <td class="fw-500">{{ $arrivalType->name }}</td>
                                    <td>
                                        @if($arrivalType->code)
                                            <span class="badge bg-info">{{ $arrivalType->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $arrivalType->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $arrivalType->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $arrivalType->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editArrivalTypeBtn" data-id="{{ $arrivalType->id }}"
                                                data-name="{{ $arrivalType->name }}" 
                                                data-code="{{ $arrivalType->code }}"
                                                data-status="{{ $arrivalType->status }}"
                                                data-description="{{ $arrivalType->description }}">
                                            <i class="ri-edit-line"></i> 
                                        </button>
                                        <form action="{{ route('arrival-types.destroy', $arrivalType) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Deactivate this arrival type?');">
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
                    <p class="text-slate-500">No arrival types found. <a href="#" data-bs-toggle="modal" data-bs-target="#arrivalTypeModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>

@endsection

@section('modals')
<!-- Arrival Type Modal -->
<div class="modal fade" id="arrivalTypeModal" tabindex="-1" role="dialog" aria-labelledby="arrivalTypeModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="arrivalTypeModalLabel">Add Arrival Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="arrivalTypeForm" method="POST" action="{{ route('arrival-types.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="arrivalTypeName" class="form-label">Arrival Type Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="arrivalTypeName" name="name" placeholder="Enter arrival type name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="arrivalTypeCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="arrivalTypeCode" name="code" placeholder="e.g., AT001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="arrivalTypeStatus" class="form-label">Status</label>
                        <select class="form-select" id="arrivalTypeStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="arrivalTypeDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="arrivalTypeDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Arrival Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('arrivalTypeModal');
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);

    // ── New ──
    document.getElementById('addArrivalTypeBtn').addEventListener('click', function () {
        document.getElementById('arrivalTypeForm').reset();
        document.getElementById('arrivalTypeForm').action = "{{ route('arrival-types.store') }}";
        document.getElementById('arrivalTypeModalLabel').textContent = 'Add Arrival Type';
        document.getElementById('submitBtn').textContent = 'Add Arrival Type';
        document.getElementById('arrivalTypeStatus').value = '1';
        const ex = document.getElementById('arrivalTypeForm').querySelector('input[name="_method"]');
        if (ex) ex.remove();
        modal.show();
    });

    // ── Edit ──
    document.querySelectorAll('.editArrivalTypeBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('arrivalTypeName').value        = d.name        || '';
            document.getElementById('arrivalTypeCode').value        = d.code        || '';
            document.getElementById('arrivalTypeStatus').value      = d.status      ?? '1';
            document.getElementById('arrivalTypeDescription').value = d.description || '';
            document.getElementById('arrivalTypeModalLabel').textContent = 'Edit Arrival Type';
            document.getElementById('submitBtn').textContent             = 'Update Arrival Type';

            const form = document.getElementById('arrivalTypeForm');
            const ex = form.querySelector('input[name="_method"]');
            if (ex) ex.remove();
            const m = document.createElement('input');
            m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
            form.appendChild(m);
            form.action = `/arrival-types/${d.id}`;
            modal.show();
        });
    });
});
</script>
@endpush
