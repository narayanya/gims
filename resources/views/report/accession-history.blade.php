@extends('layouts.app')

@section('content')
<div class="col-12">

    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h3 class="text-xl font-bold">
                <i class="ri-history-line me-2 text-primary"></i>Accession History
            </h3>
            <p class="text-muted mb-0" style="font-size:13px">
                Full lifecycle timeline for
                <strong>{{ $accession->accession_number }}</strong> — {{ $accession->accession_name }}
            </p>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    {{-- ── Accession Summary Card ── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2 col-sm-4 text-center">
                    @if($accession->images->first())
                    <img src="{{ asset('storage/accessions/images/' . $accession->images->first()->image_name) }}"
                         class="img-thumbnail rounded" style="width:80px;height:80px;object-fit:cover;">
                    @else
                    <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:80px;height:80px;margin:auto;">
                        <i class="ri-seedling-line text-success fs-2"></i>
                    </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <div class="row g-2 small">
                        <div class="col-md-3"><span class="text-muted">Accession No.</span><br><strong>{{ $accession->accession_number }}</strong></div>
                        <div class="col-md-3"><span class="text-muted">Name</span><br><strong>{{ $accession->accession_name ?? '—' }}</strong></div>
                        <div class="col-md-3"><span class="text-muted">Crop</span><br><strong>{{ $accession->crop?->crop_name ?? '—' }}</strong></div>
                        <div class="col-md-3"><span class="text-muted">Status</span><br>
                            <span class="badge {{ $accession->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                                {{ $accession->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="col-md-3"><span class="text-muted">Storage Time</span><br><strong>{{ $accession->storageTime?->name ?? '—' }}</strong></div>
                        <div class="col-md-3"><span class="text-muted">Total Qty (Available)</span><br>
                            <strong class="text-success">{{ number_format($seedQuantities->sum('quantity_show'), 2) }} {{ $accession->capacityUnit?->code ?? '' }}</strong>
                        </div>
                        <div class="col-md-3"><span class="text-muted">Expiry Date</span><br><strong>{{ $accession->expiry_date?->format('d M Y') ?? '—' }}</strong></div>
                        <div class="col-md-3"><span class="text-muted">Recheck Date</span><br><strong>{{ $accession->recheck_date?->format('d M Y') ?? '—' }}</strong></div>
                    </div>
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
                <div class="card-body" style="max-height:600px;overflow-y:auto;">
                    <div class="timeline">
                        @forelse($timeline as $event)
                        <div class="d-flex gap-3 mb-4">
                            {{-- Icon --}}
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-{{ $event['color'] }} bg-opacity-15 d-flex align-items-center justify-content-center"
                                     style="width:38px;height:38px;min-width:38px;">
                                    <i class="{{ $event['icon'] }} text-{{ $event['color'] }}"></i>
                                </div>
                            </div>
                            {{-- Content --}}
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
        </div>

        {{-- ── Right: Stats & Lots ── --}}
        <div class="col-lg-5">

            {{-- Quick Stats --}}
            <div class="row g-2 mb-3">
                @php
                    $statCards = [
                        ['label' => 'Lots',       'value' => $lots->count(),       'color' => 'primary',   'icon' => 'ri-stack-line'],
                        ['label' => 'Transfers',  'value' => $transfers->count(),  'color' => 'info',      'icon' => 'ri-swap-box-line'],
                        ['label' => 'Requests',   'value' => $requests->count(),   'color' => 'secondary', 'icon' => 'ri-file-list-3-line'],
                        ['label' => 'Dispatches', 'value' => $dispatches->count(), 'color' => 'danger',    'icon' => 'ri-truck-line'],
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

            {{-- Lots Table --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="ri-stack-line me-2"></i>Lots</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Lot No.</th><th>Storage</th><th class="text-end">Qty</th></tr>
                        </thead>
                        <tbody>
                            @forelse($lots as $lot)
                            <tr>
                                <td>
                                    <a href="{{ route('report.lot.history', $lot->id) }}" class="text-decoration-none">
                                        <span class="badge bg-primary">{{ $lot->lot_number }}</span>
                                    </a>
                                </td>
                                <td class="small">{{ $lot->storage?->name ?? '—' }}</td>
                                <td class="text-end small">{{ number_format($lot->seedQuantities->sum('quantity'), 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No lots</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Requests Summary --}}
            @if($requests->count())
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="ri-file-list-3-line me-2"></i>Requests</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Request No.</th><th>Requester</th><th>Qty</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $r)
                            <tr>
                                <td class="small">{{ $r->request_number }}</td>
                                <td class="small">{{ $r->requester_name ?? $r->user?->name }}</td>
                                <td class="small">{{ $r->quantity }}</td>
                                <td>
                                    @php $sc = match($r->status) { 'approved'=>'success','rejected'=>'danger','dispatched'=>'info','returned'=>'secondary', default=>'warning' }; @endphp
                                    <span class="badge bg-{{ $sc }}">{{ ucfirst($r->status) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Dispatches Summary --}}
            @if($dispatches->count())
            <div class="card shadow-sm">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="ri-truck-line me-2"></i>Dispatches</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Dispatch No.</th><th>MRN</th><th>Qty</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            @foreach($dispatches as $d)
                            <tr>
                                <td class="small">{{ $d->dispatch_number }}</td>
                                <td class="small">{{ $d->mrn_number }}</td>
                                <td class="small">{{ $d->quantity }}</td>
                                <td class="small">{{ $d->created_at->format('d M Y') }}</td>
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
