@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Lot Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage lot master data</p>
                </div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#lotModal" id="addLotBtn">
                    <i class="ri-add-line me-1"></i>New Lot
                </button>
            </div>
            
            

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($lots->count())
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Lot Type</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lots as $lot)
                                <tr data-id="{{ $lot->id }}">
                                    <td class="fw-500">{{ $lot->name }}</td>
                                    <td>
                                        @if($lot->code)
                                            <span class="badge bg-info">{{ $lot->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $lot->lotType->name ?? '-' }}
                                    </td>
                                    <td>{{ $lot->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $lot->status == 'active' || $lot->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $lot->status == 'active' || $lot->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editLotBtn" data-id="{{ $lot->id }}"
                                                data-name="{{ $lot->name }}" 
                                                data-code="{{ $lot->code }}"
                                                data-lot-type-id="{{ $lot->lot_type_id }}"
                                                data-status="{{ $lot->status }}"
                                                data-description="{{ $lot->description }}">
                                            <i class="ri-edit-line"></i> 
                                        </button>
                                        <form action="{{ route('lots.destroy', $lot) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Deactivate this lot?');">
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
                    <p class="text-slate-500">No lots found. <a href="#" data-bs-toggle="modal" data-bs-target="#lotModal">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const lotModalEl = document.getElementById('lotModal');

    // Handle Edit button click
    document.querySelectorAll('.editLotBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id          = this.dataset.id;
            const name        = this.dataset.name;
            const code        = this.dataset.code;
            const lotTypeId   = this.dataset.lotTypeId;
            const status      = this.dataset.status;
            const description = this.dataset.description;

            document.getElementById('lotName').value        = name        || '';
            document.getElementById('lotCode').value        = code        || '';
            document.getElementById('lotType').value        = lotTypeId   || '';
            document.getElementById('lotStatus').value      = status      || '1';
            document.getElementById('lotDescription').value = description || '';
            document.getElementById('lotModalLabel').textContent = 'Edit Lot';
            document.getElementById('submitBtn').textContent     = 'Update Lot';

            const form = document.getElementById('lotForm');
            const existing = form.querySelector('input[name="_method"]');
            if (existing) existing.remove();
            const method = document.createElement('input');
            method.type  = 'hidden';
            method.name  = '_method';
            method.value = 'PUT';
            form.appendChild(method);

            form.action = `/lots/${id}`;
            bootstrap.Modal.getOrCreateInstance(lotModalEl).show();
        });
    });

    // Handle Add button click
    document.getElementById('addLotBtn').addEventListener('click', function() {
        document.getElementById('lotForm').reset();
        document.getElementById('lotForm').action = "{{ route('lots.store') }}";
        document.getElementById('lotModalLabel').textContent = 'Add Lot';
        document.getElementById('submitBtn').textContent     = 'Add Lot';
        const existing = document.getElementById('lotForm').querySelector('input[name="_method"]');
        if (existing) existing.remove();
        bootstrap.Modal.getOrCreateInstance(lotModalEl).show();
    });
});
</script>

@endsection

@section('modals')
<!-- Lot Modal -->
<div class="modal fade" id="lotModal" tabindex="-1" role="dialog" aria-labelledby="lotModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lotModalLabel">Add Lot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="lotForm" method="POST" action="{{ route('lots.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lotName" class="form-label">Lot Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lotName" name="name" placeholder="Enter lot name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="lotType" class="form-label">Lot Type</label>
                        <select class="form-select" id="lotType" name="lot_type_id" required>
                            <option value="">-- Select Lot Type --</option>
                            @foreach($lotType as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="lotCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="lotCode" name="code" placeholder="e.g., LOT001">
                        <small class="text-danger" id="codeError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="lotStatus" class="form-label">Status</label>
                        <select class="form-select" id="lotStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="lotDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="lotDescription" name="description" placeholder="Enter description" rows="3"></textarea>
                        <small class="text-danger" id="descriptionError"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Lot</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection