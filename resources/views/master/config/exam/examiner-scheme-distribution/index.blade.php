@extends('components.layouts.app')

@section('title', 'Scheme Distribution - Examiner')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.exam.examiner-scheme-distribution-table />
@endsection
