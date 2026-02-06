@extends('components.layouts.app')

@section('title', 'Examiner Requests')

@section('content')

    {{-- Livewire Request Queue Table --}}
    <livewire:examiner.requests.request-queue-table />

@endsection
