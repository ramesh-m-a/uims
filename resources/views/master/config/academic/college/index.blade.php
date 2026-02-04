@extends('components.layouts.app')

@section('title', 'Colleges')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.academic.college.college-table />
@endsection
