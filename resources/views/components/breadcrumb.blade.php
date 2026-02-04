@props(['items'])

<nav class="text-sm text-gray-600 mb-4" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        @foreach ($items as $index => $item)
            <li class="flex items-center">
                @if (!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="text-blue-600 hover:underline">
                        {{ $item['label'] }}
                    </a>
                    home
                @else
                    <span class="font-medium text-gray-800">
                        {{ $item['label'] }}
                    </span>
                @endif

                @if (! $loop->last)
                    <span class="mx-2 text-gray-400">›</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
