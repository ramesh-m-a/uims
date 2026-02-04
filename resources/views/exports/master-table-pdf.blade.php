<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        @page {
            margin: 140px 40px 90px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        /* ================= HEADER ================= */
        header {
            position: fixed;
            top: -120px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .header-logo {
            height: 70px;
            margin-bottom: 6px;
        }

        .org-name {
            font-size: 14px;
            font-weight: bold;
        }

        .report-title {
            font-size: 13px;
            font-weight: bold;
        }

        .double-line {
            margin-top: 6px;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
        }

        /* ================= TABLE ================= */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

{{-- ================= HEADER ================= --}}
<header>
    @php
        $logoBase64 = null;

        if (!empty($meta['logo']) && file_exists($meta['logo'])) {
            $type = pathinfo($meta['logo'], PATHINFO_EXTENSION);
            $data = file_get_contents($meta['logo']);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    @endphp

    @if($logoBase64)
        <img src="{{ $logoBase64 }}" class="header-logo">
    @endif

    <div class="org-name">{{ $meta['org'] }}</div>
    <div class="report-title">{{ $meta['title'] }}</div>
    <div class="double-line"></div>
</header>

{{-- ================= TABLE ================= --}}
<table class="data-table">
    <thead>
    <tr>
        @foreach(array_keys($rows->first()) as $heading)
            <th>{{ $heading }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        <tr>
            @foreach($row as $value)
                <td>{{ $value }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

{{-- ================= DOMPDF CANVAS ================= --}}
<script type="text/php">
    if (isset($pdf)) {

        $font = $fontMetrics->getFont("Helvetica", "normal");
        $size = 9;

        $y = $pdf->get_height() - 45;

        /* LEFT */
        $pdf->page_text(
            40,
            $y,
            "User: {{ auth()->user()->name ?? 'System' }}",
        $font,
        $size
    );

    /* CENTER */
    $text = "Page {$PAGE_NUM} / {$PAGE_COUNT}";
    $w = $fontMetrics->getTextWidth($text, $font, $size);

    $pdf->page_text(
        ($pdf->get_width() - $w) / 2,
        $y,
        $text,
        $font,
        $size
    );

    /* RIGHT */
    $right = "Generated on {{ $meta['date'] }}";
    $rw = $fontMetrics->getTextWidth($right, $font, $size);

    $pdf->page_text(
        $pdf->get_width() - $rw - 40,
        $y,
        $right,
        $font,
        $size
    );

    /* ================= WATERMARK TILES ================= */
    $logo = "{{ $meta['logo'] }}";

    if (file_exists($logo)) {

        $wPage = $pdf->get_width();
        $hPage = $pdf->get_height();

        $tileW = 70;
        $tileH = 70;

        $pdf->save();
        $pdf->set_opacity(0.10);

        for ($x = -$wPage; $x < $wPage * 2; $x += 120) {
            for ($y = -$hPage; $y < $hPage * 2; $y += 120) {
                $pdf->rotate(30, $wPage / 2, $hPage / 2);
                $pdf->image($logo, $x, $y, $tileW, $tileH, null);
            }
        }

        $pdf->restore();
    }
}
</script>

</body>
</html>
