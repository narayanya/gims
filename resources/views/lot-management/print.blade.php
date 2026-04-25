<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ITN - {{ $itn->itn_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; background: #fff; }
        .no-print { margin: 16px; }
        .no-print a  { text-decoration: none; color: #0d6efd; margin-right: 12px; }
        .no-print button { padding: 6px 18px; background: #0d6efd; color: #fff; border: none; border-radius: 4px; cursor: pointer; }

        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20mm 18mm; }

        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #222; padding-bottom: 10px; margin-bottom: 14px; }
        .org { font-size: 18px; font-weight: bold; }
        .org small { display:block; font-size:12px; color:#555; }
        .itn-box { text-align: right; }
        .itn-no { font-size: 16px; font-weight: bold; color:#198754; }

        .doc-title { text-align:center; font-weight:bold; margin-bottom:15px; padding:6px; border:1px solid #222; background:#f5f5f5; }

        .section { margin-bottom: 14px; }
        .section-title { font-size: 12px; font-weight: bold; background:#e8e8e8; padding:4px 8px; border-left:3px solid #198754; margin-bottom:6px; }

        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:6px; }

        .signatures { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-top:30px; }
        .sig-box { border-top:1px solid #000; text-align:center; padding-top:6px; font-size:11px; }

        @media print {
            .no-print { display:none; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <a href="{{ route('warehouse-transfer.index') }}">← Back</a>
    <button onclick="window.print()">🖨 Print / Save PDF</button>
</div>

<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="org">
            GIMS — Germplasm Information Management System
            <small>Warehouse Transfer System</small>
        </div>
        <div class="itn-box">
            <div class="itn-no">{{ $itn->itn_number }}</div>
            <small>Internal Transfer Note</small>
        </div>
    </div>

    {{-- Title --}}
    <div class="doc-title">Internal Transfer Note (ITN)</div>

    {{-- Summary --}}
    <div class="section">
        <div class="section-title">Transfer Summary</div>
        <table>
            <tr>
                <th>ITN Date</th>
                <td>{{ \Carbon\Carbon::parse($itn->itn_date)->format('d M Y') }}</td>
                <th>Quantity</th>
                <td>{{ $itn->quantity }}</td>
            </tr>
        </table>
    </div>

    {{-- Warehouse Info --}}
    <div class="section">
        <div class="section-title">Warehouse Details</div>
        <table>
            <tr>
                <th>From Warehouse</th>
                <td>{{ $itn->fromWarehouse->name ?? '-' }}</td>
                <th>To Warehouse</th>
                <td>{{ $itn->toWarehouse->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>From Storage</th>
                <td>{{ $itn->fromStorage->name ?? '-' }}</td>
                <th>To Storage</th>
                <td>{{ $itn->toStorage->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Lot Info --}}
    <div class="section">
        <div class="section-title">Material Details</div>
        <table>
            <thead>
                <tr>
                    <th>Crop</th>
                    <th>Accession</th>
                    <th>Lot</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batchLots as $bl)
                <tr>
                    <td>{{ $bl->lot->crop->crop_name ?? '-' }}</td>
                    <td>{{ $bl->lot->accession->accession_number ?? '-' }}</td>
                    <td>{{ $bl->lot->lot_number ?? '-' }}</td>
                    <td>{{ $bl->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Receiver --}}
    <div class="section">
        <div class="section-title">Receiver Details</div>
        <table>
            <tr>
                <th>Name</th>
                <td>{{ $itn->receiver }}</td>
                <th>Mobile</th>
                <td>{{ $itn->mobile_number }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td colspan="3">{{ $itn->email ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Instructions --}}
    @if($itn->instructions)
    <div class="section">
        <div class="section-title">Instructions</div>
        <div style="padding:8px; border:1px solid #ccc;">
            {{ $itn->instructions }}
        </div>
    </div>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="sig-box">Prepared By</div>
        <div class="sig-box">Authorized By</div>
        <div class="sig-box">Received By</div>
    </div>

</div>
</body>
</html>