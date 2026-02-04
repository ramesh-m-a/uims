@props(['onSave', 'onCancel'])

<div
    x-data
    x-on:keydown.enter.prevent="$wire.{{ $onSave }}()"
    x-on:keydown.escape.prevent="$wire.{{ $onCancel }}()"
    tabindex="0"
    class="admin-loader"
>
    <div class="admin-loader-box w-[600px]">
        {{ $slot }}
    </div>
</div>
