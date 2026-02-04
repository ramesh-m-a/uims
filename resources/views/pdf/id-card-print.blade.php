<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 10mm; }
        body { font-family: DejaVu Sans; }

        .page { page-break-after: always; }
        .page:last-child { page-break-after: none; }

        .card {
            width: 85.6mm;
            height: 54mm;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 6mm;
            box-sizing: border-box;
            text-align: center;
        }

        .photo { width: 22mm; height: 22mm; border-radius: 50%; }
        .qr { width: 18mm; }

        .title { font-size: 9pt; font-weight: bold; }
        .meta { font-size: 8pt; }
    </style>
</head>
<body>

{{-- FRONT --}}
<div class="page">
    <div class="card">
        <img src="{{ $card->collegeLogo }}" width="40">

        <div class="title">{{ strtoupper($card->university) }}</div>
        <div class="meta">{{ strtoupper($card->college) }}</div>

        <img src="{{ $card->profileImage }}" class="photo">

        <div class="title">{{ strtoupper($card->name) }}</div>
        <div class="meta">{{ $card->designation }}</div>
        <div class="meta">{{ $card->department }}</div>
        <div class="meta" style="font-weight:bold;">RGUHS TIN {{ $card->tin }}</div>

        <img src="{{ $card->qrPath() }}" class="qr">
    </div>
</div>

{{-- BACK --}}
<div class="page">
    <div class="card" style="font-size:8pt;text-align:left;">
        <strong>Serial:</strong> {{ $card->serial }}<br>
        <strong>Issued:</strong> {{ $card->issueDate }}<br>
        <strong>Valid Until:</strong> {{ $card->expiryDate }}<br><br>

        This card is property of RGUHS.
        Misuse or duplication is punishable.

        <br><br><br>
        __________________________
        Registrar Signature
    </div>
</div>

</body>
</html>
