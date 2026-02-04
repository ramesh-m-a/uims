@extends('components.layouts.app')

@section('title', 'Examiner Details')

<h6 style="color:red">LIVEWIRE VIEW LOADED</h6>

@section('content')
    <div class="container-fluid">

        <h5 class="mb-3">Examiner Details</h5>

        <div class="page-header mb-3">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Examiner Details
                    </li>
                </ol>
            </nav>

            <h5 class="page-title">Examiner Details</h5>

        </div>

        <livewire:examiner.index />

    </div>
@endsection
