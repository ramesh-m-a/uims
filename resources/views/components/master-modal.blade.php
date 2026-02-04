@props([
    'title' => 'Add',
    'show' => false,
])

@if($show)
    <div class="admin-loader">
        <div class="admin-loader-box w-[480px]">

            {{-- HEADER --}}
            <h4 class="text-lg font-semibold text-center mb-4">
                {{ $title }}
            </h4>

            {{-- BODY --}}
            <div class="space-y-3">
                {{ $slot }}
            </div>

            {{-- FOOTER --}}
            <div class="flex justify-end gap-2 mt-5">
                <button
                    wire:click="cancelEdit"
                    class="btn btn-secondary btn-sm">
                    <i class="fa fa-times"></i> Cancel
                </button>

                <button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="btn btn-primary btn-sm">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>

        </div>
    </div>
@endif
