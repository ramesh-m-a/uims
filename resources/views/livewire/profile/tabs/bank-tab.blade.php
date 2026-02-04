<div class="basic-tab ace-scope bg-gray-50 border rounded-lg p-6 space-y-6">

    {{-- ================= IDENTITY INFORMATION ================= --}}
    <div class="bg-white border rounded-lg overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-3 bg-gray-200 border-b">
            <h3 class="text-sm font-semibold text-blue-700 tracking-wide">
                IDENTITY INFORMATION
            </h3>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- PAN NUMBER --}}
                <div>
                    <label class="block text-sm font-medium mb-1">PAN Number *</label>
                    <input type="text"
                           wire:model.defer="bank.identity.pan_number"
                           oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g,'').toUpperCase()"
                           maxlength="10"
                           class="form-input w-full">
                    @error('bank.identity.pan_number')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PAN NAME --}}
                <div>
                    <label class="block text-sm font-medium mb-1">PAN Name *</label>
                    <input type="text"
                           wire:model.defer="bank.identity.pan_name"
                           oninput="this.value = this.value.replace(/[^A-Za-z\s]/g,'').toUpperCase()"
                           class="form-input w-full">
                    @error('bank.identity.pan_name')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- AADHAAR --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Aadhaar Number *</label>
                    <input type="text"
                           wire:model.defer="bank.identity.aadhar_number"
                           maxlength="14"
                           placeholder="1234-5678-9012"
                           oninput="
                               let v=this.value.replace(/[^0-9]/g,'').slice(0,12);
                               this.value=v.replace(/(\d{4})(?=\d)/g,'$1-');
                           "
                           class="form-input w-full">
                    @error('bank.identity.aadhar_number')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EPF --}}
                <div>
                    <label class="block text-sm font-medium mb-1">EPF Number</label>
                    <input type="text"
                           wire:model.defer="bank.identity.epf_number"
                           oninput="this.value=this.value.replace(/[^A-Za-z0-9]/g,'').toUpperCase()"
                           class="form-input w-full">
                    @error('bank.identity.epf_number')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>
    </div>

    {{-- ================= SALARY INFORMATION ================= --}}
    <div class="bg-white border rounded-lg overflow-hidden">

        <div class="px-6 py-3 bg-gray-200 border-b">
            <h3 class="text-sm font-semibold text-blue-700 tracking-wide">
                SALARY INFORMATION
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-sm font-medium mb-1">Basic Pay *</label>
                    <input type="text"
                           wire:model.defer="bank.salary.basic_pay"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                           class="form-input w-full">
                    @error('bank.salary.basic_pay')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Salary Mode *</label>
                    <select wire:model.live="bank.salary.salary_mode_id"
                            class="form-select w-full">
                        <option value="">CHOOSE SALARY MODE</option>
                        @foreach($salaryModes as $mode)
                            <option value="{{ $mode->id }}">
                                {{ strtoupper($mode->mas_salary_mode_name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('bank.salary.salary_mode_id')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>
    </div>

    {{-- ================= BANK ACCOUNT DETAILS ================= --}}
    <div class="bg-white border rounded-lg overflow-hidden">

        <div class="px-6 py-3 bg-gray-200 border-b">
            <h3 class="text-sm font-semibold text-blue-700 tracking-wide">
                BANK ACCOUNT DETAILS
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-sm font-medium mb-1">Account Type *</label>
                    <select wire:model.live="bank.account.account_type_id"
                            class="form-select w-full">
                        <option value="">CHOOSE ACCOUNT TYPE</option>
                        @foreach($accountTypes as $type)
                            <option value="{{ $type->id }}">
                                {{ strtoupper($type->mas_account_type_name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('bank.account.account_type_id')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Account Number *</label>
                    <input type="text"
                           wire:model.defer="bank.account.account_number"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                           class="form-input w-full">
                    @error('bank.account.account_number')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Account Holder Name *</label>
                    <input type="text"
                           wire:model.defer="bank.account.account_name"
                           oninput="this.value=this.value.replace(/[^A-Za-z\s]/g,'').toUpperCase()"
                           class="form-input w-full">
                    @error('bank.account.account_name')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">IFS Code *</label>
                    <input type="text"
                           wire:model.live.debounce.500ms="bank.account.ifs_code"
                           oninput="this.value=this.value.replace(/[^A-Za-z0-9]/g,'').toUpperCase()"
                           maxlength="11"
                           class="form-input w-full">
                    @error('bank.account.ifs_code')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Bank Name</label>
                    <input type="text"
                           readonly
                           value="{{ $bank['account']['bank_name'] ?? '' }}"
                           class="form-input w-full bg-gray-100">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Branch Name</label>
                    <input type="text"
                           readonly
                           value="{{ $bank['account']['branch_name'] ?? '' }}"
                           class="form-input w-full bg-gray-100">
                </div>

            </div>
        </div>
    </div>

    {{-- ACTION --}}
    <div class="flex justify-end pt-6">
        <button wire:click="save"
                class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Save & Continue →
        </button>
    </div>

</div>
