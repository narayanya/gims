@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
            <div>
                <h3 class="text-xl font-bold">Old Dispatch list</h3>
                <p class="text-muted mb-0" style="font-size:13px">Create and manage dispatch requests</p>
            </div>
            <a href="{{ route('dispatch-management.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                            <i class="ri-history-line me-1"></i>Back to Dispatch List
                        </a>
            <!--<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLotModal">
                <i class="ri-add-line me-1"></i> Add New Dispatch
            </button>-->
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

        {{-- Lot List --}}
        <div class="row">
        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Year-wise Seed Weight Requested</h5>

                    <canvas id="seedWeightChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Total Seed Weight & Count Dispatched</h5>

                    <p class="mb-1">
                        Total Weight (kg):
                        <strong>{{ number_format($weights->sum(), 3) }}</strong>
                    </p>

                    <p class="mb-0">
                        Total Seeds:
                        <strong>{{ number_format($seeds->sum()) }}</strong>
                    </p>
                </div>
            </div>
        </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                        <h5 class="mb-0">Dispatched Orders</h5>

                        <form action="{{ route('dispatch-management.old') }}" method="GET"
                              class="d-flex flex-wrap gap-2 align-items-center">

                            <div class="input-group input-group-sm" >
                                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                <input type="date" name="from_date" style="width:150px"
                                       class="form-control form-control-sm"
                                       placeholder="From Date"
                                       value="{{ request('from_date') }}"
                                       max="{{ date('Y-m-d') }}">
                            
                                <span class="input-group-text ms-2"><i class="ri-calendar-line"></i></span>
                                <input type="date" name="to_date" style="width:150px;"
                                       class="form-control form-control-sm"
                                       placeholder="To Date"
                                       value="{{ request('to_date') }}"
                                       max="{{ date('Y-m-d') }}">
                         
                                <span class="input-group-text ms-2"><i class="ri-search-line"></i></span>
                                <input type="text" name="search" style="width:150px"
                                       class="form-control form-control-sm"
                                       placeholder="Crop, Sample ID, Person, Location…"
                                       value="{{ request('search') }}">

                                       <button type="submit" class="btn btn-sm btn-primary ms-2">
                                <i class="ri-search-line me-1"></i>Search
                            </button>

                            @if(request()->hasAny(['from_date','to_date','search']))
                                <a href="{{ route('dispatch-management.old') }}"
                                   class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="ri-refresh-line me-1"></i>Reset
                                </a>
                            @endif

                            <button type="button" class="btn btn-sm btn-success ms-2"
                                    data-bs-toggle="modal" data-bs-target="#importdispModal">
                                <i class="ri-upload-line me-1"></i>Import
                            </button>
                            </div>

                            
                        </form>
                    </div>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Crop</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Prefix</th>
                                <th>Sample ID</th>
                                <th>No. of Seeds/ Weight (kg)</th>
                                <th>No. Packets</th>
                                <th>Remarks</th>
                                <th>Concerned Person</th>
                                <th>Location</th>
                                <th>Date of request</th>
                                <th>Dispatch Date</th>
                                <th>Tracking Id</th>
                                <th>Courier Service</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($dispatches->count() > 0)
                                @foreach ($dispatches as $dispatch)
                                    <tr>
                                        <td>{{ $loop->iteration + ($dispatches->currentPage() - 1) * $dispatches->perPage() }}</td>
                                        <td>{{ $dispatch->crop }}</td>
                                        <td>{{ $dispatch->month }}</td>
                                        <td>{{ $dispatch->year }}</td>
                                        <td>{{ $dispatch->prefix }}</td>
                                        <td>{{ $dispatch->sample_id }}</td>
                                        <td>{{ $dispatch->seed_weight }}</td>
                                        <td>{{ $dispatch->no_packets }}</td>
                                        <td>{{ $dispatch->remarks }}</td>
                                        <td>{{ $dispatch->concerned_person }}</td>
                                        <td>{{ $dispatch->location }}</td>
                                        <td>{{ \Carbon\Carbon::parse($dispatch->request_date)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($dispatch->dispatch_date)->format('d-m-Y') }}</td>
                                        <td>{{ $dispatch->tracking_id }}</td>
                                        <td>{{ $dispatch->courier_service }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="15" class="text-center">No dispatched orders found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    
                </div>
               <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <div class="text-muted" style="font-size:13px">
                        @if($dispatches->total() > 0)
                            Showing {{ $dispatches->firstItem() }} to {{ $dispatches->lastItem() }}
                            of {{ $dispatches->total() }} results
                        @else
                            No results found
                        @endif
                    </div>
                    <div>
                        {{ $dispatches->links() }}
                    </div>
                </div>
            </div>
           
        </div>

    </div>
</div>
{{-- Import Dispatch Data Modal --}}
<div class="modal fade" id="importdispModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-light text-dark">
                <h5 class="modal-title"><i class="ri-upload-line me-2"></i>Import Dispatch Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dispatch-list.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show py-2">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show py-2">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('import_skipped'))
                        <div class="alert alert-warning alert-dismissible fade show py-2">
                            <strong><i class="ri-alert-line me-1"></i>Skipped rows:</strong>
                            {{ session('import_skipped') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                        <div class="form-text">Accepted: CSV, XLSX, XLS &mdash; Max 10 MB.</div>
                    </div>
      
                    <div>
                        <a href="{{ route('dispatch-list.template') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ri-download-line me-1"></i>Download Sample Template
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ri-upload-line me-1"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const ctx = document.getElementById('seedWeightChart');

    new Chart(document.getElementById('seedWeightChart'), {
    type: 'bar',
    data: {
        labels: @json($years),
        datasets: [
            {
                label: 'Weight (kg)',
                data: @json($weights),
                borderWidth: 1
            },
            {
                label: 'Number of Seeds',
                data: @json($seeds),
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

});
</script>
@endsection



