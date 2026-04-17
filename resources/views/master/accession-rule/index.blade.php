@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Accession Rule Master</h3>
                <p class="text-muted mb-0" style="font-size:13px">Define rules and constraints for accession entries</p>
            </div>
            <button class="btn btn-primary btn-sm" id="addBtn">
                <i class="ri-add-line me-1"></i> New Rule
            </button>
        </div>

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
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($rules->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Rule Type</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Unit</th>
                                <th>Mandatory</th>
                                <th>Status</th>
                                <th width="110">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rules as $rule)
                            <tr>
                                <td>{{ $loop->iteration + ($rules->currentPage() - 1) * $rules->perPage() }}</td>
                                <td>{{ $rule->name }}</td>
                                <td>
                                    @if($rule->code)
                                        <span class="badge bg-info">{{ $rule->code }}</span>
                                    @else <span class="text-muted">—</span> @endif
                                </td>
                                <td>{{ $rule->rule_type ?? '—' }}</td>
                                <td>{{ $rule->min_value ?? '—' }}</td>
                                <td>{{ $rule->max_value ?? '—' }}</td>
                                <td>{{ $rule->unit ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $rule->is_mandatory ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                        {{ $rule->is_mandatory ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $rule->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $rule->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning editBtn"
                                        data-id="{{ $rule->id }}"
                                        data-name="{{ $rule->name }}"
                                        data-code="{{ $rule->code }}"
                                        data-rule_type="{{ $rule->rule_type }}"
                                        data-description="{{ $rule->description }}"
                                        data-min_value="{{ $rule->min_value }}"
                                        data-max_value="{{ $rule->max_value }}"
                                        data-unit="{{ $rule->unit }}"
                                        data-is_mandatory="{{ $rule->is_mandatory }}"
                                        data-status="{{ $rule->status }}"
                                        title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <form action="{{ route('accession-rules.destroy', $rule->id) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Delete this rule?')">
                                        @csrf @method('DELETE')
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
                    <i class="ri-file-list-3-line fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No accession rules found.</p>
                    <button class="btn btn-primary btn-sm" id="addEmptyBtn">
                        <i class="ri-add-line me-1"></i> Add First Rule
                    </button>
                </div>
                @endif
            </div>
            @if($rules->hasPages())
            <div class="card-footer">{{ $rules->links() }}</div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="ruleModal" tabindex="-1" aria-labelledby="ruleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="ruleForm" method="POST" action="{{ route('accession-rules.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="ruleModalLabel">New Accession Rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Rule Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="rName" name="name"
                                   placeholder="e.g. Minimum Germination Rate" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" id="rCode" name="code"
                                   placeholder="e.g. MGR">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Rule Type</label>
                            <select class="form-select" id="rRuleType" name="rule_type">
                                <option value="">Select Type</option>
                                <option value="Quantity">Quantity</option>
                                <option value="Quality">Quality</option>
                                <option value="Storage">Storage</option>
                                <option value="General">General</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Min Value</label>
                            <input type="text" class="form-control" id="rMinValue" name="min_value"
                                   placeholder="e.g. 80">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Max Value</label>
                            <input type="text" class="form-control" id="rMaxValue" name="max_value"
                                   placeholder="e.g. 100">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Unit</label>
                            <input type="text" class="form-control" id="rUnit" name="unit"
                                   placeholder="e.g. %, kg, days">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Mandatory</label>
                            <select class="form-select" id="rMandatory" name="is_mandatory">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="rStatus" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="rDescription" name="description"
                                      rows="3" placeholder="Optional description of this rule"></textarea>
                        </div>

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
    const modal = new bootstrap.Modal(document.getElementById('ruleModal'));
    const form  = document.getElementById('ruleForm');

    function resetForm() {
        ['rName','rCode','rMinValue','rMaxValue','rUnit','rDescription'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('rRuleType').value  = '';
        document.getElementById('rMandatory').value = '0';
        document.getElementById('rStatus').value    = '1';
    }

    function openAdd() {
        resetForm();
        document.getElementById('ruleModalLabel').textContent = 'New Accession Rule';
        document.getElementById('submitBtn').textContent      = 'Save';
        form.action = '{{ route("accession-rules.store") }}';
        document.getElementById('formMethod').value = 'POST';
        modal.show();
    }

    document.getElementById('addBtn')?.addEventListener('click', openAdd);
    document.getElementById('addEmptyBtn')?.addEventListener('click', openAdd);

    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            resetForm();
            const d = this.dataset;
            document.getElementById('ruleModalLabel').textContent = 'Edit Accession Rule';
            document.getElementById('submitBtn').textContent      = 'Update';
            form.action = `{{ url('accession-rules') }}/${d.id}`;
            document.getElementById('formMethod').value  = 'PUT';
            document.getElementById('rName').value       = d.name;
            document.getElementById('rCode').value       = d.code || '';
            document.getElementById('rRuleType').value   = d.rule_type || '';
            document.getElementById('rMinValue').value   = d.min_value || '';
            document.getElementById('rMaxValue').value   = d.max_value || '';
            document.getElementById('rUnit').value       = d.unit || '';
            document.getElementById('rMandatory').value  = d.is_mandatory;
            document.getElementById('rStatus').value     = d.status;
            document.getElementById('rDescription').value = d.description || '';
            modal.show();
        });
    });
});
</script>
@endpush
