@extends('components.layouts.app')

@section('content')

    <div class="p-6">

        <h2 class="text-xl font-semibold mb-4">
            Examiner Requests
        </h2>

        <div class="bg-white rounded shadow p-4">

            <table class="table-auto w-full border">
                <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">College</th>
                    <th class="p-2 border">Examiner</th>
                    <th class="p-2 border">New Examiner</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Action</th>
                </tr>
                </thead>

                <tbody>
                @forelse($requests as $r)
                    <tr>
                        <td class="p-2 border">{{ $r->id }}</td>
                        <td class="p-2 border">
                            {{ $r->college_examiner_request_details_college_id }}
                        </td>
                        <td class="p-2 border">
                            {{ $r->college_examiner_request_details_examiner_id }}
                        </td>
                        <td class="p-2 border">
                            {{ $r->college_examiner_request_details_new_examiner_id }}
                        </td>
                        <td class="p-2 border">
                            {{ $r->college_examiner_request_details_status_id }}
                        </td>

                        <td class="p-2 border">

                            @if($r->college_examiner_request_details_status_id == 26)

                                <form method="POST"
                                      action="{{ route('examiner.requests.approve', $r->id) }}"
                                      class="inline">
                                    @csrf
                                    <button class="bg-green-600 text-white px-2 py-1 rounded">
                                        ✔
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('examiner.requests.reject', $r->id) }}"
                                      class="inline">
                                    @csrf
                                    <button class="bg-red-600 text-white px-2 py-1 rounded">
                                        ✖
                                    </button>
                                </form>

                            @endif

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-4">
                            No Records Found
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

            <div class="mt-4">
                {{ $requests->links() }}
            </div>

        </div>

    </div>

@endsection
