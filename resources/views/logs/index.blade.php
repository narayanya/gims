@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <div>
                <h3 class="text-xl font-bold">Activity Log Report</h3>
                <p class="text-muted mb-0" style="font-size:13px">Track user actions and page navigation</p>
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'actions' ? 'active' : '' }}"
                   href="{{ route('logs.index', array_merge(request()->except('tab','page'), ['tab' => 'actions'])) }}">
                    <i class="ri-list-check me-1"></i> Action Logs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'visits' ? 'active' : '' }}"
                   href="{{ route('logs.index', array_merge(request()->except('tab','page'), ['tab' => 'visits'])) }}">
                    <i class="ri-route-line me-1"></i> Page Visits
                </a>
            </li>
        </ul>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('logs.index') }}" class="row g-2 align-items-end">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($tab === 'actions')
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
                    @endif
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
                        <a href="{{ route('logs.index', ['tab' => $tab]) }}" class="btn btn-secondary btn-sm">Reset</a>
                        <a href="{{ route('logs.export', array_merge(request()->query(), ['tab' => $tab])) }}" class="btn btn-success btn-sm">CSV</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- ACTION LOGS TABLE --}}
        @if($tab === 'actions')
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
                                <th>Details</th>
                                <th>Page Url</th>
                                <th>Page Title</th>
                                <th>in time</th>
                                <th>Out time</th>
                                <th>Total Time</th>
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
                                <td>{{ $log->page_url }}</td>
                                <td>{{ $log->page_title }}</td>
                                <td>{{ $log->in_time }}</td>
                                <td>{{ $log->out_time }}</td>
                                <td>{{ $log->time_spent_seconds }}</td>
                                
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
            <div class="card-footer">{{ $logs->withQueryString()->links() }}</div>
            @endif
        </div>

        {{-- PAGE VISITS TABLE --}}
        @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Page</th>
                                <th>URL</th>
                                <th>In Time</th>
                                <th>Out Time</th>
                                <th>Time Spent</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            @php
                                $spent = $log->time_spent_seconds;
                                if ($spent !== null) {
                                    $mins = intdiv($spent, 60);
                                    $secs = $spent % 60;
                                    $duration = $mins > 0 ? "{$mins}m {$secs}s" : "{$secs}s";
                                } else {
                                    $duration = null;
                                }
                            @endphp
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->user?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $log->page_title ?? '—' }}</span>
                                </td>
                                <td class="text-muted small" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->page_url }}">
                                    {{ $log->page_url }}
                                </td>
                                <td>
                                    @if($log->in_time)
                                        <span class="text-success fw-semibold">
                                            <i class="ri-login-box-line me-1"></i>{{ $log->in_time->format('d M Y') }}<br>
                                            <small>{{ $log->in_time->format('H:i:s') }}</small>
                                        </span>
                                    @else —
                                    @endif
                                </td>
                                <td>
                                    @if($log->out_time)
                                        <span class="text-danger fw-semibold">
                                            <i class="ri-logout-box-line me-1"></i>{{ $log->out_time->format('d M Y') }}<br>
                                            <small>{{ $log->out_time->format('H:i:s') }}</small>
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @if($duration)
                                        <span class="badge bg-info text-dark">{{ $duration }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $log->ip_address }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No page visits recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($logs->hasPages())
            <div class="card-footer">{{ $logs->withQueryString()->links() }}</div>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection
