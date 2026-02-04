<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')

    {{-- 🔥 REQUIRED FOR LIVEWIRE --}}
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-50 antialiased">

<div class="min-h-screen flex items-center justify-center px-4 py-8">

    {{-- MAIN CARD --}}
    <div class="w-full max-w-6xl bg-white rounded-xl shadow-lg overflow-hidden">

        {{-- GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2">

            {{-- ================= LEFT : BRANDING ================= --}}
            <div class="p-8 md:p-10 flex flex-col justify-center gap-6">

                {{-- BRANDING (CENTERED BLOCK) --}}
                <div class="flex flex-col items-center text-center gap-4">

                    {{-- LOGOS --}}
                    <div class="flex items-center justify-center gap-16">
                        <img
                            src="{{ asset('images/KA_logo.png') }}"
                            alt="KA Logo"
                            class="h-24 w-auto object-contain"
                        >

                        <img
                            src="{{ asset('images/RGUHS-logo.png') }}"
                            alt="RGUHS Logo"
                            class="h-24 w-auto object-contain"
                        >
                    </div>

                    {{-- TEXT --}}
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800 leading-snug">
                            Rajiv Gandhi University of Health Sciences (RGUHS)
                        </h1>

                        <p class="text-blue-600 mt-1">
                            University Information Management System (UIMS)
                        </p>
                    </div>

                </div>

                <hr class="border-gray-200">

                {{-- SUPPORT --}}
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2 text-red-600">
                        <i class="fa fa-question-circle"></i>
                        <span>Frequently Asked Questions (FAQ)</span>
                    </div>

                    <div class="flex items-center gap-2 text-red-600">
                        <i class="fa fa-envelope"></i>
                        <span>examsupport@rguhs.ac.in</span>
                    </div>
                </div>

            </div>

            {{-- ================= RIGHT : AUTH SLOT ================= --}}
            <div class="p-8 md:p-10 flex items-center justify-center bg-gray-100">
                <div class="w-full max-w-sm">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </div>
</div>

{{-- 🔥 REQUIRED FOR LIVEWIRE --}}
@livewireScripts

{{-- Flux can stay --}}
@fluxScripts

</body>
</html>
