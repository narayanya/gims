@extends('layouts.app')

@section('content')
 <div class="row justify-content-center">
        <div class="col-12 ">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Reports Summary
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Overview of key metrics and statistics</p>
                </div>
            </div>
            <div class="card mt-3 ">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Crop Reports</h5>
                    <form class="d-flex gap-2 align-items-center" method="GET" id="txnFilterForm">
                        <select name="crop_id" class="form-select form-select-sm" onchange="document.getElementById('txnFilterForm').submit()">
                            <option value="">All Crops</option>
                            @foreach($crops as $crop)
                                <option value="{{ $crop->id }}" {{ request('crop_id') == $crop->id ? 'selected' : '' }}>
                                    {{ $crop->crop_name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="date" name="date_from" class="form-control form-control-sm" placeholder="From" value="{{ request('date_from') }}" style="width:140px">
                        <input type="date" name="date_to"   class="form-control form-control-sm" placeholder="To"   value="{{ request('date_to') }}"   style="width:140px">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Crop</th>
                                    <th>Total Accessions</th>
                                    <th>Total Lots</th>
                                    <th>Total Qty (Gram)</th>
                                    <th>Total Visibility Qty (Gram)</th>
                                    <th>Total Requested Qty (Gram)</th>
                                    <th>Total Dispatched Qty (Gram)</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary as $index => $row)
                                <tr>
                                    <td>{{ $row->crop_name }}</td>
                                    <td>{{ $row->total_accessions }}</td>
                                    <td>{{ $row->total_lots }}</td>
                                    <td>{{ $row->total_quantity ?? 0 }}</td>
                                    <td>{{ $row->total_quantity_show ?? 0 }}</td>
                                    <td>{{ $row->total_requested ?? 0 }}</td>
                                    <td>{{ $row->total_dispatched ?? 0 }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary toggleDetails"
                                                data-target="details-{{ $index }}">
                                            <i class="ri-eye-line me-1"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                                <!-- 🔽 Hidden Expand Row -->
<tr id="details-{{ $index }}" class="d-none">
    <td colspan="7">
        <div class="p-1 bg-light border rounded">

            <table class="table table-sm table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Accession No</th>
                        <th>Lot No</th>
                        <th>Quantity</th>
                        <th>Visibility Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($row->details ?? [] as $d)
                    <tr>
                        <td>
                            <a href="{{ route('report.accession.history', $d->accession_id ?? $d->id) }}"
                               class="text-decoration-none fw-semibold">
                                {{ $d->accession_number }}
                            </a>
                        </td>
                        <td>
                            @if($d->lot_id ?? null)
                                <a href="{{ route('report.lot.history', $d->lot_id) }}" class="text-decoration-none fw-semibold">
                                    {{ $d->lot_number ?? '-' }}
                                </a>
                            @else
                                {{ $d->lot_number ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $d->quantity }}</td>
                        <td>{{ $d->quantity_show }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </td>
</tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-3 ">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Accession/Lot Reports</h5>
                    <form class="d-flex gap-2 align-items-center" method="GET" id="txnFilterForm">
                        <select name="accession_id" class="form-select form-select-sm" onchange="document.getElementById('txnFilterForm').submit()">
                            <option value="">All Accessions</option>
                            @foreach($accessions as $accession)
                                <option value="{{ $accession->id }}" {{ request('accession_id') == $accession->id ? 'selected' : '' }}>
                                    {{ $accession->accession_number }}
                                </option>
                            @endforeach
                        </select>
                        <select name="lot_id" class="form-select form-select-sm" onchange="document.getElementById('txnFilterForm').submit()">
                            <option value="">All Lot</option>
                            @foreach($lots as $lot)
                                <option value="{{ $lot->id }}" {{ request('lot_id') == $lot->id ? 'selected' : '' }}>
                                    {{ $lot->lot_number }}
                                </option>
                            @endforeach
                        </select>
                        <input type="date" name="date_from" class="form-control form-control-sm" placeholder="From" value="{{ request('date_from') }}" style="width:140px">
                        <input type="date" name="date_to"   class="form-control form-control-sm" placeholder="To"   value="{{ request('date_to') }}"   style="width:140px">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Accession Name</th>
                                    <th>Accessions Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accessions as $accession)
                                <tr>
                                    <td>{{ $accession->accession_name }}</td>
                                    <td>{{ $accession->accession_number }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
             
            
        </div>
 </div>
<script>
document.querySelectorAll('.toggleDetails').forEach(btn => {
    btn.addEventListener('click', function() {

        let targetId = this.dataset.target;

        document.querySelectorAll('[id^="details-"]').forEach(row => {
            if (row.id !== targetId) row.classList.add('d-none');
        });

        let row = document.getElementById(targetId);
        row.classList.toggle('d-none');
    });
});
</script>
@endsection