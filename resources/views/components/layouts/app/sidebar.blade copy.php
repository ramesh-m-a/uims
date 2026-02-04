<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @include('partials.head')

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    @livewireStyles
    @stack('styles')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

{{-- ================= SIDEBAR ================= --}}
<flux:sidebar sticky stashable
              class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">

    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <a href="{{ route('dashboard') }}"
       class="me-5 flex items-center space-x-2 rtl:space-x-reverse"
       wire:navigate>
        <x-app-logo />
    </a>

    <livewire:menu-sidebar />

    <flux:spacer />

    <flux:navlist variant="outline">
        <flux:navlist.item
            icon="folder-git-2"
            href="https://github.com/laravel/livewire-starter-kit"
            target="_blank">
            {{ __('Repository') }}
        </flux:navlist.item>

        <flux:navlist.item
            icon="book-open-text"
            href="https://laravel.com/docs/starter-kits#livewire"
            target="_blank">
            {{ __('Documentation') }}
        </flux:navlist.item>
    </flux:navlist>

    {{-- ===== DESKTOP SIDEBAR USER (PHOTO GUARANTEED) ===== --}}
    <flux:dropdown class="hidden lg:block" position="bottom" align="start">

        {{-- FIRST CHILD = TRIGGER (Flux-compatible) --}}
        <div
            x-data="{
        src: '{{ auth()->user()->avatar_url }}',
        refresh() {
            this.src = '{{ auth()->user()->avatar_url }}' + '?v=' + Date.now();
        }
    }"
            x-on:profile-photo-updated.window="refresh()"
        >
            <button
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                data-test="sidebar-menu-button"
                type="button"
            >
                <livewire:sidebar-user-avatar />

                <span class="flex-1 truncate text-left text-sm font-medium">
            {{ auth()->user()->name }}
        </span>

                <flux:icon name="chevrons-up-down" />
            </button>
        </div>


        <flux:menu class="w-[220px]">
            <div class="flex items-center gap-3 px-3 py-2">
                <livewire:sidebar-user-avatar />
                <div class="grid text-sm leading-tight">
                    <span class="font-semibold truncate">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="text-xs text-zinc-500 truncate">
                        {{ auth()->user()->email }}
                    </span>
                </div>
            </div>

            <flux:menu.separator />

            <flux:menu.item
                :href="route('profile.edit')"
                icon="cog"
                wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>

</flux:sidebar>

{{-- ================= MOBILE HEADER ================= --}}
<flux:header class="lg:hidden">
    <flux:sidebar.toggle icon="bars-2" inset="left" />
    <flux:spacer />

    <flux:dropdown position="top" align="end">

        {{-- ✅ BUTTON = REQUIRED FOR MOBILE TAP --}}
        <div
            x-data="{
        src: '{{ auth()->user()->avatar_url }}',
        refresh() {
            this.src = '{{ auth()->user()->avatar_url }}' + '?v=' + Date.now();
        }
    }"
            x-on:profile-photo-updated.window="refresh()"
        >
            <button type="button" class="flex items-center">
                <livewire:sidebar-user-avatar />
            </button>
        </div>


        <flux:menu>
            <div class="flex items-center gap-3 px-3 py-2">
                <livewire:sidebar-user-avatar />
                <div class="grid text-sm leading-tight">
                    <span class="font-semibold truncate">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="text-xs text-zinc-500 truncate">
                        {{ auth()->user()->email }}
                    </span>
                </div>
            </div>

            <flux:menu.separator />

            <flux:menu.item
                :href="route('profile.edit')"
                icon="cog"
                wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>

{{ $slot }}

@livewireScripts
@fluxScripts
@stack('scripts')

</body>
</html>
