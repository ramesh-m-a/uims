@props([
    'name',
    'group' => 'action',
    'size' => 'md',
    'class' => ''
])

@php
    $path = resource_path("svg/{$group}/{$name}.svg");
@endphp

@if(file_exists($path))
    <span class="uims-icon uims-icon-{{ $size }} {{ $class }}">
        {!! file_get_contents($path) !!}
    </span>
@endif
