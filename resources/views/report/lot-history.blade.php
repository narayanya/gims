@extends('layouts.app')

@section('content')
<div class="col-12">

    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h3 class="text-xl font-bold">
                <i class="ri-stack-line me-2 text-primary"></i>Lot History
            </h3>
            <p class="text-muted mb-0" style="font-size:13px">
                Full lifecycle timeline for lot
                <strong>{{ $lot->lot_number }}</strong>
            </p>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    {{-- ── Lot Summary Card ── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 small">
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Lot Number</span>
                    <span class="badge bg-primary fs-6">{{ $lot->lot_number }}</span>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Accession</span>
                    <a href="{{ route('report.accession.history', $lot->accession_id) }}" class="fw-semibold text-decoration-none">
                        {{ $lot->accession?->accession_number }}
                    </a>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Crop</span>
                    <strong>{{ $lot->accession?->crop?->crop_name ?? '—' }}</strong>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Arrival Type</span>
                    <span class="badge bg-secondary">{{ $lot->arrival_type ?? '—' }}</span>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Current Storage</span>
                    <strong>{{ $lot->storage?->name ?? '—' }}</strong>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Status</span>
                    <span class="badge {{ $lot->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($lot->status) }}
                    </span>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Total Quantity</span>
                    <strong class="text-success">{{ number_format($seedQuantities->sum('quantity'), 2) }}</strong>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Visible to Users</span>
                    <strong class="text-info">{{ number_format($seedQuantities->sum('quantity_show'), 2) }}</strong>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Section / Rack</span>
                    <strong>{{ $lot->section?->name ?? '—' }} / {{ $lot->rack?->name ?? '—' }}</strong>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Bin / Container</span>
                    <strong>{{ $lot->bin?->name ?? '—' }} / {{ $lot->container?->name ?? '—' }}</strong>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Created</span>
                    <strong>{{ $lot->created_at->format('d M Y') }}</strong>
                </div>
                <div class="col-md-2 col-sm-4">
                    <span class="text-muted d-block">Expiry Date</span>
                    <strong>{{ $lot->expiry_date ? \Carbon\Carbon::parse($lot->expiry_date)->format('d M Y') : '—' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- ── Left: Timeline ── --}}
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="ri-time-line me-2"></i>Activity Timeline</h6>
                    <span class="badge bg-primary">{{ $timeline->count() }} events</span>
                </div>
                <div class="card-body" style="max-height:620px;overflow-y:auto;">
                    @forelse($timeline as $event)
                    <div class="d-flex gap-3 mb-4">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-{{ $event['color'] }} bg-opacity-15 d-flex align-items-center justify-content-center"
                                 style="width:38px;height:38px;min-width:38px;">
                                <i class="{{ $event['icon'] }} text-{{ $event['color'] }}"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 border-bottom pb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="fw-semibold">{{ $event['title'] }}</span>
                                <small class="text-muted ms-2 text-nowrap">
                                    {{ $event['date'] ? \Carbon\Carbon::parse($event['date'])->format('d M Y, H:i') : '—' }}
                                </small>
                            </div>
                            <div class="text-muted small mt-1">{!! $event['body'] !!}</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">No activity recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── Right: Stats & Details ── --}}
        <div class="col-lg-5">

            {{-- Quick Stats --}}
            <div class="row g-2 mb-3">
                @php
                    $statCards = [
                        ['label'=>'Transfers',   'value'=>$transfers->count(),         'color'=>'warning',   'icon'=>'ri-swap-box-line'],
                        ['label'=>'WH Transfers','value'=>$warehouseTransfers->count(),'color'=>'primary',   'icon'=>'ri-building-line'],
                        ['label'=>'Requests',    'value'=>$requests->count(),          'color'=>'secondary', 'icon'=>'ri-file-list-3-line'],
                        ['label'=>'Dispatches',  'value'=>$dispatches->count(),        'color'=>'danger',    'icon'=>'ri-truck-line'],
                    ];
                @endphp
                @foreach($statCards as $s)
                <div class="col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-2 py-2">
                            <div class="rounded-circle bg-{{ $s['color'] }} bg-opacity-10 d-flex align-items-center justify-content-center" style="width:36px;height:36px;min-width:36px;">
                                <i class="{{ $s['icon'] }} text-{{ $s['color'] }}"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-5">{{ $s['value'] }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $s['label'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Seed Quantities --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="ri-scales-line me-2"></i>Seed Quantities</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Ref No.</th><th>Qty</th><th>Visible</th><th>Min</th><th>Unit</th></tr>
                        </thead>
                        <tbody>
                            @forelse($seedQuantities as $sq)
                            <tr>
                                <td class="small">{{ $sq->reference_number ?? '—' }}</td>
                                <td class="small fw-bold">{{ number_format($sq->quantity, 2) }}</td>
                                <td class="small text-success">{{ number_format($sq->quantity_show, 2) }}</td>
                                <td class="small text-warning">{{ number_format($sq->min_quantity ?? 0, 2) }}</td>
                                <td class="small">{{ $sq->unit?->code ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">No quantity data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Total</th>
                                <th>{{ number_format($seedQuantities->sum('quantity'), 2) }}</th>
                                <th class="text-success">{{ number_format($seedQuantities->sum('quantity_show'), 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Quality Records --}}
            @if($qualities->count())
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="ri-test-tube-line me-2"></i>Quality Records</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Date</th><th>Germ%</th><th>Moist%</th><th>Purity%</th><th>Health</th></tr>
                        </thead>
                        <tbody>
                            @foreach($qualities as $q)
                            <tr>
                                <td class="small">{{ $q->created_at->format('d M Y') }}</td>
                                <td class="small">{{ $q->germination_percentage ?? '—' }}</td>
                                <td class="small">{{ $q->moisture_content ?? '—' }}</td>
                                <td class="small">{{ $q->purity_percentage ?? '—' }}</td>
                                <td class="small">{{ $q->seed_health_status ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Transfer History --}}
            @if($transfers->count())
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="ri-swap-box-line me-2"></i>Transfer History</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Date</th><th>From</th><th>To</th><th>Open</th><th>Close</th><th>Bal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($transfers as $t)
                            <tr>
                                <td class="small">{{ $t->created_at->format('d M Y') }}</td>
                                <td class="small">{{ $t->fromStorage?->name ?? '—' }}</td>
                                <td class="small">{{ $t->toStorage?->name ?? '—' }}</td>
                                <td class="small">{{ $t->o_quantity }}</td>
                                <td class="small">{{ $t->c_quantity }}</td>
                                <td class="small fw-bold">{{ $t->b_quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
