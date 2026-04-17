@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Category Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage category master data</p>
                </div>
                <button class="btn btn-sm btn-primary" id="addCategoryBtn">
                    <i class="ri-add-line me-1"></i>New Category
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($categories->count())
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
                                @foreach($categories as $category)
                                <tr data-id="{{ $category->id }}">
                                    <td class="fw-500">{{ $category->name }}</td>
                                    <td>
                                        @if($category->code)
                                            <span class="badge bg-info">{{ $category->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->description ?? '-' }}</td>
                                    <td>
                                       <span class="badge {{ $category->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $category->status == 1 ? 'Active' : 'Inactive' }}
                                        </span> 
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editCategoryBtn" data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}" 
                                                data-code="{{ $category->code }}"
                                                data-status="{{ $category->status }}"
                                                data-description="{{ $category->description }}">
                                            <i class="ri-edit-line"></i> 
                                        </button>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this category?');">
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
                    <p class="text-slate-500">No categories found. <a href="#" data-bs-toggle="modal" data-bs-target="#categoryModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>

<style>
.modal-backdrop {
    z-index: 1050;
}
.modal {
    z-index: 1060 !important;
}
</style>


@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryModalEl = document.getElementById('categoryModal');
    const modal = bootstrap.Modal.getOrCreateInstance(categoryModalEl);

    // Add button
    document.getElementById('addCategoryBtn').addEventListener('click', function () {
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryForm').action = "{{ route('categories.store') }}";
        document.getElementById('categoryModalLabel').textContent = 'Add Category';
        document.getElementById('submitBtn').textContent = 'Add Category';
        const existing = document.getElementById('categoryForm').querySelector('input[name="_method"]');
        if (existing) existing.remove();
        modal.show();
    });

    // Edit buttons
    document.querySelectorAll('.editCategoryBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id          = this.dataset.id;
            const name        = this.dataset.name;
            const code        = this.dataset.code;
            const description = this.dataset.description;

            document.getElementById('categoryName').value        = name        || '';
            document.getElementById('categoryCode').value        = code        || '';
            document.getElementById('categoryDescription').value = description || '';
            document.getElementById('categoryStatus').value      = this.dataset.status ?? '1';
            document.getElementById('categoryModalLabel').textContent = 'Edit Category';
            document.getElementById('submitBtn').textContent         = 'Update Category';

            const form = document.getElementById('categoryForm');
            const existing = form.querySelector('input[name="_method"]');
            if (existing) existing.remove();
            const m = document.createElement('input');
            m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
            form.appendChild(m);
            form.action = `/categories/${id}`;

            modal.show();
        });
    });
});
</script>
@endpush


@section('modals')
    <!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" placeholder="Enter category name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="categoryCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="categoryCode" name="code" placeholder="e.g., CAT001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                     <div class="mb-3">
                        <label for="categoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="categoryStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection