<div class="space-y-4">

    {{-- =========================
     | BREADCRUMB BAR
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'title' => 'Batch Split',
            'mode'  => $breadcrumbMode ?? 'View',
        ])
    </div>

    {{-- =========================
     | PAGE TITLE ROW
     ========================= --}}
    <div class="flex justify-between items-center">
        <h1 class="font-normal text-black-400">
            Batch Split
        </h1>
    </div>

    {{-- =========================
     | LIST TABLE (REUSE CORE TABLE)
     ========================= --}}
    @include('livewire.master._crud-table', [
        'rows'              => $rows,
        'columns'           => $columns,
        'search'            => $search,
        'searchPlaceholder' => 'Search...',
        'canEdit'           => false,
        'canDelete'         => false,
        'canRestore'        => false,
    ])

</div>
