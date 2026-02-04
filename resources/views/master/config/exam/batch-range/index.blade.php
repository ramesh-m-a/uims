@extends('components.layouts.app')

@section('title', 'Batch Range')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.exam.batch-range-table />
@endsection
