@extends('layouts.app')

@section('content')

    <div class="container py-6">

        <h3 class="mb-4">View ID Card</h3>

        @if(! $card_generated)
            <div class="alert alert-warning">
                ID Card cannot be generated until Aadhaar details are submitted.
            </div>
            @return
        @endif

        <div class="id-card-wrapper">

            {{-- WATERMARK --}}
            <div class="watermark left">Examiner ID Card</div>
            <div class="watermark right">Examiner ID Card</div>

            <div class="id-card">

                <img src="{{ asset('images/RGUHS-logo-AA.png') }}" class="logo">

                <h4 class="university">
                    RAJIV GANDHI UNIVERSITY OF HEALTH SCIENCES, KARNATAKA
                </h4>

                <p class="college">{{ $details->mas_college_name }}</p>

                <img
                    src="{{ $details->photo_path
                    ? asset('storage/'.$details->photo_path)
                    : asset('images/avatar.png') }}"
                    class="photo"
                >

                <h5>{{ $details->name }}</h5>
                <p>{{ $details->mas_designation_name }}</p>
                <p>{{ $details->mas_department_name }}</p>

                <p class="tin">RGUHS TIN {{ $details->user_tin }}</p>

                <div class="qr">{!! $qr !!}</div>

            </div>
        </div>
    </div>

    <style>
        .id-card-wrapper {
            position: relative;
            width: 340px;
        }
        .id-card {
            border: 2px solid #000;
            padding: 16px;
            text-align: center;
            background: #fff;
        }
        .logo { height: 60px; }
        .photo {
            width: 120px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
        }
        .watermark {
            position: absolute;
            top: 0;
            bottom: 0;
            font-size: 28px;
            color: rgba(0,0,0,0.05);
            writing-mode: vertical-rl;
        }
        .watermark.left { left: -24px; }
        .watermark.right {
            right: -24px;
            transform: rotate(180deg);
        }
        .qr { margin-top: 12px; }
    </style>

@endsection
