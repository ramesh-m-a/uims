@extends('components.layouts.app')

@section('title', 'Department')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.academic.department.department-table />
@endsection
