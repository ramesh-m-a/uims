@props([
    'title',
    'subtitle' => null,
    'subtitleClass' => 'text-gray-500', // default color
])

<div class="flex justify-between items-center">
    <h1 class="font-semibold text-blue-700 text-lg">
        {{ $title }}

        @if($subtitle)
            <span class="mx-1 text-gray-500">››</span>
            <span class="{{ $subtitleClass }}">
                {{ $subtitle }}
            </span>
        @endif
    </h1>

    @if(trim($slot ?? ''))
        <div>
            {{ $slot }}
        </div>
    @endif
</div>
