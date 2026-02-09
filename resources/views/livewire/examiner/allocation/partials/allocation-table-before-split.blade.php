@php
    $isCollegeUser = auth()->check() && auth()->user()->user_role_id === 3;
@endphp

<div class="space-y-4 relative">

    {{-- ========================= BREADCRUMB ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => request()->route()?->getName() ?? '',
            'title' => 'Examiner Allocation',
            'mode'  => 'View / Allocate'
        ])
    </div>

    {{-- ========================= HEADER ========================= --}}
    <div class="flex justify-between items-center px-4">
        <h1 class="text-lg font-semibold text-gray-700">
            Examiner Allocation
            <span class="mx-1 text-gray-400">››</span>
            <span class="text-gray-400">View / Allocate</span>
        </h1>
    </div>

    {{-- ========================= MAIN LAYOUT ========================= --}}
    <div class="grid grid-cols-12 gap-4 px-4">

        @php
            $grouped = $rows->groupBy('centre_name');

            if ($centre) {
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

                    @if($centre)
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

        {{-- ================= RIGHT PANEL SCROLL ================= --}}
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
                                                    <th class="text-end pe-3 w-[120px]">Actions</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                @foreach($dateRows as $row)

                                                    @php
                                                        $statusData = $requestStatusMap[$row->batch_id][$row->examiner_id] ?? null;
                                                        $isPendingRequest = $statusData && ($statusData['is_pending'] ?? false);
                                                    @endphp

                                                    <tr class="align-middle">

                                                        <td class="ps-2">{{ $row->examiner_type }}</td>

                                                        <td class="fw-semibold">
                                                            {{ $row->examiner_name }}
                                                            @if($row->is_additional ?? false)
                                                                <div class="text-primary text-xs font-bold">ADDITIONAL</div>
                                                            @endif
                                                        </td>

                                                        <td class="text-end pe-3 font-mono">
                                                            {{ $row->mobile }}
                                                        </td>

                                                        {{-- STATUS --}}
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

                                                        {{-- ACTIONS --}}
                                                        <td class="text-end pe-3 text-lg whitespace-nowrap">

                                                            {{-- ADMIN --}}
                                                            @if(!$isCollegeUser)

                                                                @if($isPendingRequest)
                                                                    <span style="color:#fd7e14;">⏳</span>
                                                                @else
                                                                    <button wire:click="openDrawer({{ json_encode((array)$row) }})">🔁</button>
                                                                    <button wire:click="openContactRGUHSDrawer({{ json_encode((array)$row) }})">🔒</button>
                                                                    <button wire:click="openChangeDateDrawer({{ json_encode((array)$row) }})">📅</button>
                                                                    <button wire:click="openAddDrawer({{ $row->id }})">➕</button>
                                                                @endif

                                                            @endif

                                                            {{-- COLLEGE --}}
                                                            @if($isCollegeUser)

                                                                @if($isPendingRequest)
                                                                    <span style="color:#fd7e14;">⏳</span>
                                                                @else
                                                                    <button
                                                                        wire:click="openRequestDrawer({{ json_encode((array)$row) }})"
                                                                        class="p-1 hover:bg-gray-100 rounded"
                                                                    >
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                             class="w-4 h-4 text-black"
                                                                             fill="currentColor"
                                                                             viewBox="0 0 24 24">
                                                                            <path d="M20 4H4a2 2 0 00-2 2v.01l10 6.49 10-6.49V6a2 2 0 00-2-2z"/>
                                                                            <path d="M22 8.236l-9.445 6.13a1 1 0 01-1.11 0L2 8.236V18a2 2 0 002 2h16a2 2 0 002-2V8.236z"/>
                                                                        </svg>
                                                                    </button>
                                                                @endif

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

    @include('livewire.examiner.allocation.partials.drawers')
</div>

{{-- ================= TOAST SCRIPT RESTORED ================= --}}
{{--<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('toast', (data) => {
            const toast = document.createElement('div');
            toast.innerText = data.message;

            Object.assign(toast.style,{
                position:'fixed',
                bottom:'20px',
                right:'20px',
                background:'#198754',
                color:'#fff',
                padding:'10px 16px',
                borderRadius:'6px',
                zIndex:'9999'
            });

            document.body.appendChild(toast);
            setTimeout(()=>toast.remove(),2500);
        });
    });
</script>--}}



<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('toast', (data) => {
            const toast = document.createElement('div');
            toast.innerText = data.message;

            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = '#198754';
            toast.style.color = '#fff';
            toast.style.padding = '10px 16px';
            toast.style.borderRadius = '6px';
            toast.style.fontSize = '14px';
            toast.style.zIndex = '9999';
            toast.style.boxShadow = '0 4px 10px rgba(0,0,0,0.2)';

            document.body.appendChild(toast);

            setTimeout(() => toast.remove(), 2500);
        });
    });
</script>
