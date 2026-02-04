@extends('common.layouts.master')

@section('title', 'Upload Eligible Student Details')

@section('content')
    <div class="container">
        <h3 class="text-center">Upload Eligible Student Details</h3>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('batch.upload.import') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            <div class="form-group text-center">
                <label style="color:#9c4b2d;font-weight:500">
                    Choose Excel File (.xlsx or .xls)
                </label>

                <input type="file"
                       name="excel_file"
                       class="form-control"
                       accept=".xlsx,.xls"
                       required>
            </div>

            <div class="text-center mt-3">
                <button class="btn btn-info">Upload</button>
            </div>
        </form>
    </div>
@endsection
