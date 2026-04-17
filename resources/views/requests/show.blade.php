@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Request Details - {{ $seedRequest->request_number }}</h2>
                <div>
                    <a href="{{ route('requests.edit', $seedRequest) }}" class="btn btn-warning">
                        <i class="ri-edit-line me-1"></i> Edit
                    </a>
                    <a href="{{ route('requests.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Request Number:</strong>
                    <p>{{ $seedRequest->request_number }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Status:</strong>
                    <p>
                        @if($seedRequest->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($seedRequest->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($seedRequest->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-info">Completed</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Crop:</strong>
                    <p>{{ $seedRequest->crop->crop_name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Quantity:</strong>
                    <p>{{ $seedRequest->quantity }} {{ $seedRequest->unit->code ?? '' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Unit:</strong>
                    <p>{{ $seedRequest->unit->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Requester Name:</strong>
                    <p>{{ $seedRequest->requester_name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Requester Email:</strong>
                    <p>{{ $seedRequest->requester_email ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Purpose:</strong>
                    <p>{{ $seedRequest->purpose ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Request Date:</strong>
                    <p>{{ $seedRequest->request_date?->format('d M Y') ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Required Date:</strong>
                    <p>{{ $seedRequest->required_date?->format('d M Y') ?? 'N/A' }}</p>
                </div>
            </div>

            @if($seedRequest->approved_by)
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Approved By:</strong>
                    <p>{{ $seedRequest->approvedBy->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Approved At:</strong>
                    <p>{{ $seedRequest->approved_at?->format('d M Y H:i') ?? 'N/A' }}</p>
                </div>
            </div>
            @endif

            @if($seedRequest->notes)
            <div class="row">
                <div class="col-12 mb-3">
                    <strong>Notes:</strong>
                    <p>{{ $seedRequest->notes }}</p>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Created At:</strong>
                    <p>{{ $seedRequest->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Updated At:</strong>
                    <p>{{ $seedRequest->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
