@extends('components.layouts.app')

@section('title', 'Users')

@section('content')
    {{-- =========================
     | USER MASTER TABLE
     ========================= --}}
    <livewire:user.user-table />
@endsection
