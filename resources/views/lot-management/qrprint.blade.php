<!DOCTYPE html>
<html>
<head>
    <title>Lot QR Labels</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        .label{
            width:225px;
            border:1px solid #000;
            padding:10px;
            margin:10px;
            float:left;
            font-family: roboto, sans-serif;
            position: relative;
        }
        p{
            font-size: 12px;
            margin-top: 8px;
            margin-bottom: 0;
        }

        @media print{
            .no-print{
                display:none;
            }
        }
    </style>
</head>
<body>
<div class="no-print" style="text-align: left;margin-right: 28px;margin-top: 5px;">
    <h4 style="display: inline-block;text-align:left;float:left;margin:7px 20px;">GIMS - QR Labels</h4>
    <button style="background-color: #007bff; color: white; border: none; padding: 10px 20px; cursor: pointer;border-radius: 4px;" onclick="window.print()">Print</button>
</div>


<div class="label">
    <div id="qr{{ $lot->id }}"></div>
    <span style="position: absolute; top: 10px;
    right: 10px;
    font-size: 12px;
    font-weight: 600;">Qty. {{ optional($lot->seedQuantities->first())->quantity 
                                        ? number_format($lot->seedQuantities->first()->quantity, 2) 
                                        : '—' }}
                                         {{ optional($lot->seedQuantities->first()?->unit)->name ?? '—' }}</span>
    <h5 style="margin-top:10px;">{{ $lot->lot_number }}</h5>

    <p>
        Acce. No: {{ $lot->accession?->accession_number }}
        <br>
        Crop: {{ $lot->accession?->crop?->crop_name }}
        <br>
        Sample ID: {{ $lot->accession?->sample_id }}, 
        <br>
        Reference No: {{ $lot->reference_number }}
    </p>

    <script>
        new QRCode(document.getElementById("qr{{ $lot->id }}"), {
            text: "{{ url('/lots/public/'.$lot->id) }}",
            width: 100,
            height: 100
        });
    </script>
</div>

</body>
</html>