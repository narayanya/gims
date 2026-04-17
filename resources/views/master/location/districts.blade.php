@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="mb-0">Location Master</h3>
                <p class="text-muted mb-0" style="font-size:13px">View synced location data — Country / State / District / City</p>
            </div>
        </div>

        @include('master.location._tabs', ['active' => 'districts'])

        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('location.districts') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Search by name or code..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="state_id" class="form-select form-select-sm">
                            <option value="">All States</option>
                            @foreach($states as $s)
                                <option value="{{ $s->id }}" {{ request('state_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->state_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="is_active" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        <a href="{{ route('location.districts') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                    <div class="col-auto ms-auto text-muted" style="font-size:12px">
                        Total: <strong>{{ $districts->total() }}</strong> districts
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>District Name</th>
                                <th>State</th>
                                <th>District Code</th>
                                <th>Numeric Code</th>
                                <th>Effective Date</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($districts as $district)
                            <tr>
                                <td>{{ $districts->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $district->district_name }}</td>
                                <td>{{ $district->state?->state_name ?? '—' }}</td>
                                <td>
                                    @if($district->district_code)
                                        <span class="badge bg-info">{{ $district->district_code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $district->numeric_code ?? '—' }}</td>
                                <td>{{ $district->effective_date ? \Carbon\Carbon::parse($district->effective_date)->format('d M Y') : '—' }}</td>
                                <td>
                                    <span class="badge {{ $district->is_active == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $district->is_active == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No districts found. Data is populated via Location Sync.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($districts->hasPages())
            <div class="card-footer">{{ $districts->links() }}</div>
            @endif
        </div>

    </div>
</div>
@endsection
