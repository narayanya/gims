@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        @php $tab = request('tab', 'rack'); @endphp

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
            <div>
                <h3 class="text-xl font-bold">Storage Location Master</h3>
                <p class="text-muted mb-0" style="font-size:13px">Manage Rack, Bin and Container masters</p>
            </div>
            <div class="d-flex gap-2">
                @if($tab === 'rack')
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#importRackModal">
                        <i class="ri-upload-line me-1"></i>Import Racks
                    </button>
                @elseif($tab === 'bin')
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#importBinModal">
                        <i class="ri-upload-line me-1"></i>Import Bins
                    </button>
                @elseif($tab === 'container')
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#importContainerModal">
                        <i class="ri-upload-line me-1"></i>Import Containers
                    </button>
                @endif
                <a href="{{ route('storage-location-master.export') }}" class="btn btn-sm btn-success">
                    <i class="ri-download-line me-1"></i>Export
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="slmTabs">
            <!--<li class="nav-item"><a class="nav-link {{ request('tab','section')=='section' ? 'active' : '' }}" href="?tab=section">Section</a></li>-->
            <li class="nav-item"><a class="nav-link {{ $tab=='rack' ? 'active' : '' }}" href="?tab=rack">Rack</a></li>
            <li class="nav-item"><a class="nav-link {{ $tab=='bin' ? 'active' : '' }}" href="?tab=bin">Bin</a></li>
            <li class="nav-item"><a class="nav-link {{ $tab=='container' ? 'active' : '' }}" href="?tab=container">Container</a></li>
        </ul>

        {{-- ── SECTION ── --}}
        {{-- @if($tab === 'section')
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>Sections</strong>
                <button class="btn btn-primary btn-sm" id="addSectionBtn"><i class="ri-add-line me-1"></i>New Section</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Unit</th><th>Warehouse</th><th>Storage</th><th>Code</th><th>Description</th><th>Status</th><th width="100">Actions</th></tr></thead>
                    <tbody>
                        @forelse($sections as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->unit->name ?? '—' }}</td>
                            <td>{{ $row->storage->warehouse->name ?? '—' }}</td>
                            <td>{{ $row->storage->name ?? '—' }}</td>
                            <td><span class="badge bg-info">{{ $row->code ? $row->code : '—' }}</span></td>
                            <td>{{ $row->description ?? '—' }}</td>
                            <td><span class="badge {{ $row->status ? 'bg-success' : 'bg-danger' }}">{{ $row->status ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning editBtn"
                                    data-type="section" data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-unit_id="{{ $row->unit_id }}"
                                    data-storage_id="{{ $row->storage_id }}" data-warehouse_id="{{ $row->storage->warehouse_id ?? '' }}"
                                    data-code="{{ $row->code }}" data-description="{{ $row->description }}" data-status="{{ $row->status }}">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <form action="{{ route('sections.destroy',$row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="ri-forbid-line"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty<tr><td colspan="8" class="text-center text-muted py-4">No sections found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($sections->hasPages())<div class="card-footer">{{ $sections->appends(['tab'=>'section'])->links() }}</div>@endif
        </div>
        @endif--}}

        {{-- ── RACK ── --}}
        @if($tab === 'rack')
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>Racks</strong>
                <button class="btn btn-primary btn-sm" id="addRackBtn"><i class="ri-add-line me-1"></i>New Rack</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Code</th><th>Warehouse</th><th>Storage</th><th>Description</th><th>Status</th><th width="100">Actions</th></tr></thead>
                    <tbody>
                        @forelse($racks as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>
                            {!! $row->code ? '<span class="badge bg-info">'.$row->code.'</span>' : '—' !!}
                        </td>
                            <td>{{ $row->storage->warehouse->name ?? '—' }}</td>
                            <td>{{ $row->storage->name ?? '—' }}</td>
                            <td>{{ $row->description ?? '—' }}</td>
                            <td><span class="badge {{ $row->status ? 'bg-success' : 'bg-danger' }}">{{ $row->status ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning editBtn"
                                    data-type="rack" data-id="{{ $row->id }}" data-name="{{ $row->name }}"
                                    data-code="{{ $row->code }}"
                                    data-warehouse_id="{{ $row->warehouse_id }}"
                                    data-storage_id="{{ $row->storage_id }}"
                                    data-description="{{ $row->description }}" data-status="{{ $row->status }}">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <form action="{{ route('racks.destroy',$row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="ri-forbid-line"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty<tr><td colspan="6" class="text-center text-muted py-4">No racks found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap p-2">
                    <div>
                        Showing {{ $racks->firstItem() }} to {{ $racks->lastItem() }}
                        of {{ $racks->total() }} results
                    </div>

                    <div>
                        {{ $racks->links() }}
                    </div>
                </div>
            </div>
            @if($racks->hasPages())<div class="card-footer">{{ $racks->appends(['tab'=>'rack'])->links() }}</div>@endif
        </div>
        @endif

        {{-- ── BIN ── --}}
        @if($tab === 'bin')
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>Bins</strong>
                <button class="btn btn-primary btn-sm" id="addBinBtn"><i class="ri-add-line me-1"></i>New Bin</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Code</th><th>Rack</th><th>Description</th><th>Status</th><th width="100">Actions</th></tr></thead>
                    <tbody>
                        @forelse($bins as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{!! $row->code ? '<span class="badge bg-info">'.$row->code.'</span>' : '—' !!}</td>
                            <td>{{ $row->rack?->name ?? '—' }}</td>
                            <td>{{ $row->description ?? '—' }}</td>
                            <td><span class="badge {{ $row->status ? 'bg-success' : 'bg-danger' }}">{{ $row->status ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning editBtn"
                                    data-type="bin" data-id="{{ $row->id }}" data-name="{{ $row->name }}"
                                    data-code="{{ $row->code }}" data-rack_id="{{ $row->rack_id }}"
                                    data-description="{{ $row->description }}" data-status="{{ $row->status }}">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <form action="{{ route('bins.destroy',$row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="ri-forbid-line"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty<tr><td colspan="6" class="text-center text-muted py-4">No bins found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap p-2">
                    <div>
                        Showing {{ $bins->firstItem() }} to {{ $bins->lastItem() }}
                        of {{ $bins->total() }} results
                    </div>

                    <div>
                        {{ $bins->links() }}
                    </div>
                </div>
            @if($bins->hasPages())<div class="card-footer">{{ $bins->appends(['tab'=>'bin'])->links() }}</div>@endif
        </div>
        @endif

        {{-- ── CONTAINER ── --}}
        @if($tab === 'container')
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>Containers</strong>
                <button class="btn btn-primary btn-sm" id="addContainerBtn"><i class="ri-add-line me-1"></i>New Container</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Dimensions</th><th>Code</th><th>Type</th><th>Capacity(No of pouches)</th><th>Unit</th><th>Status</th><th width="100">Actions</th></tr></thead>
                    <tbody>
                        @forelse($containers as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{!! $row->length ? $row->length.' x '.$row->width.' x '.$row->height.' '.$row->dimension_unit : '—' !!}</td>
                            <td>{!! $row->code ? '<span class="badge bg-info">'.$row->code.'</span>' : '—' !!}</td>
                            <td>{{ $row->container_type ?? '—' }}</td>
                            <td>{{ $row->capacity ?? '—' }}</td>
                            <td>{{ $row->unit->name ?? '—' }}</td>
                            <td><span class="badge {{ $row->status ? 'bg-success' : 'bg-danger' }}">{{ $row->status ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning editBtn"
                                    data-type="container" data-id="{{ $row->id }}" data-name="{{ $row->name }}"
                                    data-code="{{ $row->code }}" data-container_type="{{ $row->container_type }}"
                                    data-capacity="{{ $row->capacity }}" data-description="{{ $row->description }}" data-status="{{ $row->status }}" data-unit_id="{{ $row->unit_id }}">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <form action="{{ route('containers.destroy',$row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="ri-forbid-line"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty<tr><td colspan="6" class="text-center text-muted py-4">No containers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap p-2">
                    <div>
                        Showing {{ $containers->firstItem() }} to {{ $containers->lastItem() }}
                        of {{ $containers->total() }} results
                    </div>

                    <div>
                        {{ $containers->links() }}
                    </div>
                </div>
            @if($containers->hasPages())<div class="card-footer">{{ $containers->appends(['tab'=>'container'])->links() }}</div>@endif
        </div>
        @endif

    </div>
</div>
@endsection

@section('modals')
{{-- Universal Modal --}}
<div class="modal fade" id="slmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="slmForm" method="POST" action="#">
                @csrf
                <input type="hidden" name="_method" id="slmMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="slmModalLabel">New Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="slmModalBody">
                    {{-- Fields injected by JS --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="slmSubmitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="modal fade" id="importRackModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Racks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('racks.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload CSV / Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <p class="text-muted small mb-0">
                            Required columns: <b>name</b><br>
                            Optional: <b>code, warehouse, storage, description, status</b>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importBinModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Bins</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('bins.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload CSV / Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <p class="text-muted small mb-0">
                            Required columns: <b>name</b><br>
                            Optional: <b>code, rack, description, status</b>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importContainerModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Containers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('containers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload CSV / Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <p class="text-muted small mb-0">
                            Required columns: <b>name</b><br>
                            Optional: <b>code, container_type, capacity_no_of_pouches, unit, length, width, height, dimension_unit, description, status</b>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('slmModal');
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);
    const form    = document.getElementById('slmForm');

    const sections    = @json($allSections);
    const racks       = @json($allRacks);
    const units       = @json($units);
    const warehouses  = @json($warehouses);
    const allStorages = @json($allStorages);

    function baseFields(d = {}) {
        return `
        <div class="mb-3"><label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" value="${d.name||''}" required></div>
            <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Code</label>
            <input type="text" class="form-control" name="code" value="${d.code||''}"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="">Select Status</option>
                <option value="1" ${(d.status==1||d.status===undefined)?'selected':''}>Active</option>
                <option value="0" ${d.status==0?'selected':''}>Inactive</option>
            </select>
        </div>
        </div>
        <div class="mb-3"><label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="2">${d.description||''}</textarea></div>`;
    }

    function unitSelect(val='') {
        let opts = '<option value="">— Select Unit —</option>';
        units.forEach(u => {
            opts += `<option value="${u.id}" ${u.id==val?'selected':''}>${u.name}</option>`;
        });
        return `
        <div class="mb-3">
            <label class="form-label">Unit <span class="text-danger">*</span></label>
            <select class="form-select" name="unit_id" required>${opts}</select>
        </div>`;
    }

    function warehouseStorageFields(warehouseVal='', storageVal='') {
        let whOpts = '<option value="">— Select Warehouse —</option>';
        warehouses.forEach(w => {
            whOpts += `<option value="${w.id}" ${w.id==warehouseVal?'selected':''}>${w.name}</option>`;
        });

        let filteredStorages = warehouseVal
            ? allStorages.filter(s => s.warehouse_id == warehouseVal)
            : allStorages;
        let stOpts = '<option value="">— Select Storage —</option>';
        filteredStorages.forEach(s => {
            stOpts += `<option value="${s.id}" ${s.id==storageVal?'selected':''}>${s.name}</option>`;
        });

        return `
        <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Warehouse</label>
            <select class="form-select" id="sectionWarehouseSelect" name="warehouse_id">
                ${whOpts}
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Storage</label>
            <select class="form-select" id="sectionStorageSelect" name="storage_id">
                ${stOpts}
            </select>
        </div>
        </div>`;
    }

    function sectionSelect(val='') {
        let opts = '<option value="">— None —</option>';
        sections.forEach(s => opts += `<option value="${s.id}" ${s.id==val?'selected':''}>${s.name}</option>`);
        return `<div class="mb-3"><label class="form-label">Section</label><select class="form-select" name="section_id">${opts}</select></div>`;
    }

    function rackSelect(val='') {
        let opts = '<option value="">— None —</option>';
        racks.forEach(r => opts += `<option value="${r.id}" ${r.id==val?'selected':''}>${r.name}</option>`);
        return `<div class="mb-3"><label class="form-label">Rack</label><select class="form-select" name="rack_id">${opts}</select></div>`;
    }

    function containerTypeField(d = {}) {
        const types = ['Packet','Pouch','Box','Jar','Envelope','Bag'];
        let opts = '<option value="">— Select —</option>';
        types.forEach(t => {
            opts += `<option value="${t}" ${t == d.container_type ? 'selected' : ''}>${t}</option>`;
        });
        return `
        <div class="mb-3">
            <label class="form-label">Container Type</label>
            <select class="form-select" name="container_type">${opts}</select>
        </div>
        <div class="mb-2">
                        <label class="form-label">Dimensions</label>
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text text-muted" style="font-size:11px;">L</span>
                                    <input type="number" class="form-control" id="BLength" name="length"
                                           placeholder="Length" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-auto text-muted fw-bold">×</div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text text-muted" style="font-size:11px;">W</span>
                                    <input type="number" class="form-control" id="BWidth" name="width"
                                           placeholder="Width" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-auto text-muted fw-bold">×</div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text text-muted" style="font-size:11px;">H</span>
                                    <input type="number" class="form-control" id="BHeight" name="height"
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
        <div class="mb-3">
            <label class="form-label">Capacity(No. of Pouches)</label>
            <div class="row">
                <div class="col-md-8">
                    <input type="number" class="form-control" name="capacity" step="0.01" value="${d.capacity || ''}">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="unit_id">
                        <option value="">Unit</option>
                        ${units.map(u => `<option value="${u.id}" ${u.id == d.unit_id ? 'selected' : ''}>${u.name} (${u.code})</option>`).join('')}
                    </select>
                </div>
            </div>
        </div>`;
    }

    function openModal(type, label, action, method, d = {}) {
        document.getElementById('slmModalLabel').textContent = label;
        document.getElementById('slmSubmitBtn').textContent  = method === 'POST' ? 'Save' : 'Update';
        document.getElementById('slmMethod').value           = method;
        form.action = action;

        let body = baseFields(d);
        if (type === 'section') {
            body = unitSelect(d.unit_id) + warehouseStorageFields(d.warehouse_id, d.storage_id) + body;
        }
        if (type === 'rack')      body = warehouseStorageFields(d.warehouse_id, d.storage_id) + body;
        if (type === 'bin')       body = rackSelect(d.rack_id) + body;
        if (type === 'container') body = containerTypeField(d) + body;

        document.getElementById('slmModalBody').innerHTML = body;

        // Warehouse → Storage cascade for rack modal
        if (type === 'rack') {
            const whSel = document.getElementById('sectionWarehouseSelect');
            const stSel = document.getElementById('sectionStorageSelect');
            if (whSel && stSel) {
                whSel.addEventListener('change', function () {
                    const wid = this.value;
                    const filtered = wid ? allStorages.filter(s => s.warehouse_id == wid) : [];
                    stSel.innerHTML = '<option value="">— Select Storage —</option>';
                    filtered.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.name;
                        stSel.appendChild(opt);
                    });
                });
            }
        }

        // fix capacity field for container edit
        if (type === 'container' && d.capacity) {
            const cap = document.querySelector('[name="capacity"]');
            if (cap) cap.value = d.capacity;
        }
        modal.show();
    }

    // Add buttons
    document.getElementById('addSectionBtn')?.addEventListener('click',   () => openModal('section',   'New Section',   '{{ route("sections.store") }}',   'POST'));
    document.getElementById('addRackBtn')?.addEventListener('click',      () => openModal('rack',      'New Rack',      '{{ route("racks.store") }}',      'POST'));
    document.getElementById('addBinBtn')?.addEventListener('click',       () => openModal('bin',       'New Bin',       '{{ route("bins.store") }}',       'POST'));
    document.getElementById('addContainerBtn')?.addEventListener('click', () => openModal('container', 'New Container', '{{ route("containers.store") }}', 'POST'));

    // Edit buttons
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d    = this.dataset;
            const type = d.type;
            const routes = {
                section:   `{{ url('sections') }}/${d.id}`,
                rack:      `{{ url('racks') }}/${d.id}`,
                bin:       `{{ url('bins') }}/${d.id}`,
                container: `{{ url('containers') }}/${d.id}`,
            };
            const labels = { section:'Edit Section', rack:'Edit Rack', bin:'Edit Bin', container:'Edit Container' };
            openModal(type, labels[type], routes[type], 'PUT', d);
        });
    });
});
</script>
@endpush
