@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Generate MRN</h3>
                <p class="text-muted mb-0" style="font-size:13px">Confirm dispatch and generate Material Release Note for ITN <strong>{{ $itn->itn_number }}</strong></p>
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

            {{-- Left: ITN Summary --}}
            <div class="col-lg-8">

                {{-- Transfer Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-swap-box-line me-2"></i>Transfer Details — ITN: {{ $itn->itn_number }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <small class="text-muted d-block">ITN Date</small>
                                <strong>{{ \Carbon\Carbon::parse($itn->itn_date)->format('d M Y') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Receiver</small>
                                <strong>{{ $itn->receiver ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Mobile</small>
                                <strong>{{ $itn->mobile_number ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Email</small>
                                <strong>{{ $itn->email ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Warehouse Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-building-line me-2"></i>Warehouse / Storage</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <small class="text-muted d-block">From Warehouse</small>
                                <strong>{{ $itn->fromWarehouse->name ?? '-' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">From Storage</small>
                                <strong>{{ $itn->fromStorage->name ?? '-' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">To Warehouse</small>
                                <strong>{{ $itn->toWarehouse->name ?? '-' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">To Storage</small>
                                <strong>{{ $itn->toStorage->name ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Batch Lots --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="ri-stack-line me-2"></i>Lots in this Transfer</h6>
                        <span class="badge bg-white text-dark">{{ $batchLots->count() }} lot(s)</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Lot No.</th>
                                    <th>Crop</th>
                                    <th>Accession No.</th>
                                    <th>Storage</th>
                                    <th class="text-end">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batchLots as $i => $bt)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><span class="badge bg-primary">{{ $bt->lot->lot_number ?? '-' }}</span></td>
                                    <td>{{ $bt->lot->crop->crop_name ?? '-' }}</td>
                                    <td>{{ $bt->lot->accession->accession_number ?? '-' }}</td>
                                    <td>{{ $bt->lot->storage->name ?? '-' }}</td>
                                    <td class="text-end">{{ $bt->quantity }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted">No lots found</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Total Quantity</th>
                                    <th class="text-end">{{ $batchLots->sum('quantity') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if($itn->instructions)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="ri-sticky-note-line me-2"></i>Instructions</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted small">{{ $itn->instructions }}</p>
                    </div>
                </div>
                @endif

            </div>

            {{-- Right: MRN Form --}}
            <div class="col-lg-4">
                <div class="card border-success sticky-top" style="top: 80px;">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="ri-file-text-line me-2"></i>Generate MRN</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('dispatch.itnStore', $itn->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Dispatch Date</label>
                                    <input type="date" name="dispatched_at" class="form-control"
                                           value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">MRN Number</label>
                                    <input type="text" name="mrn_number" class="form-control"
                                           placeholder="MRN-2026-001">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Courier Name</label>
                                    <input type="text" name="courier_name" class="form-control"
                                           placeholder="e.g. FedEx, DHL, Speed Post">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Contact Person</label>
                                    <input type="text" name="contact_person" class="form-control"
                                           value="{{ $itn->receiver }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control"
                                           value="{{ $itn->mobile_number }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tracking Number</label>
                                    <input type="text" name="tracking_number" class="form-control"
                                           placeholder="e.g. 1Z999AA10123456784">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Upload Photo</label>
                                    <input type="file" name="dispatchUpload" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="2"
                                              placeholder="Add any dispatch notes..."></textarea>
                                </div>
                                <div class="col-12 d-grid mt-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-file-text-line me-1"></i> Generate MRN &amp; Print
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
