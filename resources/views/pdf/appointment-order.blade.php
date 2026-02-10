<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>

        body{
            font-family:"Times New Roman", serif;
            font-size:13px;
            color:#000;
            line-height:1.55;
        }

        .letter-container{
            width:780px;
            margin:auto;
            padding:25px 30px;
            position:relative;
            z-index:1;
        }

        /* ✅ WATERMARK SAFE LAYER */
        .letter-container::before{
            content:"";
            position:absolute;
            inset:0;
            background:url('{{ public_path("images/RGUHS-logo-AA.png") }}') no-repeat center;
            background-size:320px auto;
            opacity:0.06;
            z-index:0;
        }

        /* HEADER */
        .header{
            display:flex;
            align-items:center;
            border-bottom:2px solid #000;
            padding-bottom:8px;
            margin-bottom:14px;
        }

        .logo img{ height:70px; }

        .header-text{
            flex:1;
            text-align:center;
        }

        /* META TABLE */
        .meta-table{
            width:100%;
            margin-top:10px;
            margin-bottom:14px;
        }

        .meta-table td{
            padding:2px 0;
        }

        .right{ text-align:right; }

        /* BODY */
        .body p{
            margin:10px 0;
            text-align:justify;
        }

        .subject-line{ font-weight:bold; }

        /* INFO TABLE */
        .info-table{
            width:100%;
            margin-top:10px;
        }

        .info-table td{
            padding:4px 0;
        }

        /* FOOTER */
        .footer{
            margin-top:30px;
        }

        .footer-flex{
            display:flex;
            justify-content:space-between;
            align-items:flex-end;
        }

        .qr-box{
            position:relative;
            z-index:5;
        }

        .signature-img{ height:70px; }
        .seal-img{ height:80px; margin-top:5px; }

        .note{
            text-align:center;
            font-size:11px;
            margin-top:20px;
        }

    </style>
</head>

<body>

<div class="letter-container">

    {{-- HEADER --}}
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('images/RGUHS-logo-AA.png') }}">
        </div>

        <div class="header-text">
            <strong>RAJIV GANDHI UNIVERSITY OF HEALTH SCIENCES, KARNATAKA</strong><br>
            4th T Block, Jayanagar, Bengaluru – 560041<br>
            <strong>APPOINTMENT ORDER</strong>
        </div>
    </div>

    {{-- META --}}
    <table class="meta-table">

        <tr>
            <td width="60">To</td>
            <td></td>
            <td width="80" class="right">Date :</td>
            <td width="120" class="right">
                {{ optional($order->generated_at)->format('d-m-Y') }}
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <strong>
                    {{ strtoupper(
                    $order->examiner->name
                    ?? $order->allocation->examiner_name
                    ?? '-'
                    ) }}
                </strong>
            </td>

            <td class="right">Order :</td>
            <td class="right">
                {{ $order->order_number }}
            </td>
        </tr>

        <tr>
            <td>College :</td>
            <td colspan="3">
                {{ strtoupper(
                $order->examiner->college->college_name
                ?? $order->allocation->centre_name
                ?? '-'
                ) }}
            </td>
        </tr>

        <tr>
            <td>Mobile :</td>
            <td>{{ $order->examiner->mobile ?? $order->allocation->mobile ?? '-' }}</td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>TIN :</td>
            <td>{{ strtoupper($order->examiner->user_tin ?? '-') }}</td>
        </tr>

    </table>

    {{-- BODY --}}
    <div class="body">

        <p>Dear Sir / Madam,</p>

        <p class="subject-line">
            Subject: Appointment of examiner for conduct of Practical / Viva Examination
            {{ strtoupper(optional($order->allocation->from_date)->format('Y')) }}
            - {{ strtoupper(optional($order->allocation->from_date)->format('F')) }}
        </p>

        <p>
            I am pleased to inform you that the Hon’ble Vice-Chancellor has approved your appointment as Practical Examiner for the examination indicated below.
        </p>

        <table class="info-table">

            <tr>
                <td width="120"><strong>Subject</strong></td>
                <td>{{ strtoupper($order->allocation->subject_name ?? '-') }}</td>
            </tr>

            <tr>
                <td><strong>Centre</strong></td>
                <td>{{ strtoupper($order->allocation->centre_name ?? '-') }}</td>
            </tr>

            <tr>
                <td><strong>Batch</strong></td>
                <td>{{ strtoupper($order->allocation->batch_name ?? '-') }}</td>
            </tr>

            <tr>
                <td><strong>Exam Date</strong></td>
                <td>
                    {{ optional($order->allocation->from_date)->format('d-m-Y') }}
                    to
                    {{ optional($order->allocation->to_date)->format('d-m-Y') }}
                </td>
            </tr>

        </table>

        <ol>
            <li>You are requested to report to the examination centre on the specified date and time and follow all examination regulations.</li>
            <li>Exact schedule will be communicated by the concerned college. Please send your Acceptance/Non-Acceptance to: <a href="mailto:drexamrguhs@gmail.com">drexamrguhs@gmail.com</a></li>
            <li>This order stands cancelled if you have crossed the age of 70 years or do not meet minimum eligibility as per NMC/RGUHS norms. If any relative of yours is appearing, please submit non-acceptance.</li>
            <li>Please log in to <a href="https://uims.rguhsqp.com/login" target="_blank">https://uims.rguhsqp.com/login</a> to update your bank details (Account No, IFSC, Bank Name, Branch) for prompt disbursement.</li>
            <li>All documents related to TA / DA shall be submitted to Exam Centre mandatorily.</li>
        </ol>

    </div>

    {{-- FOOTER --}}
    <div class="footer">

        <div class="footer-flex">

            <div class="qr-box">
                @if(!empty($qrBase64))
                    <img src="data:image/png;base64,{{ $qrBase64 }}"
                         width="95"
                         height="95"
                         style="display:block;">
                @else
                    <div style="width:95px;height:95px;border:1px solid #000;text-align:center;font-size:10px;">
                        QR<br>NOT<br>AVAILABLE
                    </div>
                @endif
            </div>

            <div style="text-align:right;">

                <p>Yours faithfully,</p>

                <img src="{{ public_path('images/registrar-signature.png') }}" class="signature-img">

                <div><strong>Registrar (Evaluation)</strong></div>

                <img src="{{ public_path('images/registrar-seal.png') }}" class="seal-img">

            </div>

        </div>

        <div class="note">
            **** This is a system generated communication and does not require signature ****
        </div>

    </div>

</div>

</body>
</html>
