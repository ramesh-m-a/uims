<!DOCTYPE html>
<html>
<head>
    <title>Allocation Debug</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; position: sticky; top: 0; }
        tr:nth-child(even) { background: #fafafa; }
        .unassigned { color: #999; }
        .assigned { color: green; font-weight: bold; }
    </style>
</head>
<body>

<h2>Examiner Allocation Debug</h2>

<p>
    Context:
    Year={{ $context->yearId }},
    Month={{ $context->monthId }},
    Scheme={{ $context->schemeId }},
    Degree={{ $context->degreeId }},
    Stream={{ $context->streamId }}
</p>

<p>Total rows: <strong>{{ count($rows) }}</strong></p>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Centre</th>
        <th>Batch</th>
        <th>Date</th>
        <th>Subject</th>
        <th>Type</th>
        <th>Examiner</th>
        <th>Mobile</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r->centreName }}</td>
            <td>{{ $r->batchName }}</td>
            <td>{{ $r->fromDate }}</td>
            <td>{{ $r->subjectName }}</td>
            <td>{{ $r->examinerType }}</td>
            <td class="{{ $r->name === 'Unassigned' ? 'na' : 'Na' }}">
                {{ $r->name }}
            </td>
            <td>{{ $r->mobile }}</td>
            <td>{{ $r->status }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
<?php
