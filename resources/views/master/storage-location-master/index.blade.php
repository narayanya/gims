@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Storage Location Master</h3>
                <p class="text-muted mb-0" style="font-size:13px">Manage Section, Rack, Bin and Container masters</p>
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
            <li class="nav-item"><a class="nav-link {{ request('tab','section')=='section' ? 'active' : '' }}" href="?tab=section">Section</a></li>
            <li class="nav-item"><a class="nav-link {{ request('tab')=='rack' ? 'active' : '' }}" href="?tab=rack">Rack</a></li>
            <li class="nav-item"><a class="nav-link {{ request('tab')=='bin' ? 'active' : '' }}" href="?tab=bin">Bin</a></li>
            <li class="nav-item"><a class="nav-link {{ request('tab')=='container' ? 'active' : '' }}" href="?tab=container">Container</a></li>
        </ul>

        @php $tab = request('tab','section'); @endphp

        {{-- ── SECTION ── --}}
        @if($tab === 'section')
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>Sections</strong>
                <button class="btn btn-primary btn-sm" id="addSectionBtn"><i class="ri-add-line me-1"></i>New Section</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Unit</th><th>Code</th><th>Description</th><th>Status</th><th width="100">Actions</th></tr></thead>
                    <tbody>
                        @forelse($sections as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->unit->name ?? '—' }}</td>
                            <td><span class="badge bg-info">{{ $row->code ? $row->code : '—' }}</span></td>
                            <td>{{ $row->description ?? '—' }}</td>
                            <td><span class="badge {{ $row->status ? 'bg-success' : 'bg-danger' }}">{{ $row->status ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning editBtn"
                                    data-type="section" data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-unit_id="{{ $row->unit_id }}"
                                    data-code="{{ $row->code }}" data-description="{{ $row->description }}" data-status="{{ $row->status }}">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <form action="{{ route('sections.destroy',$row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="ri-forbid-line"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty<tr><td colspan="5" class="text-center text-muted py-4">No sections found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($sections->hasPages())<div class="card-footer">{{ $sections->appends(['tab'=>'section'])->links() }}</div>@endif
        </div>
        @endif

        {{-- ── RACK ── --}}
        @if($tab === 'rack')
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>Racks</strong>
                <button class="btn btn-primary btn-sm" id="addRackBtn"><i class="ri-add-line me-1"></i>New Rack</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Code</th><th>Section</th><th>Description</th><th>Status</th><th width="100">Actions</th></tr></thead>
                    <tbody>
                        @forelse($racks as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>
                            {!! $row->code ? '<span class="badge bg-info">'.$row->code.'</span>' : '—' !!}
                        </td>
                            <td>{{ $row->section?->name ?? '—' }}</td>
                            <td>{{ $row->description ?? '—' }}</td>
                            <td><span class="badge {{ $row->status ? 'bg-success' : 'bg-danger' }}">{{ $row->status ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning editBtn"
                                    data-type="rack" data-id="{{ $row->id }}" data-name="{{ $row->name }}"
                                    data-code="{{ $row->code }}" data-section_id="{{ $row->section_id }}"
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
                    <thead class="table-light"><tr><th>Name</th><th>Code</th><th>Type</th><th>Capacity</th><th>Unit</th><th>Status</th><th width="100">Actions</th></tr></thead>
                    <tbody>
                        @forelse($containers as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('slmModal');
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);
    const form    = document.getElementById('slmForm');

    const sections  = @json($allSections);
    const racks     = @json($allRacks);
    const units = @json($units);

    function baseFields(d = {}) {
        return `
        <div class="mb-3"><label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" value="${d.name||''}" required></div>
        <div class="mb-3"><label class="form-label">Code</label>
            <input type="text" class="form-control" name="code" value="${d.code||''}"></div>
        <div class="mb-3"><label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="1" ${(d.status==1||d.status===undefined)?'selected':''}>Active</option>
                <option value="0" ${d.status==0?'selected':''}>Inactive</option>
            </select></div>
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
            <select class="form-select" name="unit_id" required>
                ${opts}
            </select>
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
            <select class="form-select" name="container_type">
                ${opts}
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Capacity</label>
            <div class="row">
                <div class="col-md-8">
                    <input type="number" class="form-control" name="capacity" step="0.01" value="${d.capacity || ''}">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="unit_id">
                        <option value="">Unit</option>
                        ${units.map(u => `
                            <option value="${u.id}" ${u.id == d.unitId ? 'selected' : ''}>
                                ${u.name} (${u.code})
                            </option>
                        `).join('')}
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
        if (type === 'section') body = unitSelect(d.unit_id) + body;
        if (type === 'rack')      body = sectionSelect(d.section_id) + body;
        if (type === 'bin')       body = rackSelect(d.rack_id) + body;
        if (type === 'container') body = containerTypeField(d) + body;

        document.getElementById('slmModalBody').innerHTML = body;
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
