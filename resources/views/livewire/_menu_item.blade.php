@php
    $hasChildren = !empty($item['children']);

    // 🔑 SINGLE SOURCE OF TRUTH
    $isActive = $item['active'] ?? false;

    // Detect if any child is active
    $childActive = false;
    if ($hasChildren) {
        foreach ($item['children'] as $child) {
            if (($child['active'] ?? false) === true) {
                $childActive = true;
                break;
            }
        }
    }

    // 🔥 AUTO-OPEN LOGIC (unchanged)
    $isOpen = $hasChildren && ($isActive || $childActive);

    $openClass = $isOpen ? 'open' : '';

    // ✅ ACTIVE ONLY FOR LEAF
    $activeClass = (!$hasChildren && $isActive)
        ? 'bg-zinc-200/70 dark:bg-white/15 text-zinc-900 dark:text-white'
        : '';

    // Icon
    $iconHtml = '';
    if (!empty($item['icon'])) {
        $safeIcon = e($item['icon']);
        $iconHtml = '<div class="relative">
            <i class="fa-solid fa-' . $safeIcon . ' shrink-0"
               style="width:1.5rem;text-align:center;font-size:1.15rem;"></i>
        </div>';
    }

    // 🔥 BASE STYLE (unchanged)
    $baseAnchorClasses = 'h-10 lg:h-8 flex items-center gap-3 rounded-lg px-3 text-sm
        text-zinc-500 dark:text-white/80
        hover:text-zinc-800 dark:hover:text-white
        hover:bg-zinc-800/5 dark:hover:bg-white/[7%]';
@endphp

<li class="nav-item {{ $openClass }}" style="list-style:none;">
    @if($hasChildren)
        {{-- Parent --}}
        <a href="javascript:void(0)"
           class="{{ $baseAnchorClasses }} dropdown-toggle"
           role="button"
           aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
            {!! $iconHtml !!}

            {{-- 🔥 FIX: NO WRAP + ELLIPSIS --}}
            <span
                class="flex-1 font-medium truncate whitespace-nowrap overflow-hidden"
                title="{{ $item['title'] }}">
                {{ $item['title'] }}
            </span>

            <i class="fa fa-chevron-down text-xs opacity-60"></i>
        </a>

        <ul class="submenu">
            @foreach($item['children'] as $child)
                @include('livewire._menu_item', ['item' => $child])
            @endforeach
        </ul>
    @else
        {{-- ✅ LEAF --}}
        <a href="{{ $item['href'] ?? '#' }}"
           class="{{ $baseAnchorClasses }} {{ $activeClass }}"
           wire:navigate>
            {!! $iconHtml !!}

            {{-- 🔥 FIX: NO WRAP + ELLIPSIS --}}
            <span
                class="flex-1 font-medium truncate whitespace-nowrap overflow-hidden"
                title="{{ $item['title'] }}">
                {{ $item['title'] }}
            </span>
        </a>
    @endif
</li>
