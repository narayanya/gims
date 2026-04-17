@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Dispatch Management</h3>
                <p class="text-muted mb-0" style="font-size:13px">Review and confirm dispatch for approved request</p>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Back
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">

            {{-- Left: Request Details --}}
            <div class="col-lg-8">

                {{-- Request Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-file-list-3-line me-2"></i>Request Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Request No.</small>
                                <span class="badge bg-primary fs-6">{{ $request->request_number }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Request Date</small>
                                <strong>{{ $request->created_at->format('d M Y') }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Required Date</small>
                                <strong>{{ $request->required_date ? $request->required_date->format('d M Y') : 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Status</small>
                                @php
                                    $sc = match($request->status) {
                                        'approved','completed','dispatched' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $sc }}">{{ ucfirst($request->status) }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Purpose</small>
                                <strong>{{ $request->purpose ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Purpose Details</small>
                                <strong>{{ $request->purpose_details ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Requester Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-user-line me-2"></i>Requester Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Name</small>
                                <strong>{{ $request->requester_name ?? $request->user?->name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Email</small>
                                <strong>{{ $request->requester_email ?? $request->user?->email ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Receiver Name</small>
                                <strong>{{ $request->receiver_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Destination</small>
                                <strong>{{ $request->destination ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Requester- Reporting/Status</small>
                                <strong>{{ $request->destination ?? 'N/A' }} <span class="badge bg-success">Approved</span></strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Seed / Accession Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-seedling-line me-2"></i>Seed / Accession Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Crop</small>
                                <strong>{{ $request->crop?->crop_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Accession Name</small>
                                <strong>{{ $request->accession?->accession_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Accession No.</small>
                                <strong>{{ $request->accession?->accession_number ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Quantity</small>
                                <strong>{{ $request->quantity }} {{ $request->unit?->name ?? '' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Approval Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-shield-check-line me-2"></i>Approval Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Approved By</small>
                                <strong>{{ $request->approvedBy?->name ?? $request->approver_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Approval Date</small>
                                <strong>{{ $request->approved_at ? \Carbon\Carbon::parse($request->approved_at)->format('d M Y') : 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Approval Remarks</small>
                                <strong>{{ $request->remarks ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right: Dispatch Action --}}
            <div class="col-lg-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="ri-truck-line me-2"></i>Confirm Dispatch</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('dispatch.store', $request->id) }}">
                            @csrf
                            <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Dispatch Date</label>
                                <input type="date" name="dispatched_at" class="form-control"
                                       value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">MRN Number</label>
                                <input type="text" name="mrn_number" class="form-control"
                                       placeholder="e.g. MRN-2026-001">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Courier Name</label>
                                <input type="text" name="courier_name" class="form-control"
                                       placeholder="e.g. FedEx, DHL, Speed Post">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control"
                                       placeholder="Courier contact person name">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="number" name="contact_number" class="form-control"
                                       placeholder="e.g. +91 98765 43210">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-control"
                                       placeholder="e.g. 1Z999AA10123456784">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload Photo</label>
                                <input type="file" name="dispatchUpload" class="form-control" >
                                <small class="text-muted">Max 5 images (JPG, PNG, GIF — max 2MB each)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="3"
                                          placeholder="Add any dispatch notes..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-send-plane-line me-1"></i> Confirm Dispatch
                                </button>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Notes --}}
                @if($request->notes)
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-sticky-note-line me-2"></i>Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted small">{{ $request->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
