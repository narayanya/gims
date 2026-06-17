@extends('layouts.app')

@section('content')
<div class="col-12">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
        <div>
            <h3 class="text-xl font-bold">Log Detail <span class="text-muted">#{{ $log->id }}</span></h3>
            <p class="text-muted mb-0" style="font-size:13px">
                Full activity record — {{ $log->created_at->format('d M Y, H:i:s') }}
            </p>
        </div>
        <a href="{{ route('logs.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Back to Logs
        </a>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1"><i class="ri-user-line me-1"></i>User</p>
                    <h6 class="mb-0 fw-bold">{{ $log->user?->name ?? 'System' }}</h6>
                    <small class="text-muted">{{ $log->user?->email ?? '—' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1"><i class="ri-flashlight-line me-1"></i>Action</p>
                    @php
                        $badge = match($log->action) {
                            'created'    => 'success',
                            'updated'    => 'warning',
                            'deleted'    => 'danger',
                            'login'      => 'info',
                            'logout'     => 'secondary',
                            'page_visit' => 'primary',
                            default      => 'dark',
                        };
                    @endphp
                    <span class="badge bg-{{ $badge }} fs-6">{{ ucfirst($log->action) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1"><i class="ri-stack-line me-1"></i>Module / Record</p>
                    <h6 class="mb-0 fw-bold text-capitalize">{{ $log->module }}</h6>
                    <small class="text-muted">{{ $log->record_label ?? ($log->record_id ? '#'.$log->record_id : '—') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1"><i class="ri-time-line me-1"></i>Date / Time</p>
                    <h6 class="mb-0 fw-bold">{{ $log->created_at->format('d M Y') }}</h6>
                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }} ({{ $log->created_at->diffForHumans() }})</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- ── Left Column ── --}}
        <div class="col-lg-8">

            {{-- Changes Table --}}
            @if($log->old_values || $log->new_values)
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="ri-git-diff-line me-2"></i>Field Changes</h6>
                    @php
                        $old  = $log->old_values ?? [];
                        $new  = $log->new_values ?? [];
                        $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
                        $changedCount = collect($keys)->filter(fn($k) => ($old[$k] ?? null) !== ($new[$k] ?? null))->count();
                    @endphp
                    <span class="badge bg-warning text-dark">{{ $changedCount }} field(s) changed</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:25%">Field</th>
                                    <th style="width:37%">Old Value</th>
                                    <th style="width:37%">New Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($keys as $key)
                                @php
                                    $oldVal  = $old[$key] ?? null;
                                    $newVal  = $new[$key] ?? null;
                                    $changed = $oldVal !== $newVal;
                                    $fmt = fn($v) => is_array($v) ? json_encode($v, JSON_PRETTY_PRINT) : ($v ?? '—');
                                @endphp
                                <tr class="{{ $changed ? 'table-warning' : '' }}">
                                    <td>
                                        <code class="text-dark">{{ $key }}</code>
                                        @if($changed)
                                            <i class="ri-arrow-right-line text-warning ms-1"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="{{ $changed ? 'text-danger' : 'text-muted' }}" style="white-space:pre-wrap;word-break:break-all;font-size:12px;">
                                            {{ $fmt($oldVal) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $changed ? 'text-success fw-semibold' : 'text-muted' }}" style="white-space:pre-wrap;word-break:break-all;font-size:12px;">
                                            {{ $fmt($newVal) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Page Visit Info --}}
            @if($log->action === 'page_visit')
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ri-route-line me-2"></i>Page Visit Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Page Title</p>
                            <span class="badge bg-primary fs-6">{{ $log->page_title ?? '—' }}</span>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Full URL</p>
                            <a href="{{ $log->page_url }}" target="_blank" class="small text-break">{{ $log->page_url ?? '—' }}</a>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1"><i class="ri-login-box-line me-1 text-success"></i>In Time</p>
                            <strong>{{ $log->in_time ? $log->in_time->format('d M Y, H:i:s') : '—' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1"><i class="ri-logout-box-line me-1 text-danger"></i>Out Time</p>
                            <strong>{{ $log->out_time ? $log->out_time->format('d M Y, H:i:s') : '—' }}</strong>
                            @if(!$log->out_time)
                                <span class="badge bg-warning text-dark ms-1">Active</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1"><i class="ri-timer-line me-1 text-info"></i>Time Spent</p>
                            @if($log->time_spent_seconds !== null)
                                @php
                                    $mins = intdiv($log->time_spent_seconds, 60);
                                    $secs = $log->time_spent_seconds % 60;
                                @endphp
                                <span class="badge bg-info text-dark fs-6">
                                    {{ $mins > 0 ? "{$mins}m {$secs}s" : "{$secs}s" }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- ── Right Column ── --}}
        <div class="col-lg-4">

            {{-- Request Info --}}
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ri-global-line me-2"></i>Request Info</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th class="text-muted fw-normal" style="width:40%">IP Address</th>
                            <td><code>{{ $log->ip_address ?? '—' }}</code></td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Record ID</th>
                            <td>{{ $log->record_id ? '#'.$log->record_id : '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Record Label</th>
                            <td>{{ $log->record_label ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Log ID</th>
                            <td><span class="badge bg-secondary">#{{ $log->id }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- User Agent --}}
            @if($log->user_agent)
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ri-computer-line me-2"></i>Browser / Device</h6>
                </div>
                <div class="card-body">
                    @php
                        $ua = $log->user_agent;
                        $browser = 'Unknown';
                        $os      = 'Unknown';
                        if (str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg'))  $browser = 'Chrome';
                        elseif (str_contains($ua, 'Firefox'))  $browser = 'Firefox';
                        elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome'))  $browser = 'Safari';
                        elseif (str_contains($ua, 'Edg'))  $browser = 'Edge';
                        elseif (str_contains($ua, 'MSIE') || str_contains($ua, 'Trident'))  $browser = 'IE';
                        if (str_contains($ua, 'Windows'))  $os = 'Windows';
                        elseif (str_contains($ua, 'Mac'))  $os = 'macOS';
                        elseif (str_contains($ua, 'Linux'))  $os = 'Linux';
                        elseif (str_contains($ua, 'Android'))  $os = 'Android';
                        elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad'))  $os = 'iOS';
                    @endphp
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge bg-primary">{{ $browser }}</span>
                        <span class="badge bg-secondary">{{ $os }}</span>
                    </div>
                    <p class="text-muted small mb-0" style="word-break:break-all;font-size:11px;">{{ $ua }}</p>
                </div>
            </div>
            @endif

            {{-- Related Logs --}}
            @php
                $related = \App\Models\ActivityLog::with('user')
                    ->where('id', '!=', $log->id)
                    ->where(function($q) use ($log) {
                        $q->where('record_id', $log->record_id)
                          ->where('module', $log->module)
                          ->whereNotNull('record_id');
                    })
                    ->orWhere(function($q) use ($log) {
                        $q->where('user_id', $log->user_id)
                          ->whereDate('created_at', $log->created_at->toDateString())
                          ->where('id', '!=', $log->id);
                    })
                    ->latest('created_at')
                    ->limit(5)
                    ->get();
            @endphp
            @if($related->count())
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ri-history-line me-2"></i>Related Logs</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($related as $rel)
                        <li class="list-group-item py-2 px-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    @php
                                        $rb = match($rel->action) {
                                            'created' => 'success', 'updated' => 'warning',
                                            'deleted' => 'danger',  'login'   => 'info',
                                            default   => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $rb }} me-1">{{ ucfirst($rel->action) }}</span>
                                    <span class="small text-capitalize">{{ $rel->module }}</span>
                                    @if($rel->record_label)
                                        <span class="text-muted small"> — {{ $rel->record_label }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('logs.show', $rel->id) }}" class="btn btn-xs btn-outline-secondary btn-sm py-0 px-1 ms-2">View</a>
                            </div>
                            <div class="text-muted" style="font-size:11px;">
                                {{ $rel->user?->name ?? 'System' }} · {{ $rel->created_at->format('d M, H:i') }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
