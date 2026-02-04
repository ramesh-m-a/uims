@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
            {{-- Mobile --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-400">Previous</span>
            @else
                <button wire:click="previousPage" class="px-3 py-2 text-sm text-blue-600">
                    Previous
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-2 text-sm text-blue-600">
                    Next
                </button>
            @else
                <span class="px-3 py-2 text-sm text-gray-400">Next</span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ $paginator->firstItem() ?? 1 }}</span>
                to
                <span class="font-medium">{{ $paginator->lastItem() ?? $paginator->count() }}</span>
                of
                <span class="font-medium">{{ $paginator->total() }}</span>
                results
            </div>

            <div>
                <span class="inline-flex -space-x-px rounded-md shadow-sm">
                    {{-- Previous --}}
                    @if ($paginator->onFirstPage())
                        <span class="px-3 py-2 text-gray-400 border">‹</span>
                    @else
                        <button wire:click="previousPage" class="px-3 py-2 border text-blue-600">‹</button>
                    @endif

                    {{-- Always show page 1 --}}
                    <span class="px-3 py-2 border bg-blue-50 text-blue-600">1</span>

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                        <button wire:click="nextPage" class="px-3 py-2 border text-blue-600">›</button>
                    @else
                        <span class="px-3 py-2 text-gray-400 border">›</span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
