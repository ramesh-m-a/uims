<div class="flex items-center gap-2 text-slate-600">

    {{-- Home --}}
    <a href="{{ url('/') }}" class="text-indigo-600" title="Home">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
             fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 12l8.954-8.955c.44-.439 1.152-.439
                 1.591 0L21.75 12M4.5 9.75v10.125
                 c0 .621.504 1.125 1.125 1.125H9.75
                 v-4.875c0-.621.504-1.125
                 1.125-1.125h2.25c.621 0
                 1.125.504 1.125 1.125V21
                 h4.125c.621 0 1.125-.504
                 1.125-1.125V9.75M8.25 21h8.25"/>
        </svg>

        here
    </a>

    @foreach($items as $item)
        <span>›</span>

        @if(isset($item['url']))
            <a href="{{ $item['url'] }}" class="text-indigo-600">
                {{ $item['label'] }}
            </a>
        @else
            <span class="text-gray-500">
                {{ $item['label'] }}
            </span>
        @endif
    @endforeach
</div>
