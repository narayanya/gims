<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MRN - {{ $dispatch->mrn_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; background: #fff; }
        .no-print { margin: 16px; }
        .no-print a  { text-decoration: none; color: #0d6efd; margin-right: 12px; }
        .no-print button { padding: 6px 18px; background: #0d6efd; color: #fff; border: none; border-radius: 4px; cursor: pointer; }

        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20mm 18mm; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #222; padding-bottom: 10px; margin-bottom: 14px; }
        .header .org { font-size: 18px; font-weight: bold; }
        .header .org small { display: block; font-size: 12px; font-weight: normal; color: #555; }
        .header .mrn-box { text-align: right; }
        .header .mrn-box .mrn-no { font-size: 16px; font-weight: bold; color: #0d6efd; }
        .header .mrn-box small { display: block; font-size: 11px; color: #555; }

        /* Title */
        .doc-title { text-align: center; font-size: 15px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 16px; border: 1px solid #222; padding: 6px; background: #f5f5f5; }

        /* Section */
        .section { margin-bottom: 14px; }
        .section-title { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; background: #e8e8e8; padding: 4px 8px; border-left: 3px solid #0d6efd; margin-bottom: 6px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 20px; padding: 0 8px; }
        .grid-3 { grid-template-columns: 1fr 1fr 1fr; }
        .field { padding: 3px 0; border-bottom: 1px dotted #ccc; }
        .field label { font-size: 11px; color: #666; display: block; }
        .field span { font-weight: bold; font-size: 13px; }

        /* Dispatch highlight */
        .dispatch-box { border: 2px solid #0d6efd; border-radius: 4px; padding: 10px 14px; margin-bottom: 14px; background: #f0f6ff; }
        .dispatch-box .row { display: flex; gap: 20px; flex-wrap: wrap; }
        .dispatch-box .item { flex: 1; min-width: 120px; }
        .dispatch-box .item label { font-size: 11px; color: #555; display: block; }
        .dispatch-box .item span { font-weight: bold; font-size: 14px; }

        /* Signatures */
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 30px; }
        .sig-box { border-top: 1px solid #222; padding-top: 6px; text-align: center; font-size: 11px; color: #555; }

        /* Footer */
        .footer { margin-top: 20px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 10px; color: #888; display: flex; justify-content: space-between; }

        @media print {
            .no-print { display: none; }
            .page { padding: 12mm 14mm; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <a href="{{ route('dispatch-management.index') }}">← Back to List</a>
    <button onclick="window.print()">🖨 Print / Save PDF</button>
</div>

<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="org">
            GIMS — Germplasm Information Management System
            <small>Germplasm Repository &amp; Seed Bank</small>
        </div>
        <div class="mrn-box">
            <div class="mrn-no">{{ $dispatch->mrn_number }}</div>
            <small>Material Release Note</small>
            <small>Dispatch No: {{ $dispatch->dispatch_number }}</small>
        </div>
    </div>

    {{-- Document Title --}}
    <div class="doc-title">Material Release Note (MRN) — Dispatch Slip</div>

    {{-- Dispatch Summary --}}
    <div class="dispatch-box">
        <div class="row">
            <div class="item">
                <label>Dispatch Date</label>
                <span>{{ $dispatch->dispatched_at ? \Carbon\Carbon::parse($dispatch->dispatched_at)->format('d M Y') : now()->format('d M Y') }}</span>
            </div>
            <div class="item">
                <label>Request No.</label>
                <span>{{ $dispatch->request->request_number }}</span>
            </div>
            <div class="item">
                <label>Quantity Dispatched</label>
                <span>{{ $dispatch->quantity }} {{ $dispatch->request->unit?->name ?? '' }}</span>
            </div>
            <div class="item">
                <label>Status</label>
                <span>Dispatched</span>
            </div>
        </div>
    </div>

    {{-- Requester Info --}}
    <div class="section">
        <div class="section-title">Requester Information</div>
        <div class="grid">
            <div class="field"><label>Name</label><span>{{ $dispatch->request->requester_name ?? $dispatch->request->user?->name ?? 'N/A' }}</span></div>
            <div class="field"><label>Email</label><span>{{ $dispatch->request->requester_email ?? $dispatch->request->user?->email ?? 'N/A' }}</span></div>
            <div class="field"><label>Purpose</label><span>{{ $dispatch->request->purpose ?? 'N/A' }}</span></div>
        </div>
    </div>

    {{-- Seed / Accession Info --}}
    <div class="section">
        <div class="section-title">Seed / Accession Information</div>
        <div class="grid grid-3">
            <div class="field"><label>Crop</label><span>{{ $dispatch->request->crop?->crop_name ?? 'N/A' }}</span></div>
            <div class="field"><label>Accession No.</label><span>{{ $dispatch->accession?->accession_number ?? 'N/A' }}</span></div>
            <div class="field"><label>Requested Qty</label><span>{{ $dispatch->request->quantity }} {{ $dispatch->request->unit?->name ?? '' }}</span></div>
            <div class="field"><label>Dispatched Qty</label><span>{{ $dispatch->quantity }} {{ $dispatch->request->unit?->name ?? '' }}</span></div>
        </div>
    </div>

    {{-- Courier / Logistics Info --}}
    <div class="section">
        <div class="section-title">Courier &amp; Logistics</div>
        <div class="grid grid-3">
            <div class="field"><label>Courier Name</label><span>{{ $dispatch->courier_name ?? 'N/A' }}</span></div>
            <div class="field"><label>Contact Person</label><span>{{ $dispatch->contact_person ?? 'N/A' }}</span></div>
            <div class="field"><label>Contact Number</label><span>{{ $dispatch->contact_number ?? 'N/A' }}</span></div>
            <div class="field"><label>Tracking Number</label><span>{{ $dispatch->tracking_number ?? 'N/A' }}</span></div>
        </div>
    </div>

    {{-- Approval Info --}}
    <div class="section">
        <div class="section-title">Approval Information</div>
        <div class="grid">
            <div class="field"><label>Approved By</label><span>{{ $dispatch->request->approvedBy?->name ?? 'N/A' }}</span></div>
            <div class="field"><label>Approval Date</label><span>{{ $dispatch->request->approved_at ? \Carbon\Carbon::parse($dispatch->request->approved_at)->format('d M Y') : 'N/A' }}</span></div>
            <div class="field"><label>Approval Remarks</label><span>{{ $dispatch->request->remarks ?? 'N/A' }}</span></div>
        </div>
    </div>

    {{-- Dispatch Remarks --}}
    @if($dispatch->remarks)
    <div class="section">
        <div class="section-title">Dispatch Remarks</div>
        <div style="padding: 6px 8px; background:#fffbe6; border-left:3px solid #ffc107; font-size:12px;">
            {{ $dispatch->remarks }}
        </div>
    </div>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="sig-box">Prepared By<br><br><br>Name &amp; Designation</div>
        <div class="sig-box">Authorized By<br><br><br>Name &amp; Designation</div>
        <div class="sig-box">Received By<br><br><br>Name &amp; Designation</div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <span>Generated: {{ now()->format('d M Y, H:i') }}</span>
        <span>MRN: {{ $dispatch->mrn_number }} | Dispatch: {{ $dispatch->dispatch_number }}</span>
    </div>

</div>
</body>
</html>
