@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Expire Report
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">View expire data</p>
                </div>
                <a href="{{ route('expiry.report.download') }}" class="btn btn-sm btn-primary">
                    <i class="ri-download-line me-1"></i>Download Report
                </a>
            </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Accession ID</th>
                <th>Accession</th>
                <th>Crop</th>
                <th>Storage</th>
                <th>Re-check Date</th>
                <th>Expiry Date</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        @foreach($accessions as $key => $accession)

            @php
                $expiry = $accession->expiry_date 
                    ? \Carbon\Carbon::parse($accession->expiry_date) 
                    : null;

                $daysLeft = $expiry 
                    ? now()->diffInDays($expiry, false) 
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
                <td>{{ $key + 1 }}</td>
                <td>
                    {{ $accession->accession_id }}
                </td>
                <td>
                    {{ $accession->accession_name ?? $accession->accession_number }}
                </td>

                <td>
                    {{ $accession->crop->crop_name ?? '-' }}
                </td>
                <td>
                    <span class="badge bg-info">
                        {{ $accession->storageTime->code ?? '-' }}
                    </span>
                </td>

                <td>
                    {{ $accession->recheck_date 
                        ? \Carbon\Carbon::parse($accession->recheck_date)->format('d M Y') 
                        : '-' 
                    }}
                </td>

                <td>
                    {{ $expiry ? $expiry->format('d M Y') : '-' }}
                </td>

                <td>
                    <span class="badge bg-{{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>
</div>
@endsection