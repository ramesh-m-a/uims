@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-6">

        <h1 class="text-2xl font-semibold mb-4">
            View Teacher Details
        </h1>

        {{-- PROFILE PREVIEW --}}
        @include('profile.preview-readonly', ['draft' => $draft, 'user' => $user])

        {{-- APPROVAL ACTION --}}
        <form method="POST" action="{{ route('admin.teachers.action', $user->id) }}"
              class="mt-6 border p-4 rounded bg-gray-50">
            @csrf

            <label class="block mb-2 font-medium">Remarks</label>
            <textarea name="remarks"
                      class="w-full border rounded p-2 mb-4"></textarea>

            <div class="flex gap-4">
                <button name="action" value="approve"
                        class="px-4 py-2 bg-green-600 text-white rounded">
                    Approve
                </button>

                <button name="action" value="reject"
                        class="px-4 py-2 bg-red-600 text-white rounded">
                    Reject
                </button>
            </div>
        </form>

    </div>
@endsection
