<div class="space-y-4">

    {{-- =========================
  | BREADCRUMB BAR
  ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'title' => 'Examiner Requests',
            'mode'  => $breadcrumbMode ?? 'View / Update',
        ])
    </div>


    {{-- =========================
     | PAGE TITLE ROW
     ========================= --}}
    <div class="flex justify-between items-center">

        <h1 class="font-normal text-black-400">
            Examiner Requests
        </h1>
    </div>


    <div class="grid grid-cols-12 gap-4 px-4">

        {{-- LEFT CENTRE LIST --}}
        <div class="col-span-3">
            <div class="card">
                <div class="card-header font-semibold">
                    Centres (Requests Only)
                </div>

                <div class="list-group">
                    @foreach($centres as $c)
                        <button
                            wire:click="$set('centre','{{ $c }}')"
                            class="list-group-item {{ $centre === $c ? 'bg-gray-300' : '' }}">
                            {{ $c }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RIGHT TABLE --}}
        <div class="col-span-9">

            @foreach($rows->groupBy('centre_name') as $centreName => $centreRows)

                <div class="card mb-4">

                    <div class="card-header bg-light text-orange-700 font-bold">
                        {{ $centreName }}
                    </div>

                    <div class="card-body">

                        @foreach($centreRows->groupBy('batch_name') as $batch => $batchRows)

                            @foreach($batchRows->groupBy('from_date') as $date => $dateRows)

                                <div class="border rounded p-3 mb-3">

                                    <div class="font-semibold mb-2">
                                        {{ $batch }} — {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                                    </div>

                                    <table class="table table-sm table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Change Request</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @foreach($dateRows as $row)

                                            <tr>

                                                <td>
                                                    {{ $row->examiner_type }}
                                                </td>

                                                <td>
                                                    <div class="flex gap-4">

                                                        <div class="flex-1">

                                                            <div class="bg-orange-500 text-white text-xs px-3 py-1 rounded-full inline-block mb-1">
                                                                From
                                                            </div>

                                                            <div class="text-sm font-semibold text-red-700">
                                                                {{ $row->old_examiner_name }}
                                                            </div>

                                                            <div class="text-xs text-gray-600">
                                                                Mobile: {{ $row->old_examiner_mobile }}
                                                            </div>

                                                            <div class="text-xs text-gray-600">
                                                                Date:
                                                                {{ \Carbon\Carbon::parse($row->from_date)->format('Y-m-d') }}
                                                                →
                                                                {{ \Carbon\Carbon::parse($row->to_date)->format('Y-m-d') }}
                                                            </div>

                                                            <div class="text-xs text-gray-600">
                                                                Attached:
                                                                {{ $row->attached_college_name }}
                                                            </div>

                                                            <div class="text-xs text-red-600 mt-1">
                                                                Reason:
                                                                {{ $row->reason }}
                                                            </div>

                                                        </div>


                                                        <div class="pt-5">→</div>

                                                        <div class="flex-1">

                                                            <div class="bg-green-600 text-white text-xs px-3 py-1 rounded-full inline-block mb-1">
                                                                To
                                                            </div>

                                                            <div class="text-sm font-semibold text-red-700">
                                                                {{ $row->new_examiner_name }}
                                                            </div>

                                                            <div class="text-xs text-gray-600">
                                                                Mobile: {{ $row->new_examiner_mobile }}
                                                            </div>

                                                            <div class="text-xs text-gray-600">
                                                                Date:
                                                                {{ \Carbon\Carbon::parse($row->from_date)->format('Y-m-d') }}
                                                                →
                                                                {{ \Carbon\Carbon::parse($row->to_date)->format('Y-m-d') }}
                                                            </div>

                                                            <div class="text-xs text-gray-600">
                                                                Attached:
                                                                {{ $row->attached_college_name }}
                                                            </div>

                                                            <div class="text-xs text-red-600 mt-1">
                                                                Reason:
                                                                {{ $row->reason }}
                                                            </div>

                                                        </div>


                                                    </div>
                                                </td>

                                                <td>
                                                    <span class="px-3 py-1 text-xs rounded {{ $row->status_colour }}">
                                                        {{ $row->status_label }}
                                                    </span>
                                                </td>

                                                <td class="text-nowrap">

                                                    <button
                                                        wire:click="approveRequest({{ $row->request_id }})"
                                                        class="bg-green-600 text-white px-2 py-1 rounded me-2">
                                                        ✔
                                                    </button>

                                                    <button
                                                        wire:click="rejectRequest({{ $row->request_id }})"
                                                        class="bg-red-600 text-white px-2 py-1 rounded">
                                                        ✖
                                                    </button>

                                                </td>

                                            </tr>

                                        @endforeach
                                        </tbody>

                                    </table>

                                </div>

                            @endforeach
                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>
