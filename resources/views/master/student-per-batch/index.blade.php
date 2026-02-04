@extends('components.layouts.app') {{-- adjust to your layout --}}

@section('content')
    <div class="container">
        <h3>Student Per Batch</h3>

        {{-- Livewire component --}}
        <livewire:master.student-per-batch.index />
    </div>
@endsection
