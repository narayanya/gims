@extends('layouts.app')

@section('content')
 <div class="row justify-content-center">
        <div class="col-12 ">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
                <div class="items-center gap-3">
                    <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                       Request Report
                    </h3>
                    <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777"> </p>
                </div>
                <form class="d-flex gap-2 align-items-center" method="GET" id="txnFilterForm">
                        <input type="date" name="date_from" class="form-control form-control-sm" placeholder="From" value="{{ request('date_from') }}" style="width:140px">
                        <input type="date" name="date_to"   class="form-control form-control-sm" placeholder="To"   value="{{ request('date_to') }}"   style="width:140px">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
            </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Request No</th>
                <th>User</th>
                <th>Crop</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $r)
                <tr>
                    <td>{{ $r->request_number }}</td>
                    <td>{{ $r->user->name ?? '' }}</td>
                    <td>{{ $r->crop->crop_name ?? '' }}</td>
                    <td>{{ $r->quantity }} {{ $r->unit->name ?? '' }}</td>
                    <td>{{ ucfirst($r->status) }}</td>
                    <td>{{ $r->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
 </div>
@endsection