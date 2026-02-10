@php
    $isCollegeUser = auth()->check() && auth()->user()->user_role_id === 3;

    // ⭐ SAFETY DEFAULTS
    $requestStatusMap = $requestStatusMap ?? [];
    $isAppointmentModule = $isAppointmentModule ?? false;
@endphp



    {{-- ========================= MAIN LAYOUT ========================= --}}
    <div class="grid grid-cols-12 gap-4 px-4">

        @php
            $rowsCollection = collect($rows ?? [])
                ->map(fn($r) => is_array($r) ? (object) $r : $r);

            $allRowsCollection = collect($allRows ?? $rows ?? [])
                ->map(fn($r) => is_array($r) ? (object) $r : $r);

            /** ⭐ LEFT PANEL MUST ALWAYS USE FULL DATASET */
            $grouped = $allRowsCollection->groupBy('centre_name');

                                    if(!empty($centre)) {
                                        $centres = collect([$centre]);
                                        $activeCentre = $centre;
                                    } else {
                                        $centres = $grouped->keys();
                                        $activeCentre = $centres->first();
                                    }
        @endphp

        {{-- ================= LEFT PANEL ================= --}}
        <div class="col-span-12 md:col-span-3 lg:col-span-3">
            <div class="card h-full">

                <div class="card-header font-semibold flex justify-between items-center">
                    <span>Centres</span>

                    @if(!empty($centre))
                        <button wire:click="$set('centre', null)"
                                class="text-xs font-semibold px-2 py-1 rounded-md
                                bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                            Show All
                        </button>
                    @endif
                </div>

                <div class="list-group list-group-flush overflow-auto text-start" style="max-height:78vh;">
                    @foreach($centres as $c)
                        <button
                            wire:click="$set('centre','{{ addslashes($c) }}')"
                            class="list-group-item list-group-item-action text-start px-3 py-2 text-sm rounded-md mx-1 my-0.5
                            hover:bg-gray-200
                            {{ ($centre ?? $activeCentre) === $c ? 'bg-gray-300' : 'bg-white' }}">
                            {{ $c }}
                        </button>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- ================= RIGHT PANEL ================= --}}
        <div class="col-span-12 md:col-span-9 lg:col-span-9">
            <div class="overflow-auto pr-2" style="max-height:78vh;">

                @foreach($grouped as $centreName => $centreRows)
                    @if($activeCentre !== $centreName) @continue @endif

                    <div class="card">
                        <div class="card-header bg-light text-orange-700 font-extrabold">
                            {{ $centreName }}
                        </div>

                        <div class="card-body space-y-4">

                            @foreach($centreRows->groupBy('batch_name') as $batch => $batchRows)
                                @foreach($batchRows->groupBy('from_date') as $date => $dateRows)

                                    @php
                                        $formattedDate = \Carbon\Carbon::parse($date)->format('d-m-Y');
                                    @endphp

                                    <div class="border rounded-lg p-3 bg-white">

                                        <div class="text-base font-semibold mb-2">
                                            {{ $batch }} - 📅 {{ $formattedDate }}
                                        </div>

                                        <div class="overflow-x-auto">
                                            <table class="table table-sm table-bordered w-full admin-table">

                                                <thead>
                                                <tr class="text-gray-600 bg-light">
                                                    <th class="ps-2 w-[120px]">Type</th>
                                                    <th class="w-[320px]">Examiner</th>
                                                    <th class="text-end w-[160px] pe-3">Mobile</th>
                                                    <th class="text-center w-[220px]">Status</th>
                                                    <th class="text-end pe-3 w-[140px]">Actions</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                @foreach($dateRows as $row)

                                                    @php
                                                        $statusData = $requestStatusMap[$row->batch_id][$row->examiner_id] ?? null;
                                                    @endphp

                                                    <tr class="align-middle">

                                                        <td class="ps-2">{{ $row->examiner_type }}</td>

                                                        <td class="fw-semibold">
                                                            {{ $row->examiner_name }}
                                                            @if($row->is_additional ?? false)
                                                                <div class="text-primary text-xs font-bold">
                                                                    ADDITIONAL
                                                                </div>
                                                            @endif
                                                        </td>

                                                        <td class="text-end pe-3 font-mono">
                                                            {{ $row->mobile }}
                                                        </td>

                                                        <td class="text-center">
                                                            @if($statusData)
                                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusData['colour'] }}">
                                                                    {{ $statusData['label'] }}
                                                                </span>
                                                            @else
                                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold bg-green-200">
                                                                    Assigned
                                                                </span>
                                                            @endif
                                                        </td>

                                                        {{-- ⭐ SAFE SPLIT SWITCH --}}
                                                        <td class="text-end pe-3 text-lg whitespace-nowrap">

                                                            @if($isAppointmentModule)

                                                                @include('livewire.examiner.appointment-order.partials.actions-appointment')

                                                            @else

                                                                @include('livewire.examiner.allocation.partials.actions-allocation')

                                                            @endif

                                                        </td>

                                                    </tr>

                                                @endforeach
                                                </tbody>

                                            </table>
                                        </div>

                                    </div>

                                @endforeach
                            @endforeach

                        </div>
                    </div>

                @endforeach

            </div>
        </div>

    </div>
