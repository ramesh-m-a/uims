<div class="max-w-md mx-auto p-6 bg-white rounded-xl shadow space-y-4">

{{--    @if(!$cardGenerated)
        <div class="text-center text-gray-500">ID Card not available</div>
    @else
        <div class="border rounded-xl p-6 text-center space-y-2">

            --}}{{-- Logo --}}{{--
            <img src="{{ $logoBase64 }}" class="mx-auto w-16 mb-2">

            --}}{{-- University --}}{{--
            <h2 class="font-bold text-sm uppercase">{{ $university }}</h2>
            <p class="text-xs uppercase">{{ $stream }}</p>
            <p class="text-xs">{{ $college }}</p>

            --}}{{-- Photo --}}{{--
            <img
                src="{{ asset('storage/' . Auth::user()->photo_path) }}"
                alt="Photo"
                class="w-40 h-48 mx-auto rounded-full border my-3 object-cover">

            --}}{{-- Name & details --}}{{--
            <h3 class="font-bold uppercase">{{ $name }}</h3>
            <p class="text-sm">{{ $designation }}</p>
            <p class="text-sm">{{ $department }}</p>

            <p class="font-semibold mt-1">RGUHS TIN {{ $tin }}</p>

            --}}{{-- QR --}}{{--
            <img src="{{ $qrBase64 }}" class="w-32 mx-auto mt-3">

        </div>
    @endif

  <button wire:click="downloadPdf"
                class="px-4 py-2 bg-black text-white rounded w-full">
            Download PDF
        </button>
</div>--}}

<div class="max-w-md mx-auto p-6 bg-white rounded-xl shadow space-y-4">

    @if(!$cardGenerated)
        <div class="text-center text-gray-500">ID Card not available</div>
    @else
        <div class="border rounded-xl p-6 text-center">
            <img src="{{ asset('images/RGUHS-logo-AA.png') }}" class="mx-auto w-16 mb-3">

            <h2 class="font-bold text-sm uppercase">{{ $university }}</h2>
            <p class="text-xs uppercase">{{ $stream }}</p>
            <p class="text-xs">{{ $college }}</p>

            {{-- Photo --}}
            <img
                src="{{ asset('storage/' . Auth::user()->photo_path) }}"
                alt="Photo"
                class="w-40 h-48 mx-auto rounded-full border my-3 object-cover">

            <h3 class="font-bold uppercase">{{ $name }}</h3>
            <p class="text-sm">{{ $designation }}</p>
            <p class="text-sm">{{ $department }}</p>

            <p class="font-semibold text-brown-600 mt-1">RGUHS TIN {{ $tin }}</p>

            <img src="{{ $qrBase64 }}" class="w-32 mx-auto mt-3">
        </div>
    @endif

    <button wire:click="downloadPdf" class="px-4 py-2 bg-black text-white rounded w-full">
        Download PDF
    </button>
</div>
