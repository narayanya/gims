@extends('layouts.app')

@section('content')
<div class="col-12">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
        <div>
            <h3 class="text-xl font-bold">Permission Management</h3>
            <p class="text-muted mb-0" style="font-size:13px">Configure module, sub-action and menu access per role</p>
        </div>
        <a href="{{ route('settings') }}" class="btn btn-sm btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $groupLabels = [
            'core'     => ['label' => 'Core Actions',  'icon' => 'ri-settings-3-line',    'color' => 'primary'],
            'data'     => ['label' => 'Data Actions',  'icon' => 'ri-database-2-line',    'color' => 'info'],
            'workflow' => ['label' => 'Workflow',       'icon' => 'ri-flow-chart',         'color' => 'warning'],
            'access'   => ['label' => 'Access',         'icon' => 'ri-key-line',           'color' => 'success'],
            'admin'    => ['label' => 'Admin',          'icon' => 'ri-shield-user-line',   'color' => 'danger'],
        ];
        $moduleIcons = [
            'accession' => 'ri-seedling-line',
            'lot'       => 'ri-stack-line',
            'crop'      => 'ri-plant-line',
            'storage'   => 'ri-archive-line',
            'request'   => 'ri-file-list-3-line',
            'dispatch'  => 'ri-truck-line',
            'report'    => 'ri-bar-chart-line',
            'menu'      => 'ri-menu-line',
            'settings'  => 'ri-settings-line',
        ];
    @endphp

    <form method="POST" action="{{ route('settings.permission.save') }}" id="permForm">
        @csrf

        <div class="row g-3">

            {{-- ── Left: Role Selector ── --}}
            <div class="col-lg-3 col-md-4">
                <div class="card shadow-sm sticky-top" style="top:80px;">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="ri-shield-user-line me-2"></i>Roles</h6>
                    </div>
                    <div class="list-group list-group-flush" id="roleList">
                        @foreach($roles as $role)
                        <button type="button"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center role-tab {{ $loop->first ? 'active' : '' }}"
                            data-role="{{ $role->id }}"
                            data-slug="{{ $role->slug }}">
                            <div>
                                <div class="fw-semibold">{{ $role->name }}</div>
                                <small class="text-muted">{{ $role->slug }}</small>
                            </div>
                            @if($role->slug === 'super-admin')
                                <span class="badge bg-danger">All Access</span>
                            @else
                                <span class="badge bg-primary rounded-pill perm-count-badge" data-role="{{ $role->id }}">
                                    {{ $role->permissions->count() }}
                                </span>
                            @endif
                        </button>
                        @endforeach
                    </div>
                    <div class="card-footer d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Save Permissions
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Right: Permission Panels ── --}}
            <div class="col-lg-9 col-md-8">

                @foreach($roles as $role)
                @php
                    $rolePerms    = $role->permissions->pluck('slug')->toArray();
                    $isSuperAdmin = $role->slug === 'super-admin';
                @endphp

                <div class="role-panel {{ $loop->first ? '' : 'd-none' }}" data-role="{{ $role->id }}">

                    {{-- Super Admin Banner --}}
                    @if($isSuperAdmin)
                    <div class="card border-danger shadow-sm mb-3">
                        <div class="card-body d-flex align-items-center gap-3 py-3">
                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                <i class="ri-shield-star-line text-danger fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-danger fw-bold">Super Admin — Full Access</h6>
                                <small class="text-muted">Unrestricted access to all modules, actions and menus. Not configurable.</small>
                            </div>
                            <span class="badge bg-danger fs-6 px-3 py-2">All Access</span>
                        </div>
                    </div>

                    {{-- Show all as locked --}}
                    @foreach($modules as $module => $groups)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light py-2 d-flex align-items-center gap-2">
                            <i class="{{ $moduleIcons[$module] ?? 'ri-apps-line' }} text-primary"></i>
                            <span class="fw-semibold text-capitalize">{{ $module }}</span>
                            <span class="badge bg-success ms-auto">All Enabled</span>
                        </div>
                        <div class="card-body py-2">
                            @foreach($groups as $group => $actions)
                            @php $gl = $groupLabels[$group] ?? ['label'=>$group,'icon'=>'ri-circle-line','color'=>'secondary']; @endphp
                            <div class="mb-2">
                                <small class="text-muted fw-semibold d-block mb-1">
                                    <i class="{{ $gl['icon'] }} me-1 text-{{ $gl['color'] }}"></i>{{ $gl['label'] }}
                                </small>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($actions as $action)
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <small class="text-muted text-capitalize" style="font-size:10px;">{{ $action }}</small>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" checked disabled style="width:2.2em;height:1.2em;opacity:0.7;">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    {{-- Hidden inputs to save all perms for super-admin --}}
                    @foreach($permissions as $perm)
                        <input type="hidden" name="permissions[{{ $role->id }}][]" value="{{ $perm->slug }}">
                    @endforeach

                    @else
                    {{-- Normal Role --}}

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-bold">
                            <i class="ri-settings-3-line me-1"></i>{{ $role->name }} — Permissions
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success select-all-role" data-role="{{ $role->id }}">
                                <i class="ri-check-double-line me-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger clear-role" data-role="{{ $role->id }}">
                                <i class="ri-close-line me-1"></i>Clear All
                            </button>
                        </div>
                    </div>

                    @foreach($modules as $module => $groups)
                    @php
                        $allSlugs = collect($groups)->flatten()->map(fn($a) => $module.'.'.$a)->toArray();
                        $checkedCount = count(array_intersect($allSlugs, $rolePerms));
                        $totalCount   = count($allSlugs);
                        $moduleAllChecked = $checkedCount === $totalCount;
                        $modulePartial    = $checkedCount > 0 && $checkedCount < $totalCount;
                    @endphp

                    <div class="card shadow-sm mb-3 module-card" data-role="{{ $role->id }}" data-module="{{ $module }}">
                        {{-- Module Header --}}
                        <div class="card-header py-2 d-flex align-items-center gap-2">
                            <i class="{{ $moduleIcons[$module] ?? 'ri-apps-line' }} text-primary fs-5"></i>
                            <span class="fw-semibold text-capitalize flex-grow-1">{{ $module }}</span>

                            @if($modulePartial)
                            <span class="badge bg-warning text-dark module-partial-badge" style="font-size:10px;">Partial</span>
                            @endif

                            {{-- Module-level master toggle --}}
                            <div class="d-flex align-items-center gap-2 ms-2">
                                <small class="text-muted" style="font-size:11px;">All</small>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input module-toggle"
                                        type="checkbox"
                                        data-role="{{ $role->id }}"
                                        data-module="{{ $module }}"
                                        style="width:2.2em;height:1.2em;"
                                        {{ $moduleAllChecked ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        {{-- Groups --}}
                        <div class="card-body py-2">
                            @foreach($groups as $group => $actions)
                            @php $gl = $groupLabels[$group] ?? ['label'=>$group,'icon'=>'ri-circle-line','color'=>'secondary']; @endphp
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="{{ $gl['icon'] }} text-{{ $gl['color'] }} small"></i>
                                    <small class="fw-semibold text-muted text-uppercase" style="font-size:10px;letter-spacing:.5px;">{{ $gl['label'] }}</small>
                                    <hr class="flex-grow-1 my-0 ms-1">
                                </div>
                                <div class="d-flex flex-wrap gap-3 ps-2">
                                    @foreach($actions as $action)
                                    @php
                                        $slug    = $module . '.' . $action;
                                        $checked = in_array($slug, $rolePerms);
                                        $exists  = $permissions->where('slug', $slug)->count();
                                    @endphp
                                    <div class="d-flex flex-column align-items-center gap-1" style="min-width:60px;">
                                        <small class="text-muted text-capitalize text-center" style="font-size:10px;line-height:1.2;">{{ str_replace('_',' ',$action) }}</small>
                                        @if($exists)
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input perm-check action-toggle"
                                                type="checkbox"
                                                name="permissions[{{ $role->id }}][]"
                                                value="{{ $slug }}"
                                                data-role="{{ $role->id }}"
                                                data-module="{{ $module }}"
                                                style="width:2.2em;height:1.2em;"
                                                {{ $checked ? 'checked' : '' }}>
                                        </div>
                                        @else
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" disabled style="width:2.2em;height:1.2em;opacity:0.2;" title="Permission not defined">
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @endif
                </div>
                @endforeach

            </div>
        </div>
    </form>

</div>

<style>
.role-tab.active {
    background-color: #0d6efd !important;
    color: #fff !important;
    border-color: #0d6efd !important;
}
.role-tab.active small, .role-tab.active .text-muted { color: rgba(255,255,255,.75) !important; }
.form-check-input:checked { background-color: #198754; border-color: #198754; }
.module-card { transition: box-shadow .15s; }
.module-card:hover { box-shadow: 0 0 0 2px #0d6efd33 !important; }
.module-card .card-header { background: #f8f9fa; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Role tab switching ──────────────────────────────────────────────
    document.querySelectorAll('.role-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const roleId = this.dataset.role;
            document.querySelectorAll('.role-panel').forEach(p => p.classList.add('d-none'));
            document.querySelector(`.role-panel[data-role="${roleId}"]`).classList.remove('d-none');
        });
    });

    // ── Module master toggle ────────────────────────────────────────────
    document.querySelectorAll('.module-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const { role, module } = this.dataset;
            document.querySelectorAll(`.action-toggle[data-role="${role}"][data-module="${module}"]`)
                .forEach(cb => cb.checked = this.checked);
            updateBadge(role);
            updateModulePartial(role, module);
        });
    });

    // ── Individual action toggle ────────────────────────────────────────
    document.querySelectorAll('.action-toggle').forEach(cb => {
        cb.addEventListener('change', function () {
            const { role, module } = this.dataset;
            syncModuleToggle(role, module);
            updateBadge(role);
            updateModulePartial(role, module);
        });
    });

    // ── Select All ─────────────────────────────────────────────────────
    document.querySelectorAll('.select-all-role').forEach(btn => {
        btn.addEventListener('click', function () {
            const role = this.dataset.role;
            document.querySelectorAll(`.action-toggle[data-role="${role}"]`).forEach(cb => cb.checked = true);
            document.querySelectorAll(`.module-toggle[data-role="${role}"]`).forEach(t => t.checked = true);
            document.querySelectorAll(`.role-panel[data-role="${role}"] .module-partial-badge`).forEach(b => b.classList.add('d-none'));
            updateBadge(role);
        });
    });

    // ── Clear All ──────────────────────────────────────────────────────
    document.querySelectorAll('.clear-role').forEach(btn => {
        btn.addEventListener('click', function () {
            const role = this.dataset.role;
            document.querySelectorAll(`.action-toggle[data-role="${role}"]`).forEach(cb => cb.checked = false);
            document.querySelectorAll(`.module-toggle[data-role="${role}"]`).forEach(t => t.checked = false);
            document.querySelectorAll(`.role-panel[data-role="${role}"] .module-partial-badge`).forEach(b => b.classList.add('d-none'));
            updateBadge(role);
        });
    });

    // ── Helpers ─────────────────────────────────────────────────────────
    function syncModuleToggle(role, module) {
        const actions = document.querySelectorAll(`.action-toggle[data-role="${role}"][data-module="${module}"]`);
        const allChecked = [...actions].every(cb => cb.checked);
        const toggle = document.querySelector(`.module-toggle[data-role="${role}"][data-module="${module}"]`);
        if (toggle) toggle.checked = allChecked;
    }

    function updateBadge(role) {
        const count = document.querySelectorAll(`.action-toggle[data-role="${role}"]:checked`).length;
        const badge = document.querySelector(`.perm-count-badge[data-role="${role}"]`);
        if (badge) badge.textContent = count;
    }

    function updateModulePartial(role, module) {
        const card    = document.querySelector(`.module-card[data-role="${role}"][data-module="${module}"]`);
        if (!card) return;
        const actions  = card.querySelectorAll('.action-toggle');
        const checked  = card.querySelectorAll('.action-toggle:checked');
        let badge = card.querySelector('.module-partial-badge');

        if (checked.length > 0 && checked.length < actions.length) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'badge bg-warning text-dark module-partial-badge me-2';
                badge.style.fontSize = '10px';
                badge.textContent = 'Partial';
                card.querySelector('.card-header .flex-grow-1').after(badge);
            }
            badge.classList.remove('d-none');
        } else if (badge) {
            badge.classList.add('d-none');
        }
    }
});
</script>
@endpush
