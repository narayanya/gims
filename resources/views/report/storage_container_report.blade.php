@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">

            {{-- Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2 gap-1">
                <div>
                    <h3 class="text-xl font-bold mb-1">
                        Storage Container Report
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
                        <a href="{{ route('storage.container-report') }}" class="btn btn-sm btn-secondary">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Rack</th>
                                    <th>Bin</th>
                                    <th>Box No.</th>
                                    <th>Reference No</th>
                                    <th>Lot Count (Pouch)</th>
                                    <th>QR Code</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($boxes as $index => $box)
                                    <tr>
                                        <td>{{ $box['rack_name'] }}</td>
                                        <td>{{ $box['bin_name'] }}</td>
                                        <td>{{ $box['box_no'] }}</td>
                                        <td>{{ $box['reference_range'] }}</td>
                                        <td>{{ $box['lot_count'] }}</td>
                                        <td class="text-center">
                                            {{-- QR rendered here by JS --}}
                                            <div id="qr-{{ $index }}" style="display:inline-block;cursor:pointer;"
                                                 title="Click to view details"
                                                 data-box-index="{{ $index }}"></div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#boxModal{{ str_replace('Box-','',$box['box_no']) }}">
                                                View Lots
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            No Data Found
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QR Scan Detail Modal --}}
    <div class="modal fade" id="qrDetailModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-light text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-qr-code-scan me-2"></i>
                        Box Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="qrDetailBody">
                    {{-- filled by JS --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Per-box lot modals --}}
    @foreach($boxes as $box)
        <div class="modal fade"
             id="boxModal{{ str_replace('Box-','',$box['box_no']) }}"
             tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $box['box_no'] }} ({{ $box['reference_range'] }})
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Crop Name</th>
                                    <th>Lot No</th>
                                    <th>Ref. No.</th>
                                    <th>Rack</th>
                                    <th>Bin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($box['lots'] as $lot)
                                    <tr>
                                        <td>{{ $lot->crop->name ?? '-' }}</td>
                                        <td>{{ $lot->lot_number }}</td>
                                        <th>{{ $lot->reference_number}}</th>
                                        <td>{{ $lot->rack->name ?? '-' }}</td>
                                        <td>{{ $lot->bin->name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Box data for JS --}}
    @php
        $boxesJson = collect($boxes)->map(function ($box, $index) {
            return [
                'index'           => $index,
                'box_no'          => $box['box_no'],
                'reference_range' => $box['reference_range'],
                'lot_count'       => $box['lot_count'],
                'rack_name'       => $box['rack_name'],
                'bin_name'        => $box['bin_name'],
                'lots'            => collect($box['lots'])->map(function ($lot) {
                    return [
                        'lot_number' => $lot->lot_number,
                        'crop_name'  => $lot->crop->name ?? '-',
                        'rack'       => $lot->rack->name ?? '-',
                        'bin'        => $lot->bin->name  ?? '-',
                    ];
                })->values(),
            ];
        })->values();
    @endphp

    <script>
        const boxData = {!! json_encode($boxesJson) !!};
    </script>
@endsection

@push('scripts')
    {{-- QRCode.js library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Generate a QR for every box ──────────────────────────────
            boxData.forEach(function (box) {
                const el = document.getElementById('qr-' + box.index);
                if (!el) return;

                // Compact text encoded in the QR
                const qrText = [
                    'Box: '   + box.box_no,
                    'Rack: '  + box.rack_name,
                    'Bin: '   + box.bin_name,
                    'Ref: '   + box.reference_range,
                    'Lots: '  + box.lot_count,
                    'Items: ' + box.lots.map(l => l.lot_number).join(', '),
                ].join(' | ');

                new QRCode(el, {
                    text:         qrText,
                    width:        80,
                    height:       80,
                    colorDark:    '#000000',
                    colorLight:   '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M,
                });

                // Click QR → open detail modal
                el.addEventListener('click', function () {
                    showQrDetail(box);
                });
            });

            // ── Show detail modal ─────────────────────────────────────────
            function showQrDetail(box) {
                const lotsRows = box.lots.map(l => `
                    <tr>
                        <td>${l.crop_name}</td>
                        <td>${l.lot_number}</td>
                        <td>${l.rack}</td>
                        <td>${l.bin}</td>
                    </tr>`).join('');

                const html = `
                    <div class="p-3">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="border rounded p-2 bg-outline-success h-100">
                                    <div class="text-muted small">Box No.</div>
                                    <div class="fw-bold">${box.box_no}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 bg-outline-success h-100">
                                    <div class="text-muted small">Reference Range</div>
                                    <div class="fw-bold">${box.reference_range}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 bg-outline-success h-100">
                                    <div class="text-muted small">Rack</div>
                                    <div class="fw-bold">${box.rack_name}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 bg-outline-success h-100">
                                    <div class="text-muted small">Bin</div>
                                    <div class="fw-bold">${box.bin_name}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 bg-outline-success h-100">
                                    <div class="text-muted small">Lot Count</div>
                                    <div class="fw-bold">${box.lot_count}</div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-semibold mb-2">Lots in this Box</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" style="height:350px;overflow:auto;">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Crop Name</th>
                                        <th>Lot No</th>
                                        <th>Rack</th>
                                        <th>Bin</th>
                                    </tr>
                                </thead>
                                <tbody>${lotsRows}</tbody>
                            </table>
                        </div>
                    </div>`;

                document.getElementById('qrDetailBody').innerHTML = html;

                const modal = new bootstrap.Modal(document.getElementById('qrDetailModal'));
                modal.show();
            }
        });
    </script>
@endpush
