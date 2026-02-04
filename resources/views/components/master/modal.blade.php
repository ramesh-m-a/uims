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
        @keydown.enter.window.prevent="if ($wire.showModal) $wire.call('{{ $onSave }}')"
        x-cloak
    >
        <div
            class="admin-loader-box w-[600px] p-6"
            role="dialog"
            aria-modal="true"
        >

            {{-- MODAL TITLE --}}
            <div class="modal-header justify-center">
                <h5 class="modal-title text-center w-100">{{ $title }}</h5>
            </div>

            {{-- 🔥 IMPORTANT FIX
                 ❌ Removed space-y / flex
                 ✅ Allow Bootstrap row/col layouts
            --}}
            <div class="modal-body">
                {{ $slot }}
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex justify-end gap-2 mt-4">
                <button
                    type="button"
                    wire:click="{{ $onCancel }}"
                    class="btn btn-secondary px-4 py-2 rounded-md"
                >
                    <i class="fa fa-close"></i> Cancel
                </button>

                <button
                    type="button"
                    wire:click="{{ $onSave }}"
                    wire:loading.attr="disabled"
                    class="btn btn-primary px-4 py-2 rounded-md"
                >
                    <i class="fa fa-save"></i> Save
                </button>
            </div>

        </div>
    </div>
@endif
