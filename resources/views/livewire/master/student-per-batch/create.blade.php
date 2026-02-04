@extends('components.layouts.app')

@section('content')
    <div class="container">

        <h4>Add Student Per Batch</h4>

        <form action="{{ route('master.student-per-batch.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Subject ID</label>
                <input type="number" name="subject_id" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Degree ID</label>
                <input type="text" name="degree_id" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Total Students</label>
                <input type="number" name="total_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Per Day</label>
                <input type="number" name="per_day" class="form-control" required>
            </div>

            <button class="btn btn-success">Save</button>
            <a href="{{ route('master.student-per-batch.index') }}" class="btn btn-secondary">Back</a>
        </form>

    </div>
@endsection
