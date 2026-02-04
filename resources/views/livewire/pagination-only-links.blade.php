@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <span class="inline-flex items-center gap-1">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-2 py-2 text-gray-400 cursor-not-allowed">
                    ‹
                </span>
            @else
                <button
                    wire:click="previousPage"
                    wire:loading.attr="disabled"
                    class="px-2 py-2 hover:text-blue-600"
                >
                    ‹
                </button>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-2 text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <span class="px-3 py-2 font-semibold text-blue-600">
                                {{ $page }}
                            </span>
                        @else
                            <button
                                wire:click="gotoPage({{ $page }})"
                                class="px-3 py-2 hover:text-blue-600"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <button
                    wire:click="nextPage"
                    wire:loading.attr="disabled"
                    class="px-2 py-2 hover:text-blue-600"
                >
                    ›
                </button>
            @else
                <span class="px-2 py-2 text-gray-400 cursor-not-allowed">
                    ›
                </span>
            @endif
        </span>
    </nav>
@endif
