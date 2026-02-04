{{-- =========================================================
 | PHOTO PREVIEW MODAL (REUSABLE, LIVEWIRE-DRIVEN)
 | SAFE DEFAULTS:
 | - Works even if variables are not defined
 ========================================================= --}}

@php
    $show = $showPhotoModal ?? false;
    $src  = $photo ?? null;
@endphp

@if($show && $src)
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-4 max-w-md w-full relative">

            <button
                class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl"
                wire:click="$set('showPhotoModal', false)"
            >
                ✕
            </button>

            <img
                src="{{ $src }}"
                class="w-full h-auto rounded"
                alt="Photo Preview"
            >
        </div>
    </div>
@endif
