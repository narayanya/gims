@extends('layouts.app')

@section('content')
    <div class="row justify-content-center p-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Lot Full Details
                    </h3>
                </div>
            </div>

    <!-- HEADER -->
                {{-- Lot Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-stack-line me-1"></i>Lot Information</strong></div>
                    <div class="card-body">
                        <div class="row g-3 small">
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Number</span>
                                {{ $lot->lot_number }}
                            </div>

                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Status</span>
                                {{ $lot->status }}
                            </div>

                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Expiry Date</span>
                                {{ $lot->expiry_date }}
                            </div>

                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Regeneration Date</span>
                                {{ $lot->regeneration_date }}
                            </div>

                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Regeneration Year</span>
                                {{ $lot->regeneration_year }}
                            </div>

                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Regeneration Program</span>
                                {{ $lot->regeneration_program }}
                            </div>

                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Prefix</span>
                                {{ $lot->prefix }}
                            </div>

                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Sample Id</span>
                                {{ $lot->sample_id }}
                            </div>

                            <div class="col-12">
                                <span class="text-muted d-block">Description</span>
                                {{ $lot->description }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Accession Details --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-seedling-line me-1"></i>Accession Details</strong></div>
                    <div class="card-body">
                        <div class="row g-3 small" id="vl_accession_section">
                            <div class="col-12 col-md-3"><span class="text-muted d-block">Accession No.</span>{{ $lot->accession?->accession_number ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Crop</span>{{ $lot->accession?->crop?->crop_name ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Scientific Name</span>{{ $lot->accession?->crop?->scientific_name ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Quantity</span>{{ $lot->accession?->quantity ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Status</span>{{ $lot->accession?->status ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Quantity Parameters --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-test-tube-line me-1"></i>Quantity Parameters</strong></div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref No.</th>
                                    <th>Seeds</th>
                                    <th>Bags</th>
                                    <th>Seed Wt (g)</th>
                                    <th>Quantity</th>
                                    <th>Min Stock</th>
                                    <th>Visible</th>
                                    <th>Unit</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody id="vl_quantity_tbody">
                                <tr><td colspan="9" class="text-center text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Quality Parameters --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-test-tube-line me-1"></i>Quality Parameters</strong></div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Germination %</th>
                                    <th>Moisture %</th>
                                    <th>Purity %</th>
                                    <th>Health</th>
                                    <th>Viability Date</th>
                                    <th>Researcher</th>
                                    <th>Research Date</th>
                                </tr>
                            </thead>
                            <tbody id="vl_quality_tbody">
                                <tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Storage Details --}}
                <div class="card mb-0">
                    <div class="card-header bg-light py-2"><strong class="small"><i class="ri-archive-line me-1"></i>Storage Details</strong></div>
                    <div class="card-body">
                        <div class="row g-3 small" id="vl_storage_section">
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Warehouse</span>{{ $lot->storage?->warehouse?->name ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Storage Name</span>{{ $lot->storage?->name ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Storage ID</span>{{ $lot->storage?->storage_id ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Type</span>{{ $lot->storage?->storagetype?->name ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Condition</span>{{ $lot->storage?->storagecondition?->name ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Temperature</span>{{ $lot->storage?->temperature ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Humidity</span>{{ $lot->storage?->humidity ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Capacity</span>{{ $lot->storage?->capacity ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Rack</span>{{ $lot->rack?->name ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Bin</span>{{ $lot->bin?->name ?? '—' }}</div>
                            <div class="col-6 col-md-3"><span class="text-muted d-block">Container</span>{{ $lot->container?->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>             
    </div>
</div>

@endsection
