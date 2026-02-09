<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans; font-size: 14px; }
        .header { text-align: center; }
        .table { width: 100%; border-collapse: collapse; }
        .table td { border: 1px solid #000; padding: 6px; }
    </style>
</head>

<body>

<div class="header">
    <h2>RAJIV GANDHI UNIVERSITY OF HEALTH SCIENCES</h2>
    <p>Appointment Order</p>
</div>

<br>

<p>Order No: {{ $order->order_number }}</p>
<p>Date: {{ $order->generated_at->format('d-m-Y') }}</p>

<br>

<p>
    To,<br>
    {{ $order->examiner->name }}<br>
    {{ $order->examiner->college_name ?? '' }}
</p>

<br>

<table class="table">
    <tr>
        <td>Subject</td>
        <td>{{ $order->allocation->subject_name ?? '' }}</td>
    </tr>

    <tr>
        <td>Exam Date</td>
        <td>{{ $order->allocation->exam_date ?? '' }}</td>
    </tr>
</table>

<div style="position:absolute; top:40px; right:40px;">
    <img src="data:image/png;base64,{{ $qrBase64 }}" width="120">
</div>

</body>
</html>
