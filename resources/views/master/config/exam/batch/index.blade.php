@extends('components.layouts.app')

@section('title', 'Batch')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.exam.batch-table />
@endsection
