<div class="basic-tab ace-scope bg-gray-50 border rounded-lg p-6 space-y-6">

    {{-- ROW 1 : DOB / GENDER / AGE --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div>
            <label class="block text-sm font-medium mb-1">Date of Birth</label>
            <input type="date"
                   placeholder="Date of Birth"
                   max="{{ $this->dobMax }}"
                   wire:model.live="basic.dob"
                   class="form-input w-full">
            @error('basic.dob') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Gender</label>
            <select wire:model.live="basic.gender_id" class="form-select w-full">
                <option value="">CHOOSE GENDER</option>
                @foreach($genders as $g)
                    <option value="{{ $g->id }}">{{ $g->mas_gender_name }}</option>
                @endforeach
            </select>
            @error('basic.gender_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Age</label>
            <input type="text"
                   readonly
                   value="{{ $this->calculatedAge }}"
                   class="form-input w-full bg-gray-100 cursor-not-allowed">
        </div>
    </div>

    {{-- ROW 2 : FATHER / SPOUSE / RELIGION / CATEGORY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Father / Spouse --}}
        <div>
            <label class="block text-sm font-medium mb-1">Father / Spouse Name</label>
            <input
                wire:model.live="basic.father_name"
                class="form-input w-full" style="text-transform: uppercase;">
            @error('basic.father_name')
            <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Religion --}}
        <div>
            <label class="block text-sm font-medium mb-1">Religion</label>
            <select wire:model.live="basic.religion_id" class="form-select w-full">
                <option value="">CHOOSE RELIGION</option>
                @foreach($religions as $r)
                    <option value="{{ $r->id }}">{{ $r->mas_religion_name }}</option>
                @endforeach
            </select>
            @error('basic.religion_id')
            <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Category --}}
        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select wire:model.live="basic.category_id" class="form-select w-full">
                <option value="">CHOOSE CATEGORY</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->mas_category_name }}</option>
                @endforeach
            </select>
            @error('basic.category_id')
            <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

    </div>


    {{-- ROW 3 : DESIGNATION / DEPARTMENT / ADMIN ROLE (PRODUCTION PARITY) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div>
            <label class="block text-sm font-medium mb-1">Designation</label>
            <input type="text"
                   readonly
                   value="{{ $designationName }}"
                   class="form-input w-full bg-gray-100 cursor-not-allowed" style="text-transform: uppercase;">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Department</label>
            <select wire:model.live="selectedDepartment"
                    class="form-select w-full">
                <option value="">CHOOSE DEPARTMENT</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">
                        {{ $dept->mas_department_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Administrative Role
            </label>
            <select wire:model.live="selectedAdminRole"
                    class="form-select w-full">
                <option value="">CHOOSE ADMINISTRATIVE ROLE</option>
                @foreach($adminRoles as $role)
                    <option value="{{ $role->id }}">
                        {{ $role->mas_admin_role_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ACTION BAR --}}
    <div class="flex justify-end pt-6">
        <button
            wire:click="save"
            @disabled(! $this->isComplete)
            class="px-6 py-2 rounded-lg text-white bg-blue-600
                   {{ ! $this->isComplete ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700' }}">
            Save & Continue →
        </button>
    </div>

</div>
