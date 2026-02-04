@extends('components.layouts.app')

@section('content')
    <div class="container">

        <h4>Edit Student Per Batch</h4>

        <form action="{{ route('student-per-batch.update', $row->id) }}" method="POST">
            @csrf
            @method('PUT')

            <h6>Subject: {{ $row->mas_student_per_batch_subject_id }}</h6>
            <h6>Degree: {{ $row->mas_student_per_batch_degree_id }}</h6>

            <div class="mb-3">
                <label>Total Students</label>
                <input type="number" name="total_number" class="form-control" value="{{ $row->mas_student_per_batch_total_number }}" required>
            </div>

            <div class="mb-3">
                <label>Per Day</label>
                <input type="number" name="per_day" class="form-control" value="{{ $row->mas_student_per_batch_per_day }}" required>
            </div>

            <button class="btn btn-success">Update</button>
            <a href="{{ route('master.student-per-batch.index') }}" class="btn btn-secondary">Back</a>
        </form>

    </div>
@endsection
