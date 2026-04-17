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
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Accession Number</th>
                                <th>Request Number</th>
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
                                <td>{{ $req->accession->accession_number ?? '' }} </td>
                                <td>{{ $req->request_number }}</td>
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
                    <h5 class="mb-3">Dispatched Orders</h5>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Dispatch Number</th>
                                <th>Request Number</th>
                                <th>Accession Number</th>
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
                                    <td>{{ $dispatch->accession->accession_number ?? 'N/A' }}</td>
                                    <td>{{ $dispatch->request->request_number }}</td>
                                    <td>{{ $dispatch->accession->accession_number ?? 'N/A' }}</td>
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



