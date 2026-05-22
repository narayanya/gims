@extends('layouts.app')

@section('content')
 <div class="row justify-content-center">
        <div class="col-12 d-none">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Request Report
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777"> Request report with filters and detailed information</p>
                </div>
                <form class="d-flex gap-2 align-items-center" method="GET" id="txnFilterForm">
                        <input type="date" name="date_from" class="form-control form-control-sm" placeholder="From" value="{{ request('date_from') }}" style="width:140px">
                        <input type="date" name="date_to"   class="form-control form-control-sm" placeholder="To"   value="{{ request('date_to') }}"   style="width:140px">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
            </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Request No</th>
                <th>User</th>
                <th>Crop</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $r)
                <tr>
                    <td>{{ $r->request_number }}</td>
                    <td>{{ $r->user->name ?? '' }}</td>
                    <td>{{ $r->crop->crop_name ?? '' }}</td>
                    <td>{{ $r->quantity }} {{ $r->unit->name ?? '' }}</td>
                    <td>{{ ucfirst($r->status) }}</td>
                    <td>{{ $r->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="col-12 ">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Request Report
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777"> Request report with filters and detailed information</p>
                </div>
                 <form class="d-flex gap-2 align-items-center flex-wrap" method="GET" id="txnFilterForm">

                <input type="date"
                       name="date_from"
                       class="form-control form-control-sm"
                       value="{{ request('date_from') }}"
                       style="width:140px">

                <input type="date"
                       name="date_to"
                       class="form-control form-control-sm"
                       value="{{ request('date_to') }}"
                       style="width:140px">

                <select name="status" class="form-select form-select-sm" style="width:140px">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                <button type="submit" class="btn btn-sm btn-primary">
                    Filter
                </button>

                <a href="" class="btn btn-sm btn-secondary">
                    Reset
                </a>
            </form>
            </div>
        <!-- Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Request No</th>
                            <th>Requester</th>
                            <th>Crop</th>
                            <th>Accession</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Required Date</th>
                            <th>Purpose</th>
                            <th>Created</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requests as $key => $r)
                            <tr>

                                <td>
                                    <span class="fw-semibold text-primary">
                                        {{ $r->request_number }}
                                    </span>
                                </td>

                                <td>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $r->requester_name ?? ($r->user->name ?? '-') }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $r->requester_email ?? '-' }}
                                        </small>
                                    </div>
                                </td>

                                <td>{{ $r->crop->crop_name ?? '-' }}</td>

                                <td>{{ $r->accession->accession_number ?? '-' }}</td>

                                <td>
                                    {{ $r->quantity }}
                                    {{ $r->unit->name ?? '' }}
                                </td>

                                <td>
                                    @php
                                        $badgeClass = match($r->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'completed' => 'primary',
                                            'dispatched' => 'secondary',
                                            default => 'warning'
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $badgeClass }}">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $r->request_date ? \Carbon\Carbon::parse($r->request_date)->format('d M Y') : '-' }}
                                </td>

                                <td>
                                    {{ $r->required_date ? \Carbon\Carbon::parse($r->required_date)->format('d M Y') : '-' }}
                                </td>

                                <td style="max-width:200px">
                                    {{ $r->purpose ?: '-' }}
                                </td>

                                <td>
                                    {{ $r->created_at->format('d M Y') }}
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    No request records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $requests->withQueryString()->links() }}
                </div>

            </div>
        </div>
</div>

 </div>


@endsection