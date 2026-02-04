@extends('components.layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-semibold">
                Teacher Profile – Principal Review
            </h1>

            <span class="px-3 py-1 rounded text-sm bg-yellow-100 text-yellow-800">
            {{ $draft->status->mas_status_name }}
        </span>
        </div>

        {{-- BASIC DETAILS --}}
        <x-card title="Basic Details">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <p><b>Date of Birth:</b> {{ data_get($draft->data, 'basic.dob', '-') }}</p>
                <p><b>Gender ID:</b> {{ data_get($draft->data, 'basic.gender_id', '-') }}</p>
                <p><b>Father Name:</b> {{ data_get($draft->data, 'basic.father_name', '-') }}</p>
                <p><b>Religion ID:</b> {{ data_get($draft->data, 'basic.religion_id', '-') }}</p>
                <p><b>Category ID:</b> {{ data_get($draft->data, 'basic.category_id', '-') }}</p>
            </div>
        </x-card>

        {{-- ADDRESS --}}
        <x-card title="Address">
            @php($addr = data_get($draft->data, 'address'))
            <div class="text-sm space-y-2">
                <p><b>Permanent Address:</b></p>
                <p>
                    {{ data_get($addr, 'permanent.address_1') }},
                    {{ data_get($addr, 'permanent.address_2') }},
                    {{ data_get($addr, 'permanent.district') }},
                    {{ data_get($addr, 'permanent.state_id') }} -
                    {{ data_get($addr, 'permanent.pincode') }}
                </p>

                @if (!data_get($addr, 'same_address'))
                    <p class="mt-2"><b>Temporary Address:</b></p>
                    <p>
                        {{ data_get($addr, 'temporary.address_1') }},
                        {{ data_get($addr, 'temporary.address_2') }},
                        {{ data_get($addr, 'temporary.district') }},
                        {{ data_get($addr, 'temporary.state_id') }} -
                        {{ data_get($addr, 'temporary.pincode') }}
                    </p>
                @endif
            </div>
        </x-card>

        {{-- QUALIFICATIONS --}}
        <x-card title="Qualifications">
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Stream</th>
                    <th class="p-2 border">Degree</th>
                    <th class="p-2 border">University</th>
                    <th class="p-2 border">Year</th>
                </tr>
                </thead>
                <tbody>
                @foreach (data_get($draft->data, 'qualification', []) as $q)
                    <tr>
                        <td class="p-2 border">{{ $q['stream_id'] ?? '-' }}</td>
                        <td class="p-2 border">{{ $q['degree_id'] ?? '-' }}</td>
                        <td class="p-2 border">{{ $q['university_name'] ?? '-' }}</td>
                        <td class="p-2 border">{{ $q['year_of_award'] ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </x-card>

        {{-- WORK EXPERIENCE --}}
        <x-card title="Work Experience">
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Designation</th>
                    <th class="p-2 border">Institution</th>
                    <th class="p-2 border">From</th>
                    <th class="p-2 border">To</th>
                </tr>
                </thead>
                <tbody>
                @foreach (data_get($draft->data, 'work', []) as $w)
                    <tr>
                        <td class="p-2 border">{{ $w['designation_id'] ?? '-' }}</td>
                        <td class="p-2 border">{{ $w['institution_name'] ?? '-' }}</td>
                        <td class="p-2 border">{{ $w['from_date'] ?? '-' }}</td>
                        <td class="p-2 border">
                            @if (($w['is_current'] ?? false) || ($w['till_date'] ?? false))
                                Present
                            @else
                                {{ $w['to_date'] ?? '-' }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </x-card>

        {{-- BANK DETAILS --}}
        <x-card title="Bank Details">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <p><b>Account No:</b> {{ data_get($draft->data, 'bank.account.account_number', '-') }}</p>
                <p><b>Account Name:</b> {{ data_get($draft->data, 'bank.account.account_name', '-') }}</p>
                <p><b>Bank ID:</b> {{ data_get($draft->data, 'bank.account.bank_id', '-') }}</p>
                <p><b>IFSC:</b> {{ data_get($draft->data, 'bank.account.ifsc_code', '-') }}</p>
                <p><b>Salary Mode:</b> {{ data_get($draft->data, 'bank.salary.salary_mode_id', '-') }}</p>
            </div>
        </x-card>

        {{-- DOCUMENTS --}}
        <x-card title="Uploaded Documents">
            <ul class="list-disc pl-6 text-sm space-y-1">
                @foreach (data_get($draft->data, 'documents', []) as $doc)
                    <li>
                        <a href="{{ asset('storage/'.$doc['file_path']) }}"
                           target="_blank"
                           class="text-blue-600 underline">
                            View Document
                        </a>
                    </li>
                @endforeach
            </ul>
        </x-card>

        {{-- ACTIONS --}}
        @if ($draft->status->mas_status_code === 'Submitted')
            <div class="flex gap-4 pt-6">
                <form method="POST"
                      action="{{ route('principal.profiles.approve', $user) }}">
                    @csrf
                    <button class="px-6 py-2 bg-green-600 text-white rounded">
                        Approve & Send to RGUHS
                    </button>
                </form>

                <form method="POST"
                      action="{{ route('principal.profiles.reject', $user) }}">
                    @csrf
                    <input
                        type="text"
                        name="remarks"
                        placeholder="Reason for rejection"
                        required
                        class="border rounded px-3 py-2 w-80"
                    >
                    <button class="px-6 py-2 bg-red-600 text-white rounded ml-2">
                        Reject & Send Back
                    </button>
                </form>
            </div>
        @endif

    </div>
@endsection
