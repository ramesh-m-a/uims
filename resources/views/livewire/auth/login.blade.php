<x-layouts.auth>

    <div class="flex flex-col gap-6">

        {{-- ================= HEADER ================= --}}
        <div class="text-center">
            <h2 class="text-lg font-semibold text-gray-900">
                Login
            </h2>

            <p class="text-sm text-blue-400 mt-1">
                <i class="fa fa-info-circle text-red-400"></i>
                Enter your email as registered in RGUHS Portal
            </p>
        </div>

        {{-- ================= SESSION STATUS ================= --}}
        <x-auth-session-status
            class="text-center"
            :status="session('Status')"
        />

        {{-- ================= LOGIN FORM ================= --}}
        <form method="POST"
              action="{{ route('login.store') }}"
              class="flex flex-col gap-5">

            @csrf

            {{-- ================= EMAIL ================= --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email address
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="form-control w-full rounded-lg border border-gray-300 bg-blue-50 px-4 py-2.5
                           focus:outline-none focus:ring-2 focus:ring-black"
                >

                @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ================= PASSWORD ================= --}}
            <div x-data="{ show: false }">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-gray-700">
                        Password
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-sky-600">
                            Forgot your password ?
                        </a>
                    @endif
                </div>

                <div class="relative">
                    <input
                        :type="show ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="form-control w-full rounded-lg border border-gray-300 bg-blue-50
                   px-4 py-2.5 pr-10
                   focus:outline-none focus:ring-2 focus:ring-black"
                    >

                    <button
                        type="button"
                        @click="show = !show"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-500
                   hover:text-black"
                    >
                        <i class="fa" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>

                @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ================= CAPTCHA ================= --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Captcha
                </label>

                <div class="flex items-center gap-4 mb-2">
        <span
            class="text-sky-600 font-semibold tracking-widest text-sm select-none"
        >
            {{ $captcha }}
        </span>

                    <a href="{{ route('captcha.refresh') }}"
                       class="text-sm text-gray-500">
                        Refresh
                    </a>
                </div>

                <input
                    type="text"
                    name="captcha"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="6"
                    placeholder="Enter Captcha"
                    required
                    class="form-control w-full rounded-lg border border-gray-300 px-4 py-2.5
               focus:outline-none focus:ring-2 focus:ring-black"
                >
                @error('captcha')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ================= REMEMBER ME ================= --}}
            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300"
                >
                <span class="text-sm text-gray-700">
                    Remember me
                </span>
            </div>

            {{-- ================= LOGIN BUTTON ================= --}}
            <button
                type="submit"
                class="w-full rounded-lg bg-gray-400 text-white py-3 font-medium
                       hover:bg-gray-500 transition"
            >
                Log in
            </button>

            {{-- ================= REGISTER + FORGOT EMAIL ================= --}}
            <div class="flex items-center justify-center gap-10 text-sm pt-2">
                <a href="{{ route('register') }}"
                   class="font-medium text-red-700">
                    Register Here
                </a>

                <a href="#"
                   class="text-yellow-700 font-medium">
                    Forgot your Email Address ?
                </a>
            </div>

        </form>

    </div>

</x-layouts.auth>
