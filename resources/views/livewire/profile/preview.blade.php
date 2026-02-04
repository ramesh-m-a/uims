@extends('layouts.app')

@if ($principalRemark)
    <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded mt-6">
        <h3 class="font-semibold text-red-700 mb-1">
            Principal Remarks
        </h3>

        <p class="text-sm text-gray-800">
            {{ data_get($principalRemark->new_values, 'remarks', '—') }}
        </p>

        <p class="text-xs text-gray-500 mt-1">
            Rejected on {{ $principalRemark->created_at->format('d M Y, h:i A') }}
        </p>
    </div>
@endif


@section('content')
    <div class="max-w-7xl mx-auto px-6 py-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold">
                My Details » Preview
            </h1>

            <a href="{{ route('my-details.edit') }}"
               class="text-sm text-blue-600 hover:underline">
                ← Back to Edit
            </a>
        </div>

        {{-- PROFILE PHOTO --}}
        <div class="flex justify-center mb-6">
            <img
                src="{{ auth()->user()->avatar_url }}"
                class="h-40 w-32 object-cover border rounded"
                alt="Profile Photo"
            >
        </div>

        {{-- BASIC DETAILS --}}
        <x-preview.section title="Basic Details">
            <x-preview.row label="Date of Birth" :value="$draft->data['basic']['dob'] ?? '-'"/>
            <x-preview.row label="Gender" :value="$draft->data['basic']['gender_id'] ?? '-'"/>
            <x-preview.row label="Father / Spouse Name" :value="$draft->data['basic']['father_name'] ?? '-'"/>
            <x-preview.row label="Religion" :value="$draft->data['basic']['religion_id'] ?? '-'"/>
            <x-preview.row label="Category" :value="$draft->data['basic']['category_id'] ?? '-'"/>
        </x-preview.section>

        {{-- ADDRESS --}}
        <x-preview.section title="Address Details">
            <x-preview.subsection title="Permanent Address">
                <x-preview.row label="Address"
                               :value="implode(', ', array_filter($draft->data['address']['permanent'] ?? []))"/>
            </x-preview.subsection>

            <x-preview.subsection title="Correspondence Address">
                <x-preview.row label="Address"
                               :value="implode(', ', array_filter($draft->data['address']['temporary'] ?? []))"/>
            </x-preview.subsection>
        </x-preview.section>

        {{-- QUALIFICATION --}}
        <x-preview.section title="Qualification Details">
            @forelse($draft->data['qualification'] ?? [] as $q)
                <x-preview.row
                    label="{{ $q['degree'] ?? 'Degree' }}"
                    :value="$q['university'] ?? '-'"/>
            @empty
                <p class="text-sm text-gray-500">No qualification details entered.</p>
            @endforelse
        </x-preview.section>

        {{-- WORK --}}
        <x-preview.section title="Work Experience">
            @forelse($draft->data['work'] ?? [] as $w)
                <x-preview.row
                    label="{{ $w['designation'] ?? 'Designation' }}"
                    :value="$w['institution'] ?? '-'"/>
            @empty
                <p class="text-sm text-gray-500">No work experience entered.</p>
            @endforelse
        </x-preview.section>

        {{-- BANK --}}
        <x-preview.section title="Bank Details">
            <x-preview.row label="Account Number" :value="$draft->data['bank']['account_number'] ?? '-'"/>
            <x-preview.row label="IFSC" :value="$draft->data['bank']['ifsc'] ?? '-'"/>
            <x-preview.row label="Bank Name" :value="$draft->data['bank']['bank_name'] ?? '-'"/>
        </x-preview.section>

        {{-- DOCUMENTS --}}
        <x-preview.section title="Documents">
    @forelse($draft->data['documents']
