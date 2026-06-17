@extends('layouts.app')

@section('content')
<div class="col-12">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
        <div>
            <h3 class="text-xl font-bold">Activity Log Report</h3>
            <p class="text-muted mb-0" style="font-size:13px">Track user actions and page navigation</p>
        </div>
        <a href="{{ route('logs.export', request()->query()) }}" class="btn btn-sm btn-success">
            <i class="ri-file-download-line me-1"></i> Export CSV
        </a>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'dashboard' ? 'active' : '' }}"
               href="{{ route('logs.index', ['tab' => 'dashboard']) }}">
                <i class="ri-dashboard-line me-1"></i> Dashboard
            </a>
        </li>
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

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- DASHBOARD TAB                                                      --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($tab === 'dashboard')

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                        <i class="ri-list-check-2 text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Activities</div>
                        <div class="fw-bold fs-4">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                        <i class="ri-calendar-check-line text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Today</div>
                        <div class="fw-bold fs-4">{{ number_format($stats['today']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                        <i class="ri-user-line text-info fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Active Users Today</div>
                        <div class="fw-bold fs-4">{{ number_format($stats['active_users']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                        <i class="ri-route-line text-warning fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Page Visits Today</div>
                        <div class="fw-bold fs-4">{{ number_format($stats['page_visits']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- Module Activity --}}
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ri-bar-chart-line me-2"></i>Module Activity</h6>
                </div>
                <div class="card-body">
                    @forelse($moduleStats as $mod)
                    @php $pct = round(($mod->total / $moduleMax) * 100); @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-capitalize fw-semibold small">{{ $mod->module }}</span>
                            <span class="text-muted small">{{ number_format($mod->total) }}</span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">No data yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Action Breakdown --}}
        <div class="col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ri-pie-chart-line me-2"></i>Action Breakdown</h6>
                </div>
                <div class="card-body">
                    @php
                        $actionColors = ['created'=>'success','updated'=>'warning','deleted'=>'danger','login'=>'info','logout'=>'secondary'];
                        $actionTotal  = $actionStats->sum('total') ?: 1;
                    @endphp
                    @forelse($actionStats as $act)
                    @php
                        $color = $actionColors[$act->action] ?? 'primary';
                        $pct   = round(($act->total / $actionTotal) * 100);
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $color }}">{{ ucfirst($act->action) }}</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold">{{ number_format($act->total) }}</span>
                            <span class="text-muted small ms-1">{{ $pct }}%</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">No data yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Top Active Users Today --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ri-user-star-line me-2"></i>Top Active Users Today</h6>
                </div>
                <div class="card-body p-0">
                    @if($topUsers->count())
                    <ul class="list-group list-group-flush">
                        @foreach($topUsers as $i => $tu)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary rounded-pill">{{ $i+1 }}</span>
                                <span class="small fw-semibold">{{ $tu->user?->name ?? 'Unknown' }}</span>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $tu->total }} actions</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted text-center py-4">No activity today</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Hourly Activity Bar --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="ri-time-line me-2"></i>Hourly Activity — Today</h6>
            <small class="text-muted">{{ now()->format('d M Y') }}</small>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-end gap-1" style="height:80px;">
                @php $hourMax = $hourlyActivity->max('total') ?: 1; @endphp
                @for($h = 0; $h < 24; $h++)
                @php
                    $count  = $hourlyActivity->get($h)?->total ?? 0;
                    $height = $count ? max(8, round(($count / $hourMax) * 70)) : 4;
                    $color  = $count > 0 ? 'bg-primary' : 'bg-light border';
                    $label  = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
                @endphp
                <div class="d-flex flex-column align-items-center flex-fill" title="{{ $label }}: {{ $count }} actions">
                    <div class="{{ $color }} rounded-top w-100" style="height:{{ $height }}px;min-height:4px;cursor:default;"></div>
                    @if($h % 4 === 0)
                    <small class="text-muted" style="font-size:9px;">{{ $h }}h</small>
                    @endif
                </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Recent Activity Table --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="ri-history-line me-2"></i>Recent Activity</h6>
            <a href="{{ route('logs.index', ['tab' => 'actions']) }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date / Time</th>
                        <th>User</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Record</th>
                        <th>IP</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivity as $log)
                    @php
                        $badge = match($log->action) {
                            'created' => 'success', 'updated' => 'warning',
                            'deleted' => 'danger',  'login'   => 'info',
                            'logout'  => 'secondary', default => 'primary',
                        };
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $log->created_at->format('d M, H:i') }}</td>
                        <td class="fw-semibold small">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="text-capitalize small">{{ $log->module }}</td>
                        <td><span class="badge bg-{{ $badge }}">{{ ucfirst($log->action) }}</span></td>
                        <td class="small text-muted">{{ $log->record_label ?? ($log->record_id ? '#'.$log->record_id : '—') }}</td>
                        <td class="small text-muted">{{ $log->ip_address }}</td>
                        <td>
                            @if($log->old_values || $log->new_values)
                            <a href="{{ route('logs.show', $log->id) }}" class="btn btn-xs btn-outline-primary btn-sm py-0 px-1">Details</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No activity yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ACTION LOGS TAB                                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @elseif($tab === 'actions')

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('logs.index') }}" class="row g-2 align-items-end">
                <input type="hidden" name="tab" value="actions">
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
                    <a href="{{ route('logs.index', ['tab' => 'actions']) }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date / Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Record</th>
                            <th>IP Address</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        @php
                            $badge = match($log->action) {
                                'created' => 'success', 'updated' => 'warning',
                                'deleted' => 'danger',  'login'   => 'info',
                                'logout'  => 'secondary', default => 'primary',
                            };
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $log->id }}</td>
                            <td class="small">{{ $log->created_at->format('d M Y, H:i') }}</td>
                            <td class="fw-semibold small">{{ $log->user?->name ?? '<em>System</em>' }}</td>
                            <td><span class="badge bg-{{ $badge }}">{{ ucfirst($log->action) }}</span></td>
                            <td class="text-capitalize small">{{ $log->module }}</td>
                            <td class="small text-muted">{{ $log->record_label ?? ($log->record_id ? '#'.$log->record_id : '—') }}</td>
                            <td class="small text-muted">{{ $log->ip_address }}</td>
                            <td>
                                @if($log->old_values || $log->new_values)
                                <a href="{{ route('logs.show', $log->id) }}" class="btn btn-xs btn-outline-primary btn-sm py-0 px-1">Details</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No activity logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer">{{ $logs->withQueryString()->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE VISITS TAB                                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @else

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('logs.index') }}" class="row g-2 align-items-end">
                <input type="hidden" name="tab" value="visits">
                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
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
                    <a href="{{ route('logs.index', ['tab' => 'visits']) }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

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
                            $duration = null;
                            if ($spent !== null) {
                                $mins = intdiv($spent, 60);
                                $secs = $spent % 60;
                                $duration = $mins > 0 ? "{$mins}m {$secs}s" : "{$secs}s";
                            }
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $log->id }}</td>
                            <td class="fw-semibold small">{{ $log->user?->name ?? '—' }}</td>
                            <td><span class="badge bg-primary">{{ $log->page_title ?? '—' }}</span></td>
                            <td class="text-muted small" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->page_url }}">
                                {{ $log->page_url }}
                            </td>
                            <td>
                                @if($log->in_time)
                                <span class="text-success small"><i class="ri-login-box-line"></i> {{ $log->in_time->format('H:i:s') }}</span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($log->out_time)
                                <span class="text-danger small"><i class="ri-logout-box-line"></i> {{ $log->out_time->format('H:i:s') }}</span>
                                @else
                                <span class="badge bg-warning text-dark">Active</span>
                                @endif
                            </td>
                            <td>
                                @if($duration)
                                <span class="badge bg-info text-dark">{{ $duration }}</span>
                                @else <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $log->ip_address }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No page visits recorded yet.</td></tr>
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
@endsection
