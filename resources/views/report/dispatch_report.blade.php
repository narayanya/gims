@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <div>
                <h3 class="text-xl font-bold mb-1">
                    Dispatch Report
                </h3>
                <p class="text-muted mb-0" style="font-size:13px;">
                    Dispatch transaction history and shipment details
                </p>
            </div>

            {{-- Filter --}}
            <form class="d-flex gap-2 align-items-center" method="GET" id="txnFilterForm">
                <input type="date"
                       name="date_from"
                       class="form-control form-control-sm"
                       value="{{ request('date_from') }}"
                       style="width:140px">

                <input type="date"
                       name="date_to"
                       class="form-control form-control-sm"
                       value="{{ request('date_to') }}"
                       style="width:140px">

                <button type="submit" class="btn btn-sm btn-primary">
                    Filter
                </button>

                @if(request('date_from') || request('date_to'))
                    <a href="{{ route('dispatch.report') }}" class="btn btn-sm btn-secondary">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Dispatch No</th>
                        <th>Request No</th>
                        <th>MRN No</th>
                        <th>Accession</th>
                        <th>Lot No</th>
                        <th>Quantity</th>
                        <th>Courier</th>
                        <th>Tracking No</th>
                        <th>Dispatch Date</th>
                        <th>Created</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($dispatches as $dispatch)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <span class="fw-semibold text-primary">
                                    {{ $dispatch->dispatch_number }}
                                </span>
                            </td>

                            <td>
                                {{ $dispatch->request->request_number ?? '-' }}
                            </td>

                            <td>
                                {{ $dispatch->mrn_number ?? '-' }}
                            </td>

                            <td>
                                {{ $dispatch->accession->accession_number ?? '-' }}
                            </td>

                            <td>
                                {{ $dispatch->lot->lot_number ?? '-' }}
                            </td>

                            <td>
                                {{ $dispatch->quantity ?? 0 }}
                            </td>

                            <td>
                                {{ $dispatch->courier_name ?? '-' }}
                            </td>

                            <td>
                                {{ $dispatch->tracking_number ?? '-' }}
                            </td>

                            <td>
                                {{ $dispatch->dispatched_at ? \Carbon\Carbon::parse($dispatch->dispatched_at)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $dispatch->created_at->format('d M Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                No dispatch records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}

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
@endsection