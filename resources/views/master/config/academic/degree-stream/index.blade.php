@extends('components.layouts.app')

@section('title', 'Degree Stream Mapping')

@section('content')
    {{-- Livewire DataTable --}}
    <livewire:master.config.academic.degree-stream.degree-stream-table />
@endsection
