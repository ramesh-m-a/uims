@props([
    'title' => '',
])

<div class="space-y-4">

    {{-- Title --}}
    <h1 class="text-xl font-semibold">
        {{ $title ?: $this->masterTitle() }}
    </h1>

    {{-- Slot --}}
    <div>
        {{ $slot }}
    </div>

</div>
