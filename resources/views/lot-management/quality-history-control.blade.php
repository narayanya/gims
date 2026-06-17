@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Page Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
            <div>
                <h3 class="text-xl font-bold">Lot Quality History Control</h3>
                <p class="text-muted mb-0" style="font-size:13px">Select a lot to view and manage its seed quality information</p>
            </div>
            <div class="">
                <a href="{{ route('quality-control.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Back to Lot List
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        <div id="alertBox" class="d-none"></div>

        {{-- ── STEP 1: Lot Selection ── --}}
        <div class="card mb-3">
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Lot No.</th>
                                <th style="min-width:100px">Germination %</th>
                                <th style="min-width:100px">Moisture %</th>
                                <th style="min-width:100px">Purity %</th>
                                <th style="min-width:110px">Chlorophyll %</th>
                                <th style="min-width:100px">Water Level %</th>
                                <th style="min-width:130px">Viability Date</th>
                                <th style="min-width:140px">Health Status</th>
                                <th style="min-width:160px">Researcher</th>
                                <th style="min-width:130px">Research Date</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody id="qualityTbody">
                            @forelse($qualityHistories as $quality)
                         

                            <tr>
                                <td class="fw-bold text-success">{{ $quality->lot->lot_number ?? '—' }}</td>
                                <td>{{ $quality->germination_percentage ?? '—' }}</td>

                                <td>{{ $quality->moisture_content ?? '—' }}</td>

                                <td>{{ $quality->purity_percentage ?? '—' }}</td>

                                <td>{{ $quality->chlorophyll_percentage ?? '—' }}</td>

                                <td>{{ $quality->water_level_percentage ?? '—' }}</td>

                                <td>
                                    {{ $quality->viability_test_date
                                        ? \Carbon\Carbon::parse($quality->viability_test_date)->format('d-m-Y')
                                        : '—'
                                    }}
                                </td>

                                <td>
                                    <span class="badge bg-success">
                                        {{ $quality->health_status ?? 'Healthy' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $quality->researcher->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $quality->research_date
                                        ? \Carbon\Carbon::parse($quality->research_date)->format('d-m-Y')
                                        : '—'
                                    }}
                                </td>

                                <td>
                                    {{ $quality->created_at->format('d-m-Y h:i A') }}
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    No quality history found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <div>
                        Showing {{ $qualityHistories->firstItem() }} to {{ $qualityHistories->lastItem() }}
                        of {{ $qualityHistories->total() }} results
                    </div>

                    <div>
                        {{ $qualityHistories->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
