@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Log Detail #{{ $log->id }}</h4>
            <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm">Back to Logs</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-sm w-auto mb-4">
            <tr><th>User</th><td>{{ $log->user?->name ?? 'System' }}</td></tr>
            <tr><th>Action</th><td>{{ ucfirst($log->action) }}</td></tr>
            <tr><th>Module</th><td>{{ ucfirst($log->module) }}</td></tr>
            <tr><th>Record</th><td>{{ $log->record_label ?? ($log->record_id ? '#'.$log->record_id : '—') }}</td></tr>
            <tr><th>IP Address</th><td>{{ $log->ip_address }}</td></tr>
            <tr><th>Date / Time</th><td>{{ $log->created_at->format('d M Y, H:i:s') }}</td></tr>
        </table>

        @if($log->old_values || $log->new_values)
        <h6 class="mb-3">Changes</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Field</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $old = $log->old_values ?? [];
                        $new = $log->new_values ?? [];
                        $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
                    @endphp
                    @foreach($keys as $key)
                    @php
                        $oldVal = $old[$key] ?? null;
                        $newVal = $new[$key] ?? null;
                        $changed = $oldVal !== $newVal;
                    @endphp
                    <tr class="{{ $changed ? 'table-warning' : '' }}">
                        <td><code>{{ $key }}</code></td>
                        <td class="text-danger">{{ is_array($oldVal) ? json_encode($oldVal) : ($oldVal ?? '—') }}</td>
                        <td class="text-success">{{ is_array($newVal) ? json_encode($newVal) : ($newVal ?? '—') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
