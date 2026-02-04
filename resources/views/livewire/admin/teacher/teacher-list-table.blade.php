<div class="space-y-4">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => $this->pageRoute,
            'title' => 'Teachers',
            'mode'  => $breadcrumbMode ?? 'View'
        ])
    </div>

    {{-- Page title --}}
    <div>
        <h1 class="font-normal text-black-400">
            Teachers
            <span class="text-black-350 mx-1">››</span>
            <span class="text-black-350">View</span>
        </h1>
    </div>

    {{-- Filters --}}
    <div class="flex justify-center">
        <div class="flex gap-6 items-end">

            {{-- Stream --}}
            <div class="flex flex-col">
                <label class="text-sm mb-1">Stream</label>
                <select wire:model.live="selectedStream" class="form-select min-w-[240px]" style="text-transform: uppercase;">
                    <option value="">Choose Stream</option>
                    @foreach($streams as $stream)
                        <option value="{{ $stream->id }}">
                            {{ $stream->mas_stream_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- College --}}
            <div class="flex flex-col">
                <label class="text-sm mb-1">College</label>
                <select wire:model.live="selectedCollege"
                        class="form-select min-w-[300px]" style="text-transform: uppercase;"
                    @disabled(!$selectedStream)>
                    <option value="">Choose College</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}">
                            {{ $college->mas_college_code }} - {{ $college->mas_college_name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    {{-- Table --}}
    @if($selectedCollege)
        @include('livewire.master._crud-table', [
            'rows'        => $rows,
            'columns'     => $this->columns(),
            'searchPlaceholder' => 'Search Teacher...',
            'canEdit'     => false,
            'canDelete'   => false,
            'canRestore'  => false,
        ])
    @else
        <div class="flex justify-center mt-6">
            <div class="text-gray-500 text-sm bg-gray-50 border rounded-lg px-6 py-3">
                Please select a stream and college to view teachers.
            </div>
        </div>
    @endif


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
