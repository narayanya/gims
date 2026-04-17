@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="mb-0">Storage Condition Master</h3>
                <p class="text-muted mb-0" style="font-size:13px">Manage seed storage condition categories (temperature, humidity)</p>
            </div>
            <button class="btn btn-primary btn-sm" id="addBtn">
                <i class="ri-add-line me-1"></i> New Storage Condition
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
                @if($conditions->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Temperature Range</th>
                                <th>Humidity Range</th>
                                <th>Description</th>
                                <th width="100">Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conditions as $condition)
                            <tr>
                                <td class="">{{ $condition->name }}</td>
                                <td>
                                    @if($condition->code)
                                        <span class="badge bg-info">{{ $condition->code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($condition->temp_min !== null && $condition->temp_max !== null)
                                        <span class="badge bg-primary">
                                            {{ $condition->temp_min }}°C – {{ $condition->temp_max }}°C
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($condition->humidity_min !== null && $condition->humidity_max !== null)
                                        <span class="badge bg-secondary">
                                            {{ $condition->humidity_min }}% – {{ $condition->humidity_max }}%
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $condition->description ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $condition->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $condition->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning editBtn"
                                            data-id="{{ $condition->id }}"
                                            data-name="{{ $condition->name }}"
                                            data-code="{{ $condition->code }}"
                                            data-temp-min="{{ $condition->temp_min }}"
                                            data-temp-max="{{ $condition->temp_max }}"
                                            data-humidity-min="{{ $condition->humidity_min }}"
                                            data-humidity-max="{{ $condition->humidity_max }}"
                                            data-description="{{ $condition->description }}"
                                            data-status="{{ $condition->status }}"
                                            title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <form action="{{ route('storage-conditions.destroy', $condition->id) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Delete this storage condition?')">
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
                    <i class="ri-temp-cold-line fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No storage conditions found.</p>
                    <button class="btn btn-primary btn-sm" id="addEmptyBtn">
                        <i class="ri-add-line me-1"></i> Add First Storage Condition
                    </button>
                </div>
                @endif
            </div>
            @if($conditions->hasPages())
            <div class="card-footer">
                {{ $conditions->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="conditionModal" tabindex="-1" aria-labelledby="conditionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="conditionForm" method="POST" action="{{ route('storage-conditions.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="conditionModalLabel">New Storage Condition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="scName" name="name"
                               placeholder="e.g. Refrigerated, Ambient, Frozen" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" id="scCode" name="code"
                               placeholder="e.g. REF, AMB, FRZ">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Min Temperature (°C)</label>
                            <input type="number" step="0.01" class="form-control" id="scTempMin"
                                   name="temp_min" placeholder="e.g. -20">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Max Temperature (°C)</label>
                            <input type="number" step="0.01" class="form-control" id="scTempMax"
                                   name="temp_max" placeholder="e.g. 4">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Min Humidity (%)</label>
                            <input type="number" step="0.01" min="0" max="100" class="form-control"
                                   id="scHumidityMin" name="humidity_min" placeholder="e.g. 30">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Max Humidity (%)</label>
                            <input type="number" step="0.01" min="0" max="100" class="form-control"
                                   id="scHumidityMax" name="humidity_max" placeholder="e.g. 60">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="scStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="scDescription" name="description"
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
    const modalEl = document.getElementById('conditionModal');
    const modal   = new bootstrap.Modal(modalEl);
    const form    = document.getElementById('conditionForm');

    function resetForm() {
        form.reset();
        document.getElementById('scName').value        = '';
        document.getElementById('scCode').value        = '';
        document.getElementById('scTempMin').value     = '';
        document.getElementById('scTempMax').value     = '';
        document.getElementById('scHumidityMin').value = '';
        document.getElementById('scHumidityMax').value = '';
        document.getElementById('scStatus').value      = '1';
        document.getElementById('scDescription').value = '';
    }

    function openAdd() {
        resetForm();
        document.getElementById('conditionModalLabel').textContent = 'New Storage Condition';
        document.getElementById('submitBtn').textContent           = 'Save';
        form.action = '{{ route("storage-conditions.store") }}';
        document.getElementById('formMethod').value = 'POST';
        modal.show();
    }

    document.getElementById('addBtn')?.addEventListener('click', openAdd);
    document.getElementById('addEmptyBtn')?.addEventListener('click', openAdd);

    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            resetForm();
            document.getElementById('conditionModalLabel').textContent = 'Edit Storage Condition';
            document.getElementById('submitBtn').textContent           = 'Update';
            form.action = `{{ url('storage-conditions') }}/${this.dataset.id}`;
            document.getElementById('formMethod').value        = 'PUT';
            document.getElementById('scName').value            = this.dataset.name;
            document.getElementById('scCode').value            = this.dataset.code || '';
            document.getElementById('scTempMin').value         = this.dataset.tempMin || '';
            document.getElementById('scTempMax').value         = this.dataset.tempMax || '';
            document.getElementById('scHumidityMin').value     = this.dataset.humidityMin || '';
            document.getElementById('scHumidityMax').value     = this.dataset.humidityMax || '';
            document.getElementById('scStatus').value          = this.dataset.status;
            document.getElementById('scDescription').value     = this.dataset.description || '';
            modal.show();
        });
    });
});
</script>
@endpush
