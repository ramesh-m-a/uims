<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-transition.opacity
    class="fixed inset-0 z-50 flex justify-end bg-black/40"
>

    <div class="w-full max-w-lg bg-white h-full shadow-xl p-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold">Edit Examiner Date</h2>
            <button @click="open=false" class="text-gray-500 text-xl">×</button>
        </div>

        <div class="space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-500">Current From</label>
                    <input value="{{ $current_from }}" disabled class="w-full bg-gray-100 border rounded px-3 py-2">
                </div>

                <div>
                    <label class="text-xs text-gray-500">Current To</label>
                    <input value="{{ $current_to }}" disabled class="w-full bg-gray-100 border rounded px-3 py-2">
                </div>
            </div>

            <hr>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">New From Date</label>
                    <input type="date" wire:model="from_date" class="w-full border rounded px-3 py-2">
                    @error('from_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium">New To Date</label>
                    <input type="date" wire:model="to_date" class="w-full border rounded px-3 py-2">
                    @error('to_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 flex justify-end">
                <button wire:click="save" class="bg-green-600 text-white px-5 py-2 rounded">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
