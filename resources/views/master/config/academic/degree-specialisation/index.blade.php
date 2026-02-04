@extends('components.layouts.app')

@section('title', 'Degree Specialisation')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.academic.degree-specialisation.degree-specialisation-table />
@endsection
