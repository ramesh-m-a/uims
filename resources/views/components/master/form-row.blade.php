@props([
    'label',
    'name',
    'required' => false,
])

@php
    $error = $errors->first($name);
@endphp

<div class="mb-4">
    <div class="grid grid-cols-12 items-center gap-4">

        {{-- LABEL --}}
        <label class="col-span-3 text-right font-medium">
            {{ $label }}
        </label>

        {{-- CONTROL --}}
        <div class="col-span-9">
            {{ $slot }}

            @if($error)
                <div class="form-error">
                    {{ $error }}
                </div>
            @endif
        </div>

    </div>
</div>
