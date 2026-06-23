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

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-3">Dispatched Orders</h5>
                        
                        <div>
                            <form action="{{ route('dispatch-management.index') }}" method="GET" class="d-flex gap-2 mb-3">
                            <input type="date" name="from_date" class="form-control form-control-sm" placeholder="From Date" value="{{ request('from_date') }}" max="{{ date('Y-m-d') }}">
                            <input type="date" name="to_date" class="form-control form-control-sm" placeholder="To Date" value="{{ request('to_date') }}" max="{{ date('Y-m-d') }}">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Dispatch Number, ITN/Request No., MRN, Courier, Tracking" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-primary">Search</button>

                             <a href="" class="btn btn-sm btn-outline-primary mb-2">
                            <i class="ri-download-line me-1"></i>Export
                        </a>
                        </form>
                        
                       
                        
                        </div>
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
                                @foreach($dispatches as $dispatch)
                                <tr>
                                    <td>{{ $dispatch->dispatch_number }}</td>
                                    <td>
                                        @if($dispatch->itn_id)
                                            <span class="badge bg-info text-dark">{{ $dispatch->itn?->itn_number ?? '—' }}</span>
                                        @else
                                            {{ $dispatch->request?->request_number ?? '—' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($dispatch->batch_id)
                                            @php
                                                $dLots = \App\Models\WarehouseTransfer::with('lot')
                                                    ->where('batch_id', $dispatch->batch_id)->get();
                                            @endphp
                                            @foreach($dLots as $dl)
                                                <span class="badge bg-primary">{{ $dl->lot->lot_number ?? '—' }}</span>
                                            @endforeach
                                        @elseif($dispatch->lot_id)
                                            @php $dLot = \App\Models\Lot::find($dispatch->lot_id); @endphp
                                            <span class="badge bg-primary">{{ $dLot?->lot_number ?? '—' }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $dispatch->mrn_number }}</td>
                                    <td>{{ $dispatch->quantity }}</td>
                                    <td>{{ $dispatch->courier_name ?? '—' }}</td>
                                    <td>{{ $dispatch->tracking_number ?? '—' }}</td>
                                    <td>{{ $dispatch->created_at->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('dispatch.print', $dispatch->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                            Print MRN
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center">No dispatched orders found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    
                </div>
               <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <div>
                        Showing {{ $dispatches->firstItem() }} to {{ $dispatches->lastItem() }}
                        of {{ $dispatches->total() }} results
                    </div>

                    <div>
                        {{ $dispatches->links() }}
                    </div>
                </div>
            </div>
           
        </div>

    </div>
</div>
@endsection



