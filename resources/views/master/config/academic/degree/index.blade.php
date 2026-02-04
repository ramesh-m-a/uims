@extends('components.layouts.app')

@section('title', 'Degree')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.academic.degree.degree-table />
@endsection
