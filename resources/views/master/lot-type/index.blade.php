@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">Lot Type</h3>
                <p class="text-muted mb-0" style="font-size:13px">Manage lot / batch management type categories</p>
            </div>
            <button class="btn btn-primary btn-sm" id="addBtn">
                <i class="ri-add-line me-1"></i> New Lot Type
            </button>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Table Card --}}
        <div class="card">
            <div class="card-body">
                @if($lotTypes->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th width="100">Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lotTypes as $lotType)
                            <tr>
                                <td class="">{{ $lotType->name }}</td>
                                <td>
                                    @if($lotType->code)
                                        <span class="badge bg-info">{{ $lotType->code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $lotType->description ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $lotType->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $lotType->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning editBtn"
                                            data-id="{{ $lotType->id }}"
                                            data-name="{{ $lotType->name }}"
                                            data-code="{{ $lotType->code }}"
                                            data-description="{{ $lotType->description }}"
                                            data-status="{{ $lotType->status }}"
                                            title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <form action="{{ route('lot-types.destroy', $lotType->id) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Delete this lot type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="ri-stack-line fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No lot management types found.</p>
                    <button class="btn btn-primary btn-sm" id="addEmptyBtn">
                        <i class="ri-add-line me-1"></i> Add First Lot Type
                    </button>
                </div>
                @endif
            </div>
            @if($lotTypes->hasPages())
            <div class="card-footer">
                {{ $lotTypes->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="lotTypeModal" tabindex="-1" aria-labelledby="lotTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="lotTypeForm" method="POST" action="{{ route('lot-types.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="lotTypeModalLabel">New Lot Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ltName" name="name"
                               placeholder="e.g. Production Batch, Trial Batch, Export Lot" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" id="ltCode" name="code"
                               placeholder="e.g. PB, TB, EL">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="ltStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="ltDescription" name="description"
                                  rows="3" placeholder="Optional description"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('lotTypeModal');
    const modal   = new bootstrap.Modal(modalEl);
    const form    = document.getElementById('lotTypeForm');

    function resetForm() {
        document.getElementById('ltName').value        = '';
        document.getElementById('ltCode').value        = '';
        document.getElementById('ltStatus').value      = '1';
        document.getElementById('ltDescription').value = '';
    }

    function openAdd() {
        resetForm();
        document.getElementById('lotTypeModalLabel').textContent = 'New Lot Type';
        document.getElementById('submitBtn').textContent         = 'Save';
        form.action = '{{ route("lot-types.store") }}';
        document.getElementById('formMethod').value = 'POST';
        modal.show();
    }

    document.getElementById('addBtn')?.addEventListener('click', openAdd);
    document.getElementById('addEmptyBtn')?.addEventListener('click', openAdd);

    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            resetForm();
            document.getElementById('lotTypeModalLabel').textContent = 'Edit Lot Type';
            document.getElementById('submitBtn').textContent         = 'Update';
            form.action = `{{ url('lot-types') }}/${this.dataset.id}`;
            document.getElementById('formMethod').value      = 'PUT';
            document.getElementById('ltName').value          = this.dataset.name;
            document.getElementById('ltCode').value          = this.dataset.code || '';
            document.getElementById('ltStatus').value        = this.dataset.status;
            document.getElementById('ltDescription').value   = this.dataset.description || '';
            modal.show();
        });
    });
});
</script>
@endpush
