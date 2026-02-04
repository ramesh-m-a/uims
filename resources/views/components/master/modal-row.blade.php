@props([
    'show' => false,
    'title' => '',
    'onSave' => 'save',
    'onCancel' => 'cancelEdit',
])

@if($show)
    <div
        class="admin-loader"
        wire:ignore.self
        x-data
        @keydown.escape.window="$wire.call('{{ $onCancel }}')"
        x-cloak
    >
        <div class="admin-loader-box-row w-[700px] p-4" role="dialog" aria-modal="true">

            <h6 class="mb-3 text-lg font-medium text-gray-800">
                {{ $title }}
            </h6>

            {{-- FORM BODY --}}
            <div class="container-fluid">
                {{ $slot }}
            </div>

            {{-- FOOTER --}}
            <div class="d-flex justify-content-end gap-2 mt-4">
                <button
                    type="button"
                    class="btn btn-secondary"
                    wire:click="{{ $onCancel }}"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="btn btn-primary"
                    wire:click="{{ $onSave }}"
                    wire:loading.attr="disabled"
                >
                    Save
                </button>
            </div>

        </div>
    </div>
@endif
