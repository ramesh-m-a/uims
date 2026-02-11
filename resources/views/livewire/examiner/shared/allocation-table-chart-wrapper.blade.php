<div class="space-y-4 relative" wire:poll.visible.5s>

    {{-- ========================= BREADCRUMB ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => request()->route()?->getName() ?? '',
            'title' => 'Exam Chart',
            'mode'  => 'View / Download'
        ])
    </div>

    {{-- ========================= HEADER ========================= --}}
    <div class="flex justify-between items-center px-4">
        <h1 class="text-lg font-semibold text-gray-700">
            Exam Chart
            <span class="mx-1 text-gray-400">››</span>
            <span class="text-gray-400">View / Download</span>
        </h1>
    </div>

    {{-- ========================= BASE TABLE ========================= --}}
    @include('livewire.examiner.shared.allocation-table-base', [
        'rows' => $rows,
        'requestStatusMap' => $requestStatusMap ?? [],
        'centre' => $centre ?? null,
        'isChartModule' => true
    ])

{{-- 🚫 NO DRAWERS HERE (CHART MODULE) --}}

</div>
