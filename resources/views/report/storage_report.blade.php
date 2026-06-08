@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <div>
                    <h3 class="text-xl font-bold mb-1">
                        Storage Report
                    </h3>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        Storage location details and inventory information
                    </p>
                </div>

                {{-- Filter --}}
                <form class="d-flex gap-2 align-items-center" method="GET" id="txnFilterForm">
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="{{ request('date_from') }}" style="width:140px">

                    <input type="date" name="date_to" class="form-control form-control-sm"
                        value="{{ request('date_to') }}" style="width:140px">

                    <button type="submit" class="btn btn-sm btn-primary">
                        Filter
                    </button>

                    @if (request('date_from') || request('date_to'))
                        <a href="{{ route('storage.report') }}" class="btn btn-sm btn-secondary">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Warehouse</th>
                                    <th>Storage Name</th>
                                    <th>Storage Time</th>
                                    <th>Rack</th>
                                    <th>Bin</th>
                                    <th>Container</th>
                                    <th style="text-align: right;">Total Quantity</th>

                                </tr>
                            </thead>
                            @php $sr = 1;  $grandTotal = 0; @endphp

                            @foreach ($storages as $storage)
                                @foreach ($storage->lots as $lot)
                                    @php
                                        $quantity = $lot->seedQuantities->sum('quantity');
                                        $grandTotal += $quantity;
                                    @endphp
                                    <tr>
                                        <td>{{ $sr++ }}</td>
                                        <td>{{ $storage->warehouse->name ?? '-' }}</td>
                                        <td>{{ $storage->name }}</td>
                                        <td>{{ $storage->storageTime->code ?? '-' }}</td>
                                        <td>{{ $lot->rack->name ?? '-' }}</td>

                                        <td>{{ $lot->bin->name ?? '-' }}</td>

                                        <td>{{ $lot->container->name ?? '-' }}</td>

                                        <td style="text-align: right;">{{ $quantity }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            <!-- Grand Total Row -->
                                <tr class="table-warning fw-bold">
                                    <td colspan="7" class="text-end">
                                        Grand Total Quantity
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($grandTotal, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}

                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                        {{-- <div>
                Showing {{ $dispatches->firstItem() }} to {{ $dispatches->lastItem() }}
                of {{ $dispatches->total() }} results
            </div>
            <div>
                {{ $dispatches->links() }}
            </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
