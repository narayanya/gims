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

                

                {{-- Requester Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-user-line me-2"></i>Requester Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Name</small>
                                <strong>{{ $itn->requester_name ?? $itn->user?->name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Email</small>
                                <strong>{{ $itn->requester_email ?? $itn->user?->email ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Receiver Name</small>
                                <strong>{{ $itn->receiver_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Destination</small>
                                <strong>{{ $itn->destination ?? 'N/A' }}</strong>
                            </div>
                            
                            <div class="col-md-4">
                                <small class="text-muted d-block">Requester- Reporting/Status</small>
                               <strong>
                                    {{ $itn->user?->reportingUser?->email ?? 'N/A' }}
                                    <span class="badge bg-success">Approved</span>
                                </strong>
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
                                <strong>{{ $itn->crop?->crop_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Accession Name</small>
                                <strong>{{ $itn->accession?->accession_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Accession No.</small>
                                <strong>{{ $itn->accession?->accession_number ?? 'N/A' }}</strong>
                            </div>
                            
                            <div class="col-md-4">
                                <small class="text-muted d-block">Expiry Date</small>
                                <strong>
                                    {{ optional($itn->accession?->expiry_date)->format('d M Y') ?? 'N/A' }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Storage --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-seedling-line me-2"></i>Storage Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">From Warehouse</small>
                                <strong>{{ $itn->fromWarehouse->name ?? '-' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">To Warehouse</small>
                                <strong>{{ $itn->toWarehouse->name ?? '-' }}</strong>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">From Storage</small>
                                <strong>{{ $itn->fromStorage->name ?? '-' }}</strong>
                            </div>
                            
                            <div class="col-md-4">
                                <small class="text-muted d-block">To Storage</small>
                                <strong>
                                    {{ $itn->toStorage->name ?? '-' }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lots & Seed Quantities --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-stack-line me-2"></i>Lots & Seed Quantities</h6>
                    </div>
                    @if(isset($lots) && $lots->count())
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Lot No.</th>
                                        <th>Storage</th>
                                        <th>Total Qty</th>
                                        <th>Available (User)</th>
                                        <th>Min Stock</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lots as $lot)
                                    @php
                                        $sqs = isset($seedQuantities) ? ($seedQuantities->get($lot->id) ?? $seedQuantities->get('unlinked') ?? collect()) : collect();
                                        $sq  = $sqs->sortByDesc('id')->first();
                                    @endphp
                                    <tr>
                                        <td><span class="badge bg-primary">{{ $lot->lot_number }}</span></td>
                                        <td>{{ $lot->storage?->name ?? '—' }}</td>
                                        <td>{{ $sq ? number_format($sq->quantity, 2) : number_format($lot->quantity ?? 0, 2) }}</td>
                                        <td><strong class="text-success">{{ $sq ? number_format($sq->quantity_show, 2) : '—' }}</strong></td>
                                        <td>{{ $sq?->min_quantity ? number_format($sq->min_quantity, 2) : '—' }}</td>
                                        <td>{{ $sq?->unit?->name ?? $request->accession?->capacityUnit?->name ?? '—' }}</td>
                                        <td><span class="badge {{ $lot->status == 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($lot->status) }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-semibold">
                                    <tr>
                                        <td colspan="2">Total</td>
                                        <td>{{ number_format(isset($seedQuantities) ? $seedQuantities->flatten()->sum('quantity') : 0, 2) }}</td>
                                        <td class="text-success">{{ number_format(isset($seedQuantities) ? $seedQuantities->flatten()->sum('quantity_show') : 0, 2) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="card-body text-muted small">No lots found for this accession.</div>
                    @endif
                </div>


            </div>

            {{-- Right: Dispatch Action --}}
            <div class="col-lg-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="ri-truck-line me-2"></i>Confirm Dispatch</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('dispatch.itnStore', $itn->id) }}">
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
                                       placeholder="Courier contact person name" value="{{ $itn->receiver }}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="number" name="contact_number" class="form-control"
                                       placeholder="e.g. +91 98765 43210" value="{{ $itn->mobile_number }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-control"
                                       placeholder="e.g. 1Z999AA10123456784">
                            </div>
                            <div class="mb-3 col-md-6">
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
                @if($itn->notes)
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-sticky-note-line me-2"></i>Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted small">{{ $itn->instructions }}</p>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
