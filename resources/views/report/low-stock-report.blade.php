@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-sage-muted/20 pb-2">
            <div class="items-center gap-3">
                <h3 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100">
                    Low Stock Report
                </h3>

                <p class="text-sage-600 dark:text-sage-400 text-sm mb-1" style="color: #777777">
                    View low stock data with filters
                </p>
            </div>

            <div class="d-flex gap-2">

                <!-- Download -->
                <a href=""
                   class="btn btn-sm btn-primary">
                    <i class="ri-download-line me-1"></i>
                    Download Report
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">

                <form method="GET" class="row g-2 align-items-end">

                    <!-- From Date -->
                    <div class="col-md-2">
                        <label class="form-label text-muted small">
                            From Date
                        </label>

                        <input type="date"
                               name="date_from"
                               class="form-control form-control-sm"
                               value="{{ request('date_from') }}">
                    </div>

                    <!-- To Date -->
                    <div class="col-md-2">
                        <label class="form-label text-muted small">
                            To Date
                        </label>

                        <input type="date"
                               name="date_to"
                               class="form-control form-control-sm"
                               value="{{ request('date_to') }}">
                    </div>
                

                    <!-- Filter -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary ">
                            <i class="ri-filter-line me-1"></i>
                            Filter
                        </button>
                    </div>

                    <!-- Reset -->
                    <div class="col-md-2">
                        
                    </div>

                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive">

                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-3 py-2">Accession No.</th>
                                            <th class="px-3 py-2">Crop</th>
                                            <th class="px-3 py-2">Accession Name</th>
                                            <th class="px-3 py-2 text-end">Available Qty</th>
                                            <th class="px-3 py-2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        @foreach($lowStockAccessions as $ls)
                                        <tr class="hover:bg-rose-50 transition-colors">
                                            <td class="px-3 py-2 font-mono font-semibold text-slate-800">{{ $ls->accession_number }}</td>
                                            <td class="px-3 py-2 text-slate-600">{{ $ls->crop->crop_name ?? '—' }}</td>
                                            <td class="px-3 py-2 text-slate-600">{{ $ls->accession_name ?? '—' }}</td>
                                            <td class="px-3 py-2 text-end">
                                                <span class="font-bold {{ ($ls->total_available ?? 0) <= 5 ? 'text-red-600' : 'text-amber-600' }}">
                                                    {{ number_format($ls->total_available ?? 0, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <a href="{{ route('accessions.show', $ls->id) }}" class="text-primary text-xs">
                                                    <i class="ri-eye-line me-1"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                <!-- Pagination -->
                <div class="mt-3">
                    {{-- $accessions->withQueryString()->links() --}}
                </div>

            </div>
        </div>

    </div>
</div>
@endsection