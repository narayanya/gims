@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
            <div class="items-center gap-3">
                <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                    Expiry Report
                </h3>

                <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">
                    View expiry data with filters
                </p>
            </div>

            <div class="d-flex gap-2">

                <!-- Download -->
                <a href="{{ route('expiry.report.download', request()->query()) }}"
                   class="btn btn-sm btn-primary">
                    <i class="ri-download-line me-1"></i>
                    Download Report
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">

                <form method="GET" class="row g-2 align-items-end">

                    <!-- From Date -->
                    <div class="col-md-2">
                        <label class="form-label text-muted small">
                            From Date
                        </label>

                        <input type="date"
                               name="date_from"
                               class="form-control form-control-sm"
                               value="{{ request('date_from') }}">
                    </div>

                    <!-- To Date -->
                    <div class="col-md-2">
                        <label class="form-label text-muted small">
                            To Date
                        </label>

                        <input type="date"
                               name="date_to"
                               class="form-control form-control-sm"
                               value="{{ request('date_to') }}">
                    </div>

                    <!-- Status -->
                    <div class="col-md-2">
                        <label class="form-label text-muted small">
                            Status
                        </label>

                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>

                            <option value="expired"
                                {{ request('status') == 'expired' ? 'selected' : '' }}>
                                Expired
                            </option>

                            <option value="critical"
                                {{ request('status') == 'critical' ? 'selected' : '' }}>
                                Critical
                            </option>

                            <option value="soon"
                                {{ request('status') == 'soon' ? 'selected' : '' }}>
                                Expiring Soon
                            </option>

                            <option value="safe"
                                {{ request('status') == 'safe' ? 'selected' : '' }}>
                                Safe
                            </option>
                        </select>
                    </div>

                    <!-- Filter -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary ">
                            <i class="ri-filter-line me-1"></i>
                            Filter
                        </button>
                        <a href="{{ route('expiry.report') }}"
                           class="btn btn-sm btn-secondary">
                            Reset
                        </a>
                    </div>

                    <!-- Reset -->
                    <div class="col-md-2">
                        
                    </div>

                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive">

                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Lot Number</th>
                            <th>Accession ID</th>
                            <th>Accession</th>
                            <th>Crop</th>
                            <th>Storage</th>
                            <th>Regeneration Date</th>
                            <th>Expiry Date</th>
                            <th>Days Left</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($lots as $lot)

                        @php
                            $expiry = $lot->expiry_date
                                ? \Carbon\Carbon::parse($lot->expiry_date)
                                : null;

                            $daysLeft = $expiry
                                ? (int) now()->diffInDays($expiry, false)
                                : null;

                            if (!$expiry) {
                                $status = ['label' => 'N/A', 'class' => 'secondary'];
                            } elseif ($daysLeft < 0) {
                                $status = ['label' => 'Expired', 'class' => 'danger'];
                            } elseif ($daysLeft <= 3) {
                                $status = ['label' => 'Critical', 'class' => 'danger'];
                            } elseif ($daysLeft <= 10) {
                                $status = ['label' => 'Expiring Soon', 'class' => 'warning'];
                            } else {
                                $status = ['label' => 'Safe', 'class' => 'success'];
                            }
                        @endphp

                        <tr>
                            <td>
                                {{ $lot->lot_number }}
                            </td>

                            <td>
                                {{ $lot->accession->accession_name ?? $lot->accession->accession_number }}
                            </td>

                            <td>
                                {{ $lot->accession->crop->crop_name ?? '-' }}
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $lot->accession->storageTime->code ?? '-' }}
                                </span>
                            </td>

                            <td>
                                {{ $lot->accession->recheck_date
                                    ? \Carbon\Carbon::parse($lot->accession->recheck_date)->format('d M Y')
                                    : '-'
                                }}
                            </td>

                            <td>
                                {{ $expiry ? $expiry->format('d M Y') : '-' }}
                            </td>

                            <td>
                                @if(!is_null($daysLeft))
                                    @if($daysLeft < 0)
                                        <span class="text-danger fw-bold">
                                            {{ abs($daysLeft) }} Days Ago
                                        </span>
                                    @else
                                        <span class="text-primary fw-semibold">
                                            {{ $daysLeft }} Days
                                        </span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-{{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No expiry records found
                            </td>
                        </tr>

                    @endforelse

                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-3">
                    {{-- $accessions->withQueryString()->links() --}}
                </div>

            </div>
        </div>

    </div>
</div>
@endsection