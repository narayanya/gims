@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">{{ $title }}</h3>
                <p class="text-muted mb-0" style="font-size:13px">
                    @if($dateFrom || $dateTo)
                        {{ $dateFrom ? 'From: '.$dateFrom : '' }} {{ $dateTo ? ' To: '.$dateTo : '' }}
                    @else
                        All records
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                {{-- Date filter --}}
                <form method="GET" action="{{ route('report.transaction', $type) }}" class="d-flex gap-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" style="width:140px">
                    <input type="date" name="date_to"   class="form-control form-control-sm" value="{{ $dateTo }}"   style="width:140px">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('report.transaction', $type) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </form>
                <a href="{{ route('report.transaction.download', array_merge(['type' => $type], array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo]))) }}"
                   class="btn btn-sm btn-success">
                    <i class="ri-file-download-line me-1"></i> Download CSV
                </a>
                <a href="{{ route('report.reports') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                @if($records->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ri-file-list-3-line fs-1"></i>
                        <p class="mt-2">No records found for this period.</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            @if(in_array($type, ['arrival', 'accessioning', 'disposal']))
                            <tr>
                                <th>#</th>
                                <th>Accession No</th>
                                <th>Accession Name</th>
                                <th>Crop</th>
                                <th>Variety</th>
                                <th>Quantity</th>
                                <th>Warehouse</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            @elseif(in_array($type, ['request', 'dispatch', 'return']))
                            <tr>
                                <th>#</th>
                                <th>Request No</th>
                                <th>Requester</th>
                                <th>Crop</th>
                                <th>Variety</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            @elseif($type === 'regeneration')
                            <tr>
                                <th>#</th>
                                <th>Lot Number</th>
                                <th>Accession</th>
                                <th>Crop</th>
                                <th>Lot Type</th>
                                <th>Quantity</th>
                                <th>Storage</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            @endif
                        </thead>
                        <tbody>
                            @foreach($records as $i => $r)
                            @if(in_array($type, ['arrival', 'accessioning', 'disposal']))
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><span class="badge bg-light text-dark">{{ $r->accession_number }}</span></td>
                                <td>{{ $r->accession_name }}</td>
                                <td>{{ $r->crop?->crop_name ?? '—' }}</td>
                                <td>{{ number_format($r->quantity ?? 0, 2) }}</td>
                                <td>{{ $r->warehouse?->name ?? '—' }}</td>
                                <td><span class="badge bg-{{ $r->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($r->status) }}</span></td>
                                <td>{{ $r->created_at?->format('d M Y') }}</td>
                            </tr>
                            @elseif(in_array($type, ['request', 'dispatch', 'return']))
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><span class="badge bg-light text-dark">{{ $r->request_number }}</span></td>
                                <td>{{ $r->user?->name ?? $r->requester_name ?? '—' }}</td>
                                <td>{{ $r->crop?->crop_name ?? '—' }}</td>
                                <td>{{ $r->quantity }}</td>
                                <td>
                                    @php $sc = match($r->status) { 'approved','completed','dispatched' => 'success', 'pending' => 'warning', 'rejected' => 'danger', default => 'secondary' }; @endphp
                                    <span class="badge bg-{{ $sc }}">{{ ucfirst($r->status) }}</span>
                                </td>
                                <td>{{ $r->created_at?->format('d M Y') }}</td>
                            </tr>
                            @elseif($type === 'regeneration')
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><span class="badge bg-primary">{{ $r->lot_number }}</span></td>
                                <td>{{ $r->accession?->accession_number ?? '—' }}</td>
                                <td>{{ $r->crop?->crop_name ?? '—' }}</td>
                                <td>{{ $r->lotType?->name ?? '—' }}</td>
                                <td>{{ number_format($r->quantity ?? 0, 2) }}</td>
                                <td>{{ $r->storage?->name ?? '—' }}</td>
                                <td><span class="badge bg-{{ $r->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($r->status) }}</span></td>
                                <td>{{ $r->created_at?->format('d M Y') }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted small">
                    Total records: {{ $records->count() }}
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
