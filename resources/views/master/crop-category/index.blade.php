@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Crop Category Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage crop category master data</p>
                </div>
                <button class="btn btn-sm btn-primary" id="addCropCategoryBtn">
                    <i class="ri-add-line me-1"></i>New Crop Category
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($cropCategories->count())
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
                                @foreach($cropCategories as $cropCategory)
                                <tr data-id="{{ $cropCategory->id }}">
                                    <td class="fw-500">{{ $cropCategory->name }}</td>
                                    <td>
                                        @if($cropCategory->code)
                                            <span class="badge bg-info">{{ $cropCategory->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $cropCategory->description ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $cropCategory->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $cropCategory->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                        </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editCropCategoryBtn"
                                                data-id="{{ $cropCategory->id }}"
                                                data-name="{{ $cropCategory->name }}"
                                                data-code="{{ $cropCategory->code }}"
                                                data-status="{{ $cropCategory->status }}"
                                                data-description="{{ $cropCategory->description }}">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <form action="{{ route('crop-categories.destroy', $cropCategory) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Deactivate this crop category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Deactivate">
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
                    <p class="text-slate-500">No crop categories found. <a href="#" data-bs-toggle="modal" data-bs-target="#cropCategoryModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>

<style>
.modal-backdrop { z-index: 1050; }
.modal { z-index: 1060 !important; }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('cropCategoryModal');
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);

    // Add
    document.getElementById('addCropCategoryBtn').addEventListener('click', function () {
        document.getElementById('cropCategoryForm').reset();
        document.getElementById('cropCategoryForm').action = "{{ route('crop-categories.store') }}";
        document.getElementById('cropCategoryModalLabel').textContent = 'Add Crop Category';
        document.getElementById('submitBtn').textContent = 'Add Crop Category';
        const ex = document.getElementById('cropCategoryForm').querySelector('input[name="_method"]');
        if (ex) ex.remove();
        modal.show();
    });

    // Edit
    document.querySelectorAll('.editCropCategoryBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('cropCategoryName').value        = d.name        || '';
            document.getElementById('cropCategoryCode').value        = d.code        || '';
            document.getElementById('cropCategoryStatus').value      = d.status      ?? '1';
            document.getElementById('cropCategoryDescription').value = d.description || '';
            document.getElementById('cropCategoryModalLabel').textContent = 'Edit Crop Category';
            document.getElementById('submitBtn').textContent              = 'Update Crop Category';

            const form = document.getElementById('cropCategoryForm');
            const ex = form.querySelector('input[name="_method"]');
            if (ex) ex.remove();
            const m = document.createElement('input');
            m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
            form.appendChild(m);
            form.action = `/crop-categories/${d.id}`;
            modal.show();
        });
    });
});
</script>
@endpush

@section('modals')
<!-- Crop Category Modal -->
<div class="modal fade" id="cropCategoryModal" tabindex="-1" role="dialog" aria-labelledby="cropCategoryModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropCategoryModalLabel">Add Crop Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cropCategoryForm" method="POST" action="{{ route('crop-categories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cropCategoryName" class="form-label">Crop Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cropCategoryName" name="name" placeholder="Enter crop category name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="cropCategoryCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="cropCategoryCode" name="code" placeholder="e.g., CC001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="cropCategoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="cropCategoryStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cropCategoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="cropCategoryDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Crop Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection