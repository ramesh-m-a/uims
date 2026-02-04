@extends('components.layouts.app')

@section('title', 'Designation')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.academic.designation.designation-table />
@endsection
