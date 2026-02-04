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

<div class="form-group row mb-3">
    {{-- LABEL --}}
    <label class="col-sm-3 col-form-label text-sm-right pr-0">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    {{-- INPUT --}}
    <div class="col-sm-8">
        <input
            type="text"
            wire:model.defer="{{ $model }}"
            placeholder="{{ $placeholder }}"
            {{ $autofocus ? 'autofocus' : '' }}
            class="form-control {{ $error ? 'is-invalid' : '' }}"
            @if($onlyChars)
                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
            @endif
            @if($uppercase)
                oninput="this.value = this.value.toUpperCase()"
            @endif
        >

        @if($error)
            <div class="invalid-feedback d-block">
                {{ $error }}
            </div>
        @endif
    </div>
</div>
