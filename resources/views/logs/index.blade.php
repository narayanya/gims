@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
            <div class="items-center gap-3">
                <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                    Activity Log Report
                </h3>
                <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View Activity Log </p>
            </div>
        </div>
    

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('logs.index') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Module</label>
                <select name="module" class="form-select form-select-sm">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Action</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                <a href="{{ route('logs.export', request()->query()) }}" class="btn btn-success btn-sm">CSV</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Record</th>
                        <th>IP Address</th>
                        <th>Date / Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->user?->name ?? '<em>System</em>' }}</td>
                        <td>
                            @php
                                $badge = match($log->action) {
                                    'created'  => 'success',
                                    'updated'  => 'warning',
                                    'deleted'  => 'danger',
                                    'login'    => 'info',
                                    'logout'   => 'secondary',
                                    default    => 'primary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($log->action) }}</span>
                        </td>
                        <td>{{ ucfirst($log->module) }}</td>
                        <td>{{ $log->record_label ?? ($log->record_id ? '#'.$log->record_id : '—') }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            @if($log->old_values || $log->new_values)
                                <a href="{{ route('logs.show', $log->id) }}" class="btn btn-xs btn-outline-primary btn-sm py-0 px-1">Details</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No activity logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
    @endif
</div>
</div>
</div>
@endsection
