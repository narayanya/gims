@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Season Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage season master data</p>
                </div>
                <button class="btn btn-sm btn-primary" id="addSeasonBtn">
                    <i class="ri-add-line me-1"></i>New Season
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($seasons->count())
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
                                @foreach($seasons as $season)
                                <tr data-id="{{ $season->id }}">
                                    <td class="fw-500">{{ $season->name }}</td>
                                    <td>
                                        @if($season->code)
                                            <span class="badge bg-info">{{ $season->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $season->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $season->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $season->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editSeasonBtn" data-id="{{ $season->id }}"
                                                data-name="{{ $season->name }}" 
                                                data-code="{{ $season->code }}"
                                                data-status="{{ $season->status }}"
                                                data-description="{{ $season->description }}">
                                            <i class="ri-edit-line"></i> 
                                        </button>
                                        <form action="{{ route('seasons.destroy', $season) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Deactivate this season?');">
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
                    <p class="text-slate-500">No seasons found. <a href="#" data-bs-toggle="modal" data-bs-target="#seasonModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>

@endsection
@section('modals')
<!-- Season Modal -->
<div class="modal fade" id="seasonModal" tabindex="-1" role="dialog" aria-labelledby="seasonModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seasonModalLabel">Add Season</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="seasonForm" method="POST" action="{{ route('seasons.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="seasonName" class="form-label">Season Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="seasonName" name="name" placeholder="Enter season name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="seasonCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="seasonCode" name="code" placeholder="e.g., S001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                        <div class="mb-3">
                            <label for="seasonStatus" class="form-label">Status</label>
                            <select class="form-select" id="seasonStatus" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    <div class="mb-3">
                        <label for="seasonDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="seasonDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Season</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('seasonModal');
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);

    document.getElementById('addSeasonBtn').addEventListener('click', function () {
        document.getElementById('seasonForm').reset();
        document.getElementById('seasonForm').action = "{{ route('seasons.store') }}";
        document.getElementById('seasonModalLabel').textContent = 'Add Season';
        document.getElementById('submitBtn').textContent = 'Add Season';
        const ex = document.getElementById('seasonForm').querySelector('input[name="_method"]');
        if (ex) ex.remove();
        modal.show();
    });

    document.querySelectorAll('.editSeasonBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('seasonName').value        = d.name        || '';
            document.getElementById('seasonCode').value        = d.code        || '';
            document.getElementById('seasonStatus').value      = d.status      ?? '1';
            document.getElementById('seasonDescription').value = d.description || '';
            document.getElementById('seasonModalLabel').textContent = 'Edit Season';
            document.getElementById('submitBtn').textContent        = 'Update Season';
            const form = document.getElementById('seasonForm');
            const ex = form.querySelector('input[name="_method"]');
            if (ex) ex.remove();
            const m = document.createElement('input');
            m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
            form.appendChild(m);
            form.action = `/seasons/${d.id}`;
            modal.show();
        });
    });
});
</script>
@endpush
