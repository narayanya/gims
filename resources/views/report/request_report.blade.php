@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Request Report</h4>
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
@endsection