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

        @include('master.location._tabs', ['active' => 'cities'])

        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('location.cities') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Search by city/village name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
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
                        <select name="district_id" class="form-select form-select-sm">
                            <option value="">All Districts</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ request('district_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->district_name }}
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
                        <a href="{{ route('location.cities') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                    <div class="col-auto ms-auto text-muted" style="font-size:12px">
                        Total: <strong>{{ $cities->total() }}</strong> records
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
                                <th>City / Village Name</th>
                                <th>State</th>
                                <th>District</th>
                                <th>Code</th>
                                <th>Division</th>
                                <th>Pincode</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cities as $city)
                            <tr>
                                <td>{{ $cities->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $city->city_village_name }}</td>
                                <td>{{ $city->state?->state_name ?? '—' }}</td>
                                <td>{{ $city->district?->district_name ?? '—' }}</td>
                                <td>
                                    @if($city->city_village_code)
                                        <span class="badge bg-info">{{ $city->city_village_code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $city->division_name ?? '—' }}</td>
                                <td>{{ $city->pincode ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $city->is_active == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $city->is_active == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No cities/villages found. Data is populated via Location Sync.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($cities->hasPages())
            <div class="card-footer">{{ $cities->links() }}</div>
            @endif
        </div>

    </div>
</div>
@endsection
