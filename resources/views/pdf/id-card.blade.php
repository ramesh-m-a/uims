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

        .card-row {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 190mm;
        }

        .card-wrap-left {
            position: absolute;
            top: 50%;
            left: 10mm;
            transform: translateY(-50%);
        }

        .card-wrap-right {
            position: absolute;
            top: 50%;
            right: 10mm;
            transform: translateY(-50%);
        }

        .card {
            width: 85.6mm;
            height: 54mm;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 4mm;
            box-sizing: border-box;
            background: #fff;
            overflow: hidden;
        }


    </style>
</head>
<body>

<div class="page">

    <!-- FRONT -->
    <div class="card-wrap-left">
        <div class="card">

            <div class="row">
                <div class="left" style="width:18mm;">
                    <img src="{{ $card->logoBase64 }}" class="logo">
                </div>

                <div class="right text" style="width:58mm;text-align:left;">
                    <strong>{{ strtoupper($card->university) }}</strong><br>
                    {{ strtoupper($card->college) }}<br>
                    {{ strtoupper($card->stream) }}
                </div>
            </div>

            <div style="clear:both;height:3mm;"></div>

            <div class="row">

                <div class="left" style="width:26mm;">
                    <img src="{{ $card->photoBase64 }}" class="photo">
                </div>

                <div class="left text" style="width:36mm;margin-left:2mm;">
                    <div class="name">{{ strtoupper($card->name) }}</div>
                    <div>{{ $card->designation }}</div>
                    <div>{{ $card->department }}</div>
                    <div><strong>TIN:</strong> {{ $card->tin }}</div>
                </div>

                <div class="right" style="width:18mm;text-align:right;">
                    <img src="{{ $card->qrBase64 }}" class="qr">
                </div>

            </div>

        </div>
    </div>


    <!-- BACK -->
    <div class="card-wrap-right">
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
