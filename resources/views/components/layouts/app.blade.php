{{-- resources/views/components/layouts/app.blade.php --}}
@php
    // small helper so we can detect running as component vs plain view
    $isComponent = isset($slot) && trim((string)$slot) !== '';
@endphp

<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{-- If used as a component: render $slot.
             If used via @extends(): render @yield('content'). --}}
        @if ($isComponent)
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </flux:main>
</x-layouts.app.sidebar>

{{-- Ensure stacks are available for child views that push styles/scripts --}}
@stack('styles')
@stack('scripts')

<x-ui.toast />
