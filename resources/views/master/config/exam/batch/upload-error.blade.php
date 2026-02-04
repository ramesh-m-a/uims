@extends('common.layouts.master')

@section('title', 'Eligible Student Details Upload Error')

@section('content')
    <div class="container">

        <h3 class="text-center">Eligible Student Details Upload Error</h3>

        <table class="table table-bordered table-striped mt-3">
            <thead>
            <tr>
                <th>Row #</th>
                <th>Details</th>
                <th>Errors</th>
            </tr>
            </thead>
            <tbody>
            @foreach($validatedRows as $row)
                <tr>
                    <td>{{ $row['row_num'] }}</td>

                    <td>
                    <pre style="white-space: pre-wrap; margin:0;">
{{ json_encode($row['data'], JSON_PRETTY_PRINT) }}
                    </pre>
                    </td>

                    <td>
                        @foreach($row['errors'] as $e)
                            <div class="text-danger">{{ $e }}</div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="text-center mt-3">
            <a href="{{ route('batch.index') }}" class="btn btn-primary">Back</a>
        </div>
    </div>
@endsection
