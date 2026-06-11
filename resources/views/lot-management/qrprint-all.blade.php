<!DOCTYPE html>
<html>
<head>
    <title>Lot QR Labels</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        .label{
            width:300px;
            border:1px solid #000;
            padding:10px;
            margin:10px;
            float:left;
            font-family: roboto, sans-serif;
        }
        p{
            font-size: 12px;
            margin-top: 8px;
        }

        @media print{
            .no-print{
                display:none;
            }
        }
    </style>
</head>
<body>

<button class="no-print" onclick="window.print()">Print</button>

@foreach($lots as $lot)

<div class="label">
    <div id="qr{{ $lot->id }}"></div>
    <h5 style="margin-top:10px;">{{ $lot->lot_number }}</h5>

    <p>
        Acce. No: {{ $lot->accession?->accession_number }}
        <br>
        Crop: {{ $lot->accession?->crop?->crop_name }}
        <br>
        Sample ID: {{ $lot->accession?->sample_id }}, 
        Reference No: {{ $lot->reference_number }}
    </p>

    

    <script>
        new QRCode(document.getElementById("qr{{ $lot->id }}"), {
            text: "{{ url('/lot/public/'.$lot->lot_number) }}",
            width: 100,
            height: 100
        });
    </script>
</div>

@endforeach

</body>
</html>