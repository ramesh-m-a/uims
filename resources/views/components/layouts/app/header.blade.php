<flux:header class="lg:hidden">
    <flux:sidebar.toggle icon="bars-2" inset="left" />

    <flux:spacer />

    <flux:dropdown position="top" align="end">
        {{-- ✅ Flux-supported trigger --}}
        <flux:profile
            :src="auth()->user()->avatar_url"
            :initials="auth()->user()->initials()"
            icon:trailing="chevron-down"
        />

        <flux:menu>
            <div class="flex items-center gap-3 px-3 py-2">
                <img
                    src="{{ auth()->user()->avatar_url }}"
                    class="h-10 w-10 rounded-full object-cover"
                    alt="User Avatar"
                />
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
                wire:navigate
            >
                {{ __('Settings') }}
            </flux:menu.item>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                >
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>
