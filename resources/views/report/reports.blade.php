@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 ">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Reports
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">Manage and view all inventory reports</p>
                </div>
                <!-- Report Filters -->
                <div class="d-none">
                    <form class="row g-3" method="GET" action="{{ route('report.reports') }}">
                        <!--<div class="col-md-3">
                            <label for="reportType" class="form-label">Report Type</label>
                            <select class="form-select form-select-sm" id="reportType" name="type">
                                <option value="">All Reports</option>
                                <option value="inventory">Inventory Status</option>
                                <option value="storage">Storage Analytics</option>
                                <option value="batch">Batch Management</option>
                                <option value="accession">Accession Tracking</option>
                            </select>
                        </div>-->
                        <div class="col-md-4">
                            <label for="dateFrom" class="form-label">Date From</label>
                            <input type="date" class="form-control form-control-sm" id="dateFrom" name="date_from">
                        </div>
                        <div class="col-md-4">
                            <label for="dateTo" class="form-label">Date To</label>
                            <input type="date" class="form-control form-control-sm" id="dateTo" name="date_to">
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-50">Search</button>
                            <button type="reset" class="btn btn-sm btn-outline-secondary flex-grow-1">Reset</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reports Grid -->
            <div class="row g-4">
                <!-- Inventory Status Report -->
                <div class="col-md-3 col-lg-2 d-none">
                    <div class="card shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle bg-primary-transparent text-primary" style="font-size: 24px;">
                                        <i class="ri-database-2-line"></i>
                                    </span>
                                </div>
                                <div class="">
                                    <h5 class="card-title mb-2 ms-3">Inventory Status</h5>
                                    <div class="d-flex gap-2 ms-3">
                                        <a class="fs-15" href="" title="View Report">
                                            <i class="ri-eye-line text-info"></i>
                                        </a>
                                        <div class="vr"></div>
                                        <a class="fs-15" href="" title="View Export Options">
                                            <i class="ri-file-download-line text-secondary"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-1">Current inventory levels across all storage locations</p>
                            
                        </div>
                    </div>
                </div>

                <!-- Storage Analytics -->
                <div class="col-md-3 col-lg-2 d-none">
                    <div class="card shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle bg-info-transparent text-info" style="font-size: 24px;">
                                        <i class="ri-building-4-line"></i>
                                    </span>
                                </div>
                                <div class="">
                                    <h5 class="card-title mb-2 ms-3">Storage Analytics</h5>
                                    <div class="d-flex gap-2 ms-3">
                                        <a class="fs-15" href="" title="View Report">
                                            <i class="ri-eye-line text-info"></i>
                                        </a>
                                        <div class="vr"></div>
                                        <a class="fs-15" href="" title="View Export Options">
                                            <i class="ri-file-download-line text-secondary"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-1">Warehouse utilization and storage performance metrics</p>
                            
                        </div>
                    </div>
                </div>

                <!-- Batch Management Report -->
                <div class="col-md-3 col-lg-2 d-none">
                    <div class="card shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle bg-success-transparent text-success" style="font-size: 24px;">
                                        <i class="ri-box-2-line"></i>
                                    </span>
                                </div>
                                <div class="">
                                    <h5 class="card-title mb-2 ms-3">Lot Management</h5>
                                    <div class="d-flex gap-2 ms-3">
                                        <a class="fs-15" href="" title="View Report">
                                            <i class="ri-eye-line text-info"></i>
                                        </a>
                                        <div class="vr"></div>
                                        <a class="fs-15" href="" title="View Export Options">
                                            <i class="ri-file-download-line text-secondary"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-1">Batch creation, tracking, and expiration details</p>
                        </div>
                    </div>
                </div>

                <!-- Accession Tracking Report -->
                <div class="col-md-3 col-lg-2 ">
                    <div class="card shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle bg-warning-transparent text-warning" style="font-size: 24px;">
                                        <i class="ri-bookmark-line"></i>
                                    </span>
                                </div>
                                <div class="">
                                    <h5 class="card-title mb-2 ms-3">Accession Tracking</h5>
                                    <div class="d-flex gap-2 ms-3">
                                        <a class="fs-15" href="{{ route('report.summary') }}" title="View Report">
                                            <i class="ri-eye-line text-info"></i>
                                        </a>
                                        <div class="vr"></div>
                                        <a class="fs-15" href="{{ route('report.request.download') }}" title="View Export Options">
                                            <i class="ri-file-download-line text-secondary"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-1">Germplasm accession details and seed distribution</p>
                        </div>
                    </div> 
                </div>

                <!-- Low Stock Alert Report -->
                <div class="col-md-3 col-lg-2">
                    <div class="card shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle bg-danger-transparent text-danger" style="font-size: 24px;">
                                        <i class="ri-alarm-warning-line"></i>
                                    </span>
                                </div>
                                <div class="">
                                    <h5 class="card-title mb-2 ms-3">Low Stock Alerts</h5>
                                    <div class="d-flex gap-2 ms-3">
                                        <a class="fs-15" href="{{ route('accession.lowStockReport') }}" title="View Report">
                                            <i class="ri-eye-line text-info"></i>
                                        </a>
                                        <div class="vr"></div>
                                        <a class="fs-15" href="{{ route('report.request.download') }}" title="View Export Options">
                                            <i class="ri-file-download-line text-secondary"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-1">Items below minimum threshold requiring attention</p>
                        </div>
                    </div>
                </div>

                <!-- Custom Reports -->
                <div class="col-md-3 col-lg-2">
                    <div class="card shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle bg-secondary-transparent text-secondary" style="font-size: 24px;">
                                        <i class="ri-settings-4-line"></i>
                                    </span>
                                </div>
                                <div class="">
                                    <h5 class="card-title mb-2 ms-3">Request Reports</h5>
                                    <div class="d-flex gap-2 ms-3">
                                        <a class="fs-15" href="{{ route('report.request') }}" title="View Report">
                                            <i class="ri-eye-line text-info"></i>
                                        </a>

                                        <div class="vr"></div>

                                        <a class="fs-15" href="{{ route('report.request.download') }}" title="Download Report">
                                            <i class="ri-file-download-line text-secondary"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-1">Build and schedule request reports based on your needs</p>
                            <!--<div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary w-100">Create</button>
                                <button class="btn btn-sm btn-outline-secondary w-100">Manage</button>
                            </div>-->
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-lg-2">
                    <div class="card shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle bg-secondary-transparent text-secondary" style="font-size: 24px;">
                                        <i class="ri-settings-4-line"></i>
                                    </span>
                                </div>
                                <div class="">
                                    <h5 class="card-title mb-2 ms-3">Expire Seed Reports</h5>
                                    <div class="d-flex gap-2 ms-3">
                                        <a class="fs-15" href="{{ route('expiry.report') }}" title="View Report">
                                            <i class="ri-eye-line text-info"></i>
                                        </a>

                                        <div class="vr"></div>

                                        <a class="fs-15" href="{{ route('expiry.report.download') }}" title="Download Report">
                                            <i class="ri-file-download-line text-secondary"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-1">Build and schedule request reports based on your needs</p>
                            <!--<div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary w-100">Create</button>
                                <button class="btn btn-sm btn-outline-secondary w-100">Manage</button>
                            </div>-->
                        </div>
                    </div>
                </div>        

            </div>
            <div class="card mt-2">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <p class="text-muted small mb-1">Total Available Qty</p>
                                <h4 class="mb-0 text-success fw-bold">{{ formatWeight($totalAvailable) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <p class="text-muted small mb-1">Total Lot Quantity</p>
                                <h4 class="mb-0 text-primary fw-bold">{{ formatWeight($totalLotQty) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <p class="text-muted small mb-1">Total Dispatched Qty</p>
                                <h4 class="mb-0 text-danger fw-bold">{{ formatWeight($totalDispatched) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <p class="text-muted small mb-1">Total Pending Requests</p>
                                <h4 class="mb-0 text-warning fw-bold">{{ formatWeight($totalRequested) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <p class="text-muted small mb-1">Total Lots Transferred</p>
                                <h4 class="mb-0 text-info fw-bold">{{ $lots }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3 ">
                <div class="card-body">
                    <h5 class="card-title mb-2">Report Filters</h5>
                    <p class="text-muted mb-0">
                    @if(request('date_from') && request('date_to'))
                        From <strong>{{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}</strong>   
                    @elseif(request('date_from'))
                        From <strong>{{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}</strong> onwards
                    @elseif(request('date_to'))
                        Up to <strong>{{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}</strong>
                    @endif
                    </p>

                    <canvas id="movementChart" style="max-height:300px;"></canvas>
                </div>

            </div>
            <!-- Recent Reports Table -->
            <div class="card mt-3 ">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transaction Reports</h5>
                    <form class="d-flex gap-2 align-items-center" method="GET" id="txnFilterForm">
                        <input type="date" name="date_from" class="form-control form-control-sm" placeholder="From" value="{{ request('date_from') }}" style="width:140px">
                        <input type="date" name="date_to"   class="form-control form-control-sm" placeholder="To"   value="{{ request('date_to') }}"   style="width:140px">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Report Name</th>
                                <th>Type</th>
                                <th>Generated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $txnReports = [
                                    ['label' => 'Arrival Transaction',       'type' => 'arrival',      'badge' => 'primary'],
                                    ['label' => 'Accessioning Transaction',  'type' => 'accessioning', 'badge' => 'info'],
                                    ['label' => 'Request Transaction',       'type' => 'request',      'badge' => 'secondary'],
                                    ['label' => 'Dispatch Transaction',      'type' => 'dispatch',     'badge' => 'success'],
                                    ['label' => 'Return Transaction',        'type' => 'return',       'badge' => 'warning'],
                                    ['label' => 'Regeneration Transaction',  'type' => 'regeneration', 'badge' => 'dark'],
                                    ['label' => 'Disposal Transaction',      'type' => 'disposal',     'badge' => 'danger'],
                                    ['label' => 'Lot Inter Change Transaction',      'type' => 'change',     'badge' => 'danger'],
                                ];
                            @endphp
                            @foreach($txnReports as $r)
                            <tr>
                                <td>{{ $r['label'] }}</td>
                                <td><span class="badge bg-{{ $r['badge'] }}">{{ ucfirst($r['type']) }}</span></td>
                                <td>{{ $today->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('report.transaction', array_merge(['type' => $r['type']], request()->only('date_from','date_to'))) }}"
                                       class="btn btn-sm btn-outline-info" title="View Report">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route('report.transaction.download', array_merge(['type' => $r['type']], request()->only('date_from','date_to'))) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Download CSV">
                                        <i class="ri-file-download-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const data = @json($chartData ?? []);

    if (!data.length) return;

    new Chart(document.getElementById('movementChart'), {
        type: 'line',
        data: {
            labels: data.map(d => d.date),
            datasets: [
                {
                    label: 'Arrival (Lots)',
                    data: data.map(d => d.arrival),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.08)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                },
                {
                    label: 'Dispatch',
                    data: data.map(d => d.dispatch),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25,135,84,0.08)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                },
                {
                    label: 'Requests',
                    data: data.map(d => d.request),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255,193,7,0.08)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: {
                    display: true,
                    text: 'Movement Chart — Last 15 Days'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
});
</script>
@endpush


<style>
    .hover-shadow { transition: box-shadow 0.3s ease; }
    .hover-shadow:hover { box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important; }
    .bg-primary-transparent   { background-color: rgba(13,110,253,0.1) !important; }
    .bg-info-transparent      { background-color: rgba(23,162,184,0.1) !important; }
    .bg-success-transparent   { background-color: rgba(25,135,84,0.1)  !important; }
    .bg-warning-transparent   { background-color: rgba(255,193,7,0.1)  !important; }
    .bg-danger-transparent    { background-color: rgba(220,53,69,0.1)  !important; }
    .bg-secondary-transparent { background-color: rgba(108,117,125,0.1)!important; }
</style>

