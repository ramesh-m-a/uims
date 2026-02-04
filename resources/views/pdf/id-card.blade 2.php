<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        @page { size: A4; margin: 0; }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
        }

        .page {
            width: 210mm;
            height: 297mm;
            position: relative;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .card-wrap {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .card {
            width: 85.6mm;
            height: 54mm;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 4mm;
            box-sizing: border-box;
            background: #fff;
        }

        /* Layout grid */
        .row {
            width: 100%;
            clear: both;
        }

        .left { float: left; }
        .right { float: right; }

        .logo {
            width: 14mm;
        }

        .qr {
            width: 18mm;
        }

        .photo {
            width: 22mm;
            height: 22mm;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #ccc;
        }

        .text {
            font-size: 7pt;
            line-height: 1.2;
        }

        .name {
            font-size: 9pt;
            font-weight: bold;
        }

        .small { font-size: 7pt; }

        .block { margin-top: 2mm; }

        .signature {
            margin-top: 4mm;
            border-top: 1px solid #000;
            width: 30mm;
            font-size: 6pt;
        }
    </style>
</head>
<body>

{{-- ================= FRONT ================= --}}
<div class="page">
    <div class="card-wrap">
        <div class="card">

            <!-- Line 1: Logo + University -->
            <div class="row">
                <div class="left" style="width:18mm;">
                    <img src="file://{{ $card->logoPath }}" class="logo">
                </div>
                <div class="right text" style="width:58mm; text-align:left;">
                    <strong>{{ strtoupper($card->university) }}</strong><br>
                    {{ strtoupper($card->college) }}<br>
                    {{ strtoupper($card->stream) }}
                </div>
            </div>

            <div style="clear:both; height:3mm;"></div>

            <!-- Line 2 + 3 + 4 + 5 -->
            <div class="row">

                <!-- Photo left -->
                <div class="left" style="width:26mm;">
                    <img src="file://{{ $card->profilePath }}" class="photo">
                </div>

                <!-- Details middle -->
                <div class="left" style="width:38mm;" class="text">
                    <div class="name">{{ strtoupper($card->name) }}</div>
                    <div class="small">{{ $card->designation }}</div>
                    <div class="small">{{ $card->department }}</div>
                    <div class="small"><strong>TIN:</strong> {{ $card->tin }}</div>
                </div>

                <!-- QR right -->
                <div class="right" style="width:18mm; text-align:right;">
                    <img src="file://{{ $card->qrPath }}" class="qr">
                </div>

            </div>

        </div>
    </div>
</div>

{{-- ================= BACK ================= --}}
<div class="page">
    <div class="card-wrap">
        <div class="card text">

            <div class="block"><strong>Serial:</strong> {{ $card->serial }}</div>
            <div class="block"><strong>Issued:</strong> {{ $card->issueDate }}</div>
            <div class="block"><strong>Valid Until:</strong> {{ $card->expiryDate }}</div>

            <div class="block" style="margin-top:3mm;">
                This card is property of RGUHS. If found, return to the University.
                Misuse or tampering is punishable under law.
            </div>

            <div class="signature">Registrar</div>

        </div>
    </div>
</div>

</body>
</html>
