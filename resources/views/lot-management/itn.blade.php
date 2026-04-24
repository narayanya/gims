@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Internal Transfer Note</h3>
                <p class="text-muted mb-0" style="font-size:13px">Move a Warehouse from one location to another location</p>
            </div>
            <a href="{{ route('warehouse-transfer.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Back to Lot List
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

            <form id="itnSubmitForm"
      method="POST"
      action="{{ route('warehouse-transfer.process.itn') }}"
      enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="transfer_id" value="{{ $t->id }}">
            <div class="row g-3">
                {{-- ── FROM ── --}}
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-header bg-light text-dark">
                            <h6 class="mb-0"><i class="ri-map-pin-line me-1"></i>Internal Transfer Note</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                
                                    <table class="table table-bordered">
                                        <tr>
                                    <th>Date</th>
                                    <td>{{ $t->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>From Warehouse</th>
                                    <td>{{ $t->fromWarehouse->name ?? '-' }} <br><small class="text-muted" id="fw_address">Loading address...</small></td>

                                    <th>From Storage</th>
                                    <td>{{ $t->fromStorage->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>To Warehouse</th>
                                    <td>{{ $t->toWarehouse->name ?? '-' }} <br><small class="text-muted" id="tw_address">Loading address...</small></td>

                                    <th>To Storage</th>
                                    <td>{{ $t->toStorage->name ?? '-' }}</td>
                                </tr>
                                    </table>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Crop</th>    
                                                <th>Accession No.</th>    
                                                <th>Lot No.</th>    
                                                <th>Qty.</th>    
                                            </tr>
                                            </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $t->lot->crop->crop_name ?? '-' }}</td>
                                                <td>{{ $t->lot->accession->accession_number ?? '-' }}</td>
                                                <td>{{ $t->lot->lot_number ?? '-' }}</td>
                                                <td>{{ $t->quantity }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── TO ── --}}
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="ri-map-pin-2-line me-1"></i>ITN Processing</h6>
                        </div>
                        <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                    <label class="form-label">ITN Date</label>
                                    <input type="date" name="itn_date" class="form-control"
                                           value="{{ now()->format('Y-m-d') }}">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">ITN Number</label>
                                    <input type="text" name="itn_number" class="form-control"
                                           placeholder="ITN-2026-001">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Receiver Name <span class="text-danger">*</span></label>
                                    <input type="text" name="receiver" class="form-control" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile_number" class="form-control" required>
                                </div>

                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Upload Photo</label>
                                    <input type="file" name="dispatchUpload" class="form-control">
                                    <small class="text-muted">Max 5 images (JPG, PNG, GIF — max 2MB each)</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Special Instructions / Handling</label>
                                    <textarea name="instructions" class="form-control"></textarea>
                                </div>
                    </div>
                

                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-send-plane-line me-1"></i> Process ITN
                            </button>
                        </div>
                    
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Get the data from PHP into JS objects
    const fromWarehouse = @json($t->fromWarehouse);
    const toWarehouse = @json($t->toWarehouse);

    // 2. Helper function to format the address string
    function formatAddress(w) {
        if (!w) return '—';
        return [
            w.city?.city_village_name,
            w.district?.district_name,
            w.state?.state_name,
            w.country?.country_name
        ].filter(Boolean).join(', ') || '—';
    }

    // 3. Update the UI
    document.getElementById('fw_address').innerText = formatAddress(fromWarehouse);
    document.getElementById('tw_address').innerText = formatAddress(toWarehouse);
});
</script>
@endpush