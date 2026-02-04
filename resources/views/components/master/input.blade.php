@props([
    'label',
    'model',
    'placeholder' => '',
    'required' => false,
    'autofocus' => false,
    'onlyChars' => false,
    'uppercase' => false,
])

@php
    $error = $errors->first($model);
@endphp

<div class="mb-4 flex justify-center">
    <div class="flex items-center gap-3 w-[520px]">

        {{-- LABEL --}}
        <label
            class="w-20 text-right font-normal"
        >
            {{ $label }}

        </label>

        {{-- INPUT + ERROR --}}
        <div class="flex-1">
            <input
                type="text"
                wire:model.defer="{{ $model }}"
                placeholder="{{ $placeholder }}"
                {{ $autofocus ? 'autofocus' : '' }}
                class="form-control col-sm-6 makeSlug {{ $error ? 'border-danger' : '' }}"
                @if($onlyChars)
                    oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                @endif
                @if($uppercase)
                    oninput="this.value = this.value.toUpperCase()"
                @endif
            >

            @if($error)
                <div class="text-sm mt-1" style="color: #dc3545;">
                    {{ $error }}
                </div>
            @endif
        </div>

    </div>
</div>
