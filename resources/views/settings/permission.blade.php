@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Permission Management</h3>
                <p class="text-muted mb-0" style="font-size:13px">Set module-level permissions for each role</p>
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

        <form method="POST" action="{{ route('settings.permission.save') }}">
            @csrf

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0 text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th rowspan="2" class="text-start ps-3" style="min-width:130px">Role</th>
                                    @foreach($modules as $module)
                                        @php
                                            $actions = ($module === 'request')
                                                ? ['view','create','edit','delete','approve']
                                                : ['view','create','edit','delete'];
                                        @endphp
                                        <th colspan="{{ count($actions) }}" class="text-capitalize">{{ $module }}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($modules as $module)
                                        @php
                                            $actions = ($module === 'request')
                                                ? ['view','create','edit','delete','approve']
                                                : ['view','create','edit','delete'];
                                        @endphp
                                        @foreach($actions as $action)
                                            <th class="text-capitalize small fw-normal" style="min-width:60px">{{ $action }}</th>
                                        @endforeach
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                @php $rolePerms = $role->permissions->pluck('slug')->toArray(); @endphp
                                <tr>
                                    <td class="text-start ps-3 fw-semibold">
                                        {{ $role->name }}
                                        <small class="text-muted d-block fw-normal">{{ $role->slug }}</small>
                                    </td>
                                    @foreach($modules as $module)
                                        @php
                                            $actions = ($module === 'request')
                                                ? ['view','create','edit','delete','approve']
                                                : ['view','create','edit','delete'];
                                        @endphp
                                        @foreach($actions as $action)
                                            @php $slug = $module . '.' . $action; @endphp
                                            <td>
                                                @if($permissions->where('slug', $slug)->count())
                                                <input type="checkbox"
                                                    name="permissions[{{ $role->id }}][]"
                                                    value="{{ $slug }}"
                                                    class="form-check-input perm-check"
                                                    data-role="{{ $role->id }}"
                                                    data-module="{{ $module }}"
                                                    {{ in_array($slug, $rolePerms) ? 'checked' : '' }}>
                                                @else
                                                <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Quick select helpers --}}
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($roles as $role)
                        <button type="button" class="btn btn-sm btn-outline-secondary select-all-role"
                                data-role="{{ $role->id }}">
                            Select All — {{ $role->name }}
                        </button>
                        @endforeach
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearAll">
                            Clear All
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i> Save Permissions
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Select all for a role
    document.querySelectorAll('.select-all-role').forEach(btn => {
        btn.addEventListener('click', function () {
            const roleId = this.dataset.role;
            document.querySelectorAll(`.perm-check[data-role="${roleId}"]`)
                    .forEach(cb => cb.checked = true);
        });
    });

    // Clear all
    document.getElementById('clearAll').addEventListener('click', function () {
        document.querySelectorAll('.perm-check').forEach(cb => cb.checked = false);
    });

});
</script>
@endpush
