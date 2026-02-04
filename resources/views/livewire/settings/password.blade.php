<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full" x-data="passwordForm()">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Update password')"
        :subheading="__('Ensure your account is using a long, random password to stay secure')"
    >
        <form wire:submit.prevent="updatePassword" class="mt-6 space-y-6">

            {{-- Current password --}}
            <flux:input
                wire:model="current_password"
                :label="__('Current password')"
                x-bind:type="show ? 'text' : 'password'"
                required
                autocomplete="current-password"
            />

            {{-- New password --}}
            <div>
                <flux:input
                    wire:model="password"
                    x-model="password"
                    :label="__('New password')"
                    x-bind:type="show ? 'text' : 'password'"
                    required
                    autocomplete="new-password"
                />

                {{-- Strength --}}
                <div class="mt-2">
                    <div class="h-[3px] bg-gray-200 rounded overflow-hidden">
                        <div class="h-full transition-all duration-300"
                             x-bind:class="color"
                             x-bind:style="`width:${strength}%`"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" x-text="label"></p>
                </div>
            </div>

            {{-- Confirm --}}
            <div>
                <flux:input
                    wire:model="password_confirmation"
                    x-model="confirm"
                    :label="__('Confirm Password')"
                    x-bind:type="show ? 'text' : 'password'"
                    required
                    autocomplete="new-password"
                />

                <p x-show="confirm.length"
                   class="text-xs mt-1"
                   x-bind:class="match ? 'text-green-600' : 'text-red-500'"
                   x-text="match ? 'Passwords match' : 'Passwords do not match'">
                </p>
            </div>

            {{-- Toggle --}}
            <div class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer select-none"
                 @click="show = !show">
                <span x-text="show ? '🙈 Hide passwords' : '👁 Show passwords'"></span>
            </div>

            {{-- Actions --}}
            {{-- Actions --}}
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-zinc-900 text-white px-4 py-2 text-sm font-medium
                   hover:bg-zinc-800 transition
                   disabled:opacity-40 disabled:cursor-not-allowed"
                        x-bind:disabled="!valid"
                        wire:loading.attr="disabled"
                        wire:target="updatePassword"
                    >
            <span wire:loading.remove wire:target="updatePassword">
                Save
            </span>

                        <span wire:loading wire:target="updatePassword">
                Saving...
            </span>
                    </button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>

        </form>
    </x-settings.layout>

    {{-- ONE Alpine definition ONLY --}}
    <script>
        function passwordForm() {
            return {
                password: '',
                confirm: '',
                show: false,

                get match() {
                    return this.password && this.password === this.confirm;
                },

                get strength() {
                    let s = 0;
                    if (this.password.length >= 8) s += 25;
                    if (/[A-Z]/.test(this.password)) s += 25;
                    if (/[0-9]/.test(this.password)) s += 25;
                    if (/[^A-Za-z0-9]/.test(this.password)) s += 25;
                    return s;
                },

                get label() {
                    return ['Weak', 'Fair', 'Good', 'Strong']
                        [Math.min(3, Math.floor(this.strength / 25))];
                },

                get color() {
                    return ['bg-red-500','bg-yellow-500','bg-blue-500','bg-green-600']
                        [Math.min(3, Math.floor(this.strength / 25))];
                },

                get valid() {
                    return this.match && this.strength >= 50;
                }
            }
        }
    </script>
</section>
