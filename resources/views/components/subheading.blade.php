<div class="flex items-center justify-between mb-3">

    {{-- LEFT: TITLE --}}
    <div>
        <h1 class="text-lg font-semibold text-black-800">
            {{ $title }}
        </h1>

        @isset($subtitle)
            <p class="text-sm text-black-500">
                {{ $subtitle }}
            </p>
        @endisset
    </div>

    {{-- RIGHT: ACTIONS --}}
    @isset($actions)
        <div class="flex items-center gap-2">
            {{ $actions }}
        </div>
    @endisset

</div>
