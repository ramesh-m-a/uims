@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white border rounded-lg p-4 shadow-sm']) }}>
    @if ($title)
        <h3 class="text-sm font-semibold mb-3">
            {{ $title }}
        </h3>
    @endif

    {{ $slot }}
</div>
