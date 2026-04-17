@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="mb-0">Storage Time Master</h3>
                <p class="text-muted mb-0" style="font-size:13px">Manage seed storage duration / time categories</p>
            </div>
            <button class="btn btn-primary btn-sm" id="addBtn">
                <i class="ri-add-line me-1"></i> New Storage Time
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

        {{-- Validation Errors --}}
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
                @if($storageTimes->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Duration</th>
                                <th>Description</th>
                                <th width="100">Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($storageTimes as $st)
                            <tr>
                                <td class="">{{ $st->name }}</td>
                                <td>
                                    @if($st->code)
                                        <span class="badge bg-info">{{ $st->code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($st->duration_value && $st->duration_unit)
                                        <span class="badge bg-secondary">
                                            {{ $st->duration_value }} {{ ucfirst($st->duration_unit) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $st->description ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $st->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $st->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning editBtn"
                                            data-id="{{ $st->id }}"
                                            data-name="{{ $st->name }}"
                                            data-code="{{ $st->code }}"
                                            data-duration-value="{{ $st->duration_value }}"
                                            data-duration-unit="{{ $st->duration_unit }}"
                                            data-description="{{ $st->description }}"
                                            data-status="{{ $st->status }}"
                                            title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <form action="{{ route('storage-times.destroy', $st->id) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Delete this storage time?')">
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
                    <i class="ri-time-line fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No storage times found.</p>
                    <button class="btn btn-primary btn-sm" id="addEmptyBtn">
                        <i class="ri-add-line me-1"></i> Add First Storage Time
                    </button>
                </div>
                @endif
            </div>
            @if($storageTimes->hasPages())
            <div class="card-footer">
                {{ $storageTimes->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="storageTimeModal" tabindex="-1" aria-labelledby="storageTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="storageTimeForm" method="POST" action="{{ route('storage-times.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="storageTimeModalLabel">New Storage Time</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="stName" name="name"
                               placeholder="e.g. Short Term, Long Term, Medium Term" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" id="stCode" name="code"
                               placeholder="e.g. ST, LT, MT">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Duration Value</label>
                            <input type="number" class="form-control" id="stDurationValue" name="duration_value"
                                   min="1" placeholder="e.g. 6">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Duration Unit</label>
                            <select class="form-select" id="stDurationUnit" name="duration_unit">
                                <option value="">— Select —</option>
                                <option value="days">Days</option>
                                <option value="months">Months</option>
                                <option value="years">Years</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="stStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="stDescription" name="description"
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
    const modalEl = document.getElementById('storageTimeModal');
    const modal   = new bootstrap.Modal(modalEl);
    const form    = document.getElementById('storageTimeForm');

    function resetForm() {
        form.reset();
        document.getElementById('stName').value          = '';
        document.getElementById('stCode').value          = '';
        document.getElementById('stDurationValue').value = '';
        document.getElementById('stDurationUnit').value  = '';
        document.getElementById('stStatus').value        = '1';
        document.getElementById('stDescription').value   = '';
    }

    function openAdd() {
        resetForm();
        document.getElementById('storageTimeModalLabel').textContent = 'New Storage Time';
        document.getElementById('submitBtn').textContent             = 'Save';
        form.action = '{{ route("storage-times.store") }}';
        document.getElementById('formMethod').value = 'POST';
        modal.show();
    }

    document.getElementById('addBtn')?.addEventListener('click', openAdd);
    document.getElementById('addEmptyBtn')?.addEventListener('click', openAdd);

    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            resetForm();
            document.getElementById('storageTimeModalLabel').textContent = 'Edit Storage Time';
            document.getElementById('submitBtn').textContent             = 'Update';
            form.action = `{{ url('storage-times') }}/${this.dataset.id}`;
            document.getElementById('formMethod').value          = 'PUT';
            document.getElementById('stName').value              = this.dataset.name;
            document.getElementById('stCode').value              = this.dataset.code || '';
            document.getElementById('stDurationValue').value     = this.dataset.durationValue || '';
            document.getElementById('stDurationUnit').value      = this.dataset.durationUnit || '';
            document.getElementById('stStatus').value            = this.dataset.status;
            document.getElementById('stDescription').value       = this.dataset.description || '';
            modal.show();
        });
    });
});
</script>
@endpush
