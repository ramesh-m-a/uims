<div class="space-y-4">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => $this->pageRoute,
            'title' => 'Principals',
            'mode'  => $breadcrumbMode ?? 'View'
        ])
    </div>

    {{-- Page title --}}
    <div>
        <h1 class="font-normal text-black-400">
            Principals
            <span class="text-black-350 mx-1">››</span>
            <span class="text-black-350">View</span>
        </h1>
    </div>


    {{-- Table --}}
    @include('livewire.master._crud-table', [
        'rows'        => $rows,
        'columns'     => $this->columns(),
        'searchPlaceholder' => 'Search Principal...',
        'canEdit'     => false,
        'canDelete'   => false,
        'canRestore'  => false,
    ])

{{-- 🔑 REUSED PHOTO PREVIEW MODAL --}}
    @include('components.photo-preview-modal')

    {{-- Loading Modal --}}
    <div
        wire:loading.delay
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center"
    >
        <div class="bg-white rounded-lg shadow-lg p-6 text-center w-80">
            <h5 class="text-lg font-semibold mb-2">Please Wait</h5>
            <p class="text-sm text-gray-600">
                Working on the records, do not refresh...
            </p>
        </div>
    </div>

</div>
