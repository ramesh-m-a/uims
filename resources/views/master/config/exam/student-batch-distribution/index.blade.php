@extends('components.layouts.app')

@section('title', 'Student Batch Distribution')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.exam.student-batch-distribution-table />
@endsection
