<div class="basic-tab ace-scope bg-gray-50 border rounded-lg p-6 space-y-6">

    {{-- =========================
     | PERMANENT ADDRESS
     ========================= --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-3 bg-gray-200 border-b">
            <h3 class="text-sm font-semibold text-blue-700 tracking-wide">
                PERMANENT ADDRESS
            </h3>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Address Line 1 --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Address Line 1 *</label>
                    <input wire:model.live="address.permanent.address_1"
                           class="form-input w-full" style="text-transform: uppercase;">
                    @error('address.permanent.address_1')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address Line 2 --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Address Line 2 *</label>
                    <input wire:model.live="address.permanent.address_2"
                           class="form-input w-full" style="text-transform: uppercase;">
                    @error('address.permanent.address_2')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address Line 3 --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Address Line 3</label>
                    <input wire:model.live="address.permanent.address_3"
                           class="form-input w-full" style="text-transform: uppercase;">
                    @error('address.permanent.address_3')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">City</label>
                    <input wire:model.live="address.permanent.district"
                           class="form-input w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        State
                    </label>
                    <select wire:model.live="address.permanent.state_id"
                            class="form-select w-full">
                        <option value="">SELECT STATE</option>

                        @foreach($states as $state)
                            <option value="{{ $state['id'] }}">
                                {{ strtoupper($state['mas_state_name']) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Pin Code
                    </label>
                    <input wire:model.live="address.permanent.pincode"
                           class="form-input w-full">
                </div>

            </div>
        </div>
    </div>

    {{-- =========================
     | CORRESPONDENCE ADDRESS
     ========================= --}}
    <div>
        <label class="flex items-center gap-2 mb-4 text-sm">
            <input
                type="checkbox"
                wire:model.live="address.same_address"
            >
            <span class="text-orange-600 font-medium">
                Same as Above
            </span>
        </label>
    </div>

    <div class="bg-white border rounded-lg overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-3 bg-gray-200 border-b">
            <h3 class="text-sm font-semibold text-blue-700 tracking-wide">
                CORRESPONDENCE ADDRESS
            </h3>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Address Line 1 --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Address Line 1 *</label>
                    <input wire:model.live="address.temporary.address_1"
                           @disabled($address['same_address'])
                           class="form-input w-full
                              {{ $address['same_address'] ? 'bg-gray-100 cursor-not-allowed' : '' }}" style="text-transform: uppercase;">
                    @error('address.permanent.address_1')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address Line 2 --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Address Line 2 *</label>
                    <input wire:model.live="address.temporary.address_2"
                           @disabled($address['same_address'])
                           class="form-input w-full
                              {{ $address['same_address'] ? 'bg-gray-100 cursor-not-allowed' : '' }}" style="text-transform: uppercase;">
                    @error('address.permanent.address_2')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address Line 3 --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Address Line 3</label>
                    <input wire:model.live="address.temporary.address_3"
                           @disabled($address['same_address'])
                           class="form-input w-full
                              {{ $address['same_address'] ? 'bg-gray-100 cursor-not-allowed' : '' }}" style="text-transform: uppercase;">
                    @error('address.permanent.address_3')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">City</label>
                    <input wire:model.live="address.temporary.district"
                           @disabled($address['same_address'])
                           class="form-input w-full
                              {{ $address['same_address'] ? 'bg-gray-100 cursor-not-allowed' : '' }}" style="text-transform: uppercase;">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        State
                    </label>
                    <select wire:model.live="address.temporary.state_id"
                            @disabled($address['same_address'])
                            class="form-select w-full
           {{ $address['same_address'] ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        <option value="">SELECT STATE</option>

                        @foreach($states as $state)
                            <option value="{{ $state['id'] }}">
                                {{ strtoupper($state['mas_state_name']) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Pin Code
                    </label>
                    <input wire:model.live="address.temporary.pincode"
                           @disabled($address['same_address'])
                           class="form-input w-full
                              {{ $address['same_address'] ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                </div>

            </div>
        </div>
    </div>

    {{-- =========================
     | ACTION BAR
     ========================= --}}
    <div class="flex justify-end pt-6">
        <button
            wire:click="save"
            class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
        >
            Save & Continue →
        </button>
    </div>

</div>
