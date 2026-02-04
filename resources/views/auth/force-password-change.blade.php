@extends('components.layouts.app')

@section('content')

    <div class="space-y-4 h-full">

        {{-- =========================
         | BREADCRUMB (UNCHANGED)
         ========================= --}}
        <div class="breadcrumb-bar">
            @include('partials.breadcrumb', [
                'route' => 'password.change',
                'title' => 'Change Password',
                'mode'  => 'Security'
            ])
        </div>

        {{-- =========================
         | HEADER (UNCHANGED)
         ========================= --}}
        <div>
            <h1 class="font-normal text-black-400">
                Change Password
                <span class="text-black-350 mx-1">››</span>
                <span class="text-black-350">Mandatory</span>
            </h1>
        </div>

        {{-- =========================
         | LOCK BACKGROUND
         ========================= --}}
        @if(auth()->user()?->force_password_change)
            <div class="fixed inset-0 bg-black/40 backdrop-blur-[1px] z-[9998]"></div>
        @endif

        {{-- =========================
         | FOREGROUND CARD
         ========================= --}}
        <div class="relative z-[9999] flex items-center justify-center min-h-[65vh] px-4">

            <div class="w-full max-w-md">

                <div class="admin-card shadow-lg border border-gray-200"
                     x-data="passwordForm()"
                     x-init="mounted = true">

                    <h2 class="text-lg font-semibold mb-1">
                        You must change your password before continuing
                    </h2>

                    <p class="text-sm text-gray-500 mb-4">
                        For security reasons, please set a new password.
                    </p>

                    <form method="POST" action="{{ route('force-password.update') }}" class="space-y-4">
                        @csrf

                        {{-- Password --}}
                        <div>
                            <label class="required text-sm">New Password</label>

                            <div class="relative">
                                <input
                                    :type="show ? 'text' : 'password'"
                                    x-model="password"
                                    name="password"
                                    class="form-control pr-10"
                                    required
                                >

                                <button type="button"
                                        @click="show = !show"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 text-sm">
                                    👁
                                </button>
                            </div>

                            {{-- Strength bar --}}
                            <div class="mt-2">
                                <div class="h-[3px] bg-gray-200 rounded overflow-hidden">
                                    <div class="h-full transition-all duration-300"
                                         :class="color"
                                         :style="`width:${strength}%`"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" x-text="label"></p>
                            </div>
                        </div>

                        {{-- Confirm --}}
                        <div>
                            <label class="required text-sm">Confirm Password</label>

                            <input
                                :type="show ? 'text' : 'password'"
                                x-model="confirm"
                                name="password_confirmation"
                                class="form-control"
                                required
                            >

                            <p x-show="confirm.length"
                               class="text-xs mt-1"
                               :class="match ? 'text-green-600' : 'text-red-500'"
                               x-text="match ? 'Passwords match' : 'Passwords do not match'"></p>
                        </div>

                        {{-- Button --}}
                        <button
                            type="submit"
                            class="btn btn-white w-full disabled:cursor-not-allowed"
                            :disabled="!mounted || !valid"
                            disabled>
                            Update Password
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Force Alpine for this page (guaranteed) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        function passwordForm() {
            return {
                password: '',
                confirm: '',
                show: false,
                mounted: false,

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
                    return ['Weak', 'Fair', 'Good', 'Strong'][Math.min(3, Math.floor(this.strength / 25))];
                },

                get color() {
                    return ['bg-red-500','bg-yellow-500','bg-blue-500','bg-green-600'][Math.min(3, Math.floor(this.strength / 25))];
                },

                get valid() {
                    return this.match && this.strength >= 50;
                }
            }
        }
    </script>

@endsection
