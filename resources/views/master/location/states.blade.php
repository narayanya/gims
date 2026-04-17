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

        @include('master.location._tabs', ['active' => 'states'])

        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('location.states') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Search by name or code..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="country_id" class="form-select form-select-sm">
                            <option value="">All Countries</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ request('country_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->country_name }}
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
                        <a href="{{ route('location.states') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>State Name</th>
                                <th>Country</th>
                                <th>State Code</th>
                                <th>Short Code</th>
                                <th>Effective Date</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($states as $state)
                            <tr>
                                <td>{{ $states->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $state->state_name }}</td>
                                <td>{{ $state->country?->country_name ?? '—' }}</td>
                                <td>
                                    @if($state->state_code)
                                        <span class="badge bg-info">{{ $state->state_code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $state->short_code ?? '—' }}</td>
                                <td>{{ $state->effective_date ? \Carbon\Carbon::parse($state->effective_date)->format('d M Y') : '—' }}</td>
                                <td>
                                    <span class="badge {{ $state->is_active == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $state->is_active == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No states found. Data is populated via Location Sync.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <style>
/* Fix Laravel pagination arrows */
.pagination svg {
    width: 14px !important;
    height: 14px !important;
}

/* Prevent stretching */
.page-link svg {
    width: 14px !important;
    height: 14px !important;
}

/* Align properly */
.pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px 10px;
    font-size: 13px;
}     
svg {
    max-width: 20px;
    height: auto;
}        </style>
            @if($states->hasPages())
            <div class="card-footer">{{ $states->links() }}</div>
            @endif
        </div>

    </div>
</div>
@endsection
