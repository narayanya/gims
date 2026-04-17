@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Seed Class Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage seed class master data</p>
                </div>
                <button class="btn btn-sm btn-primary" id="addSeedClassBtn">
                    <i class="ri-add-line me-1"></i>New Seed Class
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($seedClasses->count())
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
                                @foreach($seedClasses as $seedClass)
                                <tr data-id="{{ $seedClass->id }}">
                                    <td class="fw-500">{{ $seedClass->name }}</td>
                                    <td>
                                        @if($seedClass->code)
                                            <span class="badge bg-info">{{ $seedClass->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $seedClass->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $seedClass->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $seedClass->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editSeedClassBtn" data-id="{{ $seedClass->id }}"
                                                data-name="{{ $seedClass->name }}" 
                                                data-code="{{ $seedClass->code }}"
                                                data-status="{{ $seedClass->status }}"
                                                data-description="{{ $seedClass->description }}">
                                            <i class="ri-edit-line"></i> 
                                        </button>
                                        <form action="{{ route('seed-classes.destroy', $seedClass) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Deactivate this seed class?');">
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
                    <p class="text-slate-500">No seed classes found. <a href="#" data-bs-toggle="modal" data-bs-target="#seedClassModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>

@endsection
@section('modals')
<!-- Seed Class Modal -->
<div class="modal fade" id="seedClassModal" tabindex="-1" role="dialog" aria-labelledby="seedClassModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seedClassModalLabel">Add Seed Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="seedClassForm" method="POST" action="{{ route('seed-classes.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="seedClassName" class="form-label">Seed Class Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="seedClassName" name="name" placeholder="Enter seed class name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="seedClassCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="seedClassCode" name="code" placeholder="e.g., SC001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="seedClassStatus" class="form-label">Status</label>
                        <select class="form-select" id="seedClassStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="seedClassDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="seedClassDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Seed Class</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('seedClassModal');
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);

    document.getElementById('addSeedClassBtn').addEventListener('click', function () {
        document.getElementById('seedClassForm').reset();
        document.getElementById('seedClassForm').action = "{{ route('seed-classes.store') }}";
        document.getElementById('seedClassModalLabel').textContent = 'Add Seed Class';
        document.getElementById('submitBtn').textContent = 'Add Seed Class';
        const ex = document.getElementById('seedClassForm').querySelector('input[name="_method"]');
        if (ex) ex.remove();
        modal.show();
    });

    document.querySelectorAll('.editSeedClassBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('seedClassName').value        = d.name        || '';
            document.getElementById('seedClassCode').value        = d.code        || '';
            document.getElementById('seedClassStatus').value      = d.status      ?? '1';
            document.getElementById('seedClassDescription').value = d.description || '';
            document.getElementById('seedClassModalLabel').textContent = 'Edit Seed Class';
            document.getElementById('submitBtn').textContent           = 'Update Seed Class';
            const form = document.getElementById('seedClassForm');
            const ex = form.querySelector('input[name="_method"]');
            if (ex) ex.remove();
            const m = document.createElement('input');
            m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
            form.appendChild(m);
            form.action = `/seed-classes/${d.id}`;
            modal.show();
        });
    });
});
</script>
@endpush
