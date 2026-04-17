@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="mb-0">Location Master</h3>
                <p class="text-muted mb-0" style="font-size:13px">View synced location data — Country / State / District / City</p>
            </div>
        </div>

        {{-- Tabs --}}
        @include('master.location._tabs', ['active' => 'countries'])

        {{-- Sync Result Alert --}}
        <div id="syncAlert" class="d-none"></div>

        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('location.countries') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Search by name or code..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="is_active" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        <a href="{{ route('location.countries') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                    <div class="col-auto ms-auto text-muted" style="font-size:12px">
                        Total: <strong>{{ $countries->total() }}</strong> countries
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
                                <th>Country Name</th>
                                <th>Country Code</th>
                                <th>Global Region</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($countries as $country)
                            <tr>
                                <td>{{ $countries->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $country->country_name }}</td>
                                <td>
                                    @if($country->country_code)
                                        <span class="badge bg-info">{{ $country->country_code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $country->global_region ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $country->is_active == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $country->is_active == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No countries found. Click <strong>Sync Countries</strong> to import data.
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
            @if($countries->hasPages())
            <div class="card-footer">{{ $countries->links() }}</div>
            @endif
        </div>

    </div>
</div>
@endsection
