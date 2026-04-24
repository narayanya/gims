@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h3 class="text-xl font-bold">Dispatch Management</h3>
                <p class="text-muted mb-0" style="font-size:13px">Create and manage dispatch requests</p>
            </div>
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
                    <h5>Request</h5>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Request Number</th>
                                <th>Accession Number</th>
                                <th>Lot Number</th>
                                <th>Requester Name</th>
                                <th>Crop</th>
                                <th>Quantity</th>
                                <th>Required Date</th>
                                <th>Receiver Name</th>
                                <th>Destination</th>
                                <th>Status</th>
                                <th>Dispatch Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($requests->count() > 0)
                            @foreach($requests as $req)
                            <tr>
                                <td>{{ $req->request_number }}</td>
                                <td>{{ $req->accession?->accession_number ?? '—' }}</td>
                                <td>
                                    @php
                                        $lotNums = \App\Models\Lot::where('accession_id', $req->accession_id)
                                            ->whereNotNull('lot_number')
                                            ->pluck('lot_number');
                                    @endphp
                                    {{ $lotNums->isNotEmpty() ? $lotNums->implode(', ') : '—' }}
                                </td>
                                
                                <td>{{ $req->requester_name }}</td>
                                <td>{{ $req->crop->crop_name ?? '' }}</td>
                                <td>{{ $req->quantity }}</td>
                                <td>{{ $req->required_date->format('d-m-Y h:i A') }}</td>
                                <td>{{ $req->receiver_name }}</td>
                                <td>{{ $req->destination }}</td>
                                <td>
                                    <form action="" method="POST">
                                        @csrf
                                        <a href="{{ route('dispatch.show', $req->id) }}" class="btn btn-sm btn-success">
                                            Dispatch
                                        </a>
                                    </form>
                                </td>
                                <td>{{ $req->updated_at->format('d-m-Y') }}</td>
                            </tr>
                            @endforeach 
                            @else
                            <tr>
                                <td colspan="11" class="text-center">No more requests</td> 
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h5>ITN</h5>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ITN Number</th>
                                <th>Accession Number</th>
                                <th>Lot Number</th>
                                <th>Receiver Name</th>
                                <th>Crop</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>From Warehouse</th>
                                <th>To Warehouse</th>
                                <th>Status</th>
                                <th>Itn Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($itns->count() > 0)
                            @foreach($itns as $itn)
                            <tr>
                                <td>{{ $itn->itn_number }}</td>

                                <td>{{ $itn->accession->accession_number ?? '—' }}</td>

                                <td>{{ $itn->lot->lot_number ?? '—' }}</td>

                                <td>{{ $itn->receiver ?? '—' }}</td>

                                <td>{{ $itn->crop->crop_name ?? '—' }}</td>

                                <td>{{ $itn->quantity }}</td>

                                <td>{{ \Carbon\Carbon::parse($itn->created_at)->format('d-m-Y h:i A') }}</td>

                                <td>{{ $itn->fromWarehouse->name ?? '—' }}</td>

                                <td>{{ $itn->toWarehouse->name ?? '—' }}</td>

                                <td>
                                    <span class="badge bg-success">Generated</span>
                                </td>

                                <td>{{ \Carbon\Carbon::parse($itn->itn_date)->format('d-m-Y') }}</td>

                                <td>
                                    <a href="{{ route('warehouse-transfer.itn.print', $itn->id) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-primary">
                                        Print
                                    </a>
                                    <form action="" method="POST">
                                        @csrf
                                        <a href="{{ route('dispatch.itn.show', $itn->id) }}"
                                            class="btn btn-sm btn-success">
                                                Dispatch
                                            </a>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="12" class="text-center">No ITN Found</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h5 class="mb-3">Dispatched Orders</h5>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Dispatch Number</th>
                                <th>Request Number</th>
                                <th>Accession Number</th>
                                <th>Lot Number</th>
                                <th>MRN Number</th>
                                <th>Quantity</th>
                                <th>Courier name</th>
                                <th>Tracking number</th>
                                <th>Action</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($dispatches->count() > 0)
                                @foreach($dispatches as $dispatch)
                                <tr>
                                    <td>{{ $dispatch->dispatch_number }}</td>
                                    <td>{{ $dispatch->request?->request_number }}</td>
                                    <td>{{ $dispatch->accession?->accession_number ?? 'N/A' }}</td>
                                    <td>
                                        @if($dispatch->lot_id)
                                            @php $dLot = \App\Models\Lot::find($dispatch->lot_id); @endphp
                                            <span class="badge bg-primary">{{ $dLot?->lot_number ?? '—' }}</span>
                                        @else
                                            @php
                                                $dLotNums = \App\Models\Lot::where('accession_id', $dispatch->accession_id)
                                                    ->whereNotNull('lot_number')->pluck('lot_number');
                                            @endphp
                                            {{ $dLotNums->isNotEmpty() ? $dLotNums->implode(', ') : '—' }}
                                        @endif
                                    </td>
                                    <td>{{ $dispatch->mrn_number }}</td>
                                    <td>{{ $dispatch->quantity }}</td>
                                    <td>{{ $dispatch->courier_name }}</td>
                                    <td>{{ $dispatch->tracking_number }}</td>
                                    <td>
                                        <a href="{{ route('dispatch.print', $dispatch->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                            Print MRN
                                        </a>
                                    </td>
                                    <td>{{ $dispatch->created_at->format('d-m-Y h:i A') }}</td>
                                </tr>
                                @endforeach   
                            @else
                                <tr>
                                <td colspan="11" class="text-center">No more dispatched orders</td> 
                            </tr>
                            @endif
                             
                        </tbody>
                    </table>
                </div>
               
            </div>
           
        </div>

    </div>
</div>
@endsection



