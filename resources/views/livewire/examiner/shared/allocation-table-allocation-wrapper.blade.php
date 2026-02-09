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

    {{-- ========================= BASE TABLE ========================= --}}
    @include('livewire.examiner.shared.allocation-table-base', [
        'rows' => $rows,
        'requestStatusMap' => $requestStatusMap,
        'centre' => $centre ?? null,
        'isAppointmentModule' => false
    ])

    {{-- ========================= DRAWERS ========================= --}}
    @include('livewire.examiner.allocation.partials.drawers')

</div>
