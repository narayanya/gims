@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                        Pouch Master
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View and manage pouch master data</p>
                </div>
                <button class="btn btn-sm btn-primary" id="addPouchBtn">
                    <i class="ri-add-line me-1"></i>New Pouch
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($pouches->count())
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Dimensions (L × W × H)</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pouches as $i => $pouch)
                                <tr data-id="{{ $pouch->id }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-500">{{ $pouch->name }}</td>
                                    <td>
                                        @if($pouch->code)
                                            <span class="badge bg-info">{{ $pouch->code }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pouch->length || $pouch->width || $pouch->height)
                                            <span class="text-dark">
                                                {{ $pouch->length ?? '—' }}
                                                × {{ $pouch->width ?? '—' }}
                                                × {{ $pouch->height ?? '—' }}
                                            </span>
                                            <span class="text-muted ms-1">{{ $pouch->dimension_unit }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $pouch->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $pouch->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $pouch->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning editPouchBtn"
                                                data-id="{{ $pouch->id }}"
                                                data-name="{{ $pouch->name }}"
                                                data-code="{{ $pouch->code }}"
                                                data-length="{{ $pouch->length }}"
                                                data-width="{{ $pouch->width }}"
                                                data-height="{{ $pouch->height }}"
                                                data-dimension_unit="{{ $pouch->dimension_unit }}"
                                                data-status="{{ $pouch->status }}"
                                                data-description="{{ $pouch->description }}">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <form action="{{ route('pouches.destroy', $pouch) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Deactivate this pouch?');">
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
                    <p class="text-slate-500">No pouches found. <a href="#" id="addPouchBtnEmpty">Create one</a></p>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@section('modals')
<!-- Pouch Modal -->
<div class="modal fade" id="pouchModal" tabindex="-1" role="dialog" aria-labelledby="pouchModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pouchModalLabel">Add Pouch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="pouchForm" method="POST" action="{{ route('pouches.store') }}">
                @csrf
                <div class="modal-body">

                    {{-- Name & Code --}}
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="pouchName" class="form-label">Pouch Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pouchName" name="name" placeholder="Enter pouch name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="pouchCode" class="form-label">Code</label>
                            <input type="text" class="form-control" id="pouchCode" name="code" placeholder="e.g., SP01">
                        </div>
                    </div>

                    {{-- Dimensions --}}
                    <div class="mb-2">
                        <label class="form-label">Dimensions</label>
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text text-muted" style="font-size:11px;">L</span>
                                    <input type="number" class="form-control" id="pouchLength" name="length"
                                           placeholder="Length" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-auto text-muted fw-bold">×</div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text text-muted" style="font-size:11px;">W</span>
                                    <input type="number" class="form-control" id="pouchWidth" name="width"
                                           placeholder="Width" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-auto text-muted fw-bold">×</div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text text-muted" style="font-size:11px;">H</span>
                                    <input type="number" class="form-control" id="pouchHeight" name="height"
                                           placeholder="Height" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-auto">
                                <select class="form-select" id="pouchDimensionUnit" name="dimension_unit" style="min-width:75px;">
                                    <option value="cm">cm</option>
                                    <option value="mm">mm</option>
                                    <option value="inch">inch</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="pouchStatus" class="form-label">Status</label>
                        <select class="form-select" id="pouchStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="pouchDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="pouchDescription" name="description" placeholder="Enter description" rows="2"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="pouchSubmitBtn">Add Pouch</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('pouchModal');
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);

    function openAddModal() {
        document.getElementById('pouchForm').reset();
        document.getElementById('pouchForm').action    = "{{ route('pouches.store') }}";
        document.getElementById('pouchModalLabel').textContent = 'Add Pouch';
        document.getElementById('pouchSubmitBtn').textContent  = 'Add Pouch';
        document.getElementById('pouchStatus').value          = '1';
        document.getElementById('pouchDimensionUnit').value   = 'cm';
        const ex = document.getElementById('pouchForm').querySelector('input[name="_method"]');
        if (ex) ex.remove();
        modal.show();
    }

    document.getElementById('addPouchBtn').addEventListener('click', openAddModal);

    const emptyBtn = document.getElementById('addPouchBtnEmpty');
    if (emptyBtn) emptyBtn.addEventListener('click', function (e) { e.preventDefault(); openAddModal(); });

    // ── Edit ──
    document.querySelectorAll('.editPouchBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('pouchName').value          = d.name           || '';
            document.getElementById('pouchCode').value          = d.code           || '';
            document.getElementById('pouchLength').value        = d.length         || '';
            document.getElementById('pouchWidth').value         = d.width          || '';
            document.getElementById('pouchHeight').value        = d.height         || '';
            document.getElementById('pouchDimensionUnit').value = d.dimension_unit || 'cm';
            document.getElementById('pouchStatus').value        = d.status         ?? '1';
            document.getElementById('pouchDescription').value   = d.description    || '';

            document.getElementById('pouchModalLabel').textContent = 'Edit Pouch';
            document.getElementById('pouchSubmitBtn').textContent  = 'Update Pouch';

            const form = document.getElementById('pouchForm');
            const ex = form.querySelector('input[name="_method"]');
            if (ex) ex.remove();
            const m = document.createElement('input');
            m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
            form.appendChild(m);
            form.action = `/pouches/${d.id}`;
            modal.show();
        });
    });
});
</script>
@endpush
