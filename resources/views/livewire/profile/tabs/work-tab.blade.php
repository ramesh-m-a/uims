<div class="basic-tab ace-scope bg-gray-50 border rounded-lg p-6 space-y-6">
   {{-- @php
        dd($work ?? null);
    @endphp--}}
    {{-- ADD BUTTON --}}
    @unless($showForm)
        <div class="flex justify-end">
            <button wire:click="add"
                    class="px-4 py-2 rounded-lg border bg-white hover:bg-gray-100">
                ➕ Click here to Add Work Experience
            </button>
        </div>
    @endunless

    {{-- ADD / EDIT FORM --}}
    @if($showForm)
        <div class="bg-white border rounded-lg p-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Designation --}}
                <div>
                    <label class="block text-sm font-medium text-blue-600 mb-1">
                        Designation <span class="text-red-600">*</span>
                    </label>
                    <select wire:model.defer="form.designation_id"
                            class="form-select w-full">
                        <option value="">CHOOSE DESIGNATION</option>
                        @foreach($designations as $d)
                            <option value="{{ $d['id'] }}">
                                {{ $d['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('form.designation_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Institution --}}
                <div>
                    <label class="block text-sm font-medium text-blue-600 mb-1">
                        Name of the Institution <span class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           wire:model.defer="form.institution"
                           oninput="this.value = this.value.replace(/[^A-Za-z,\s]/g, '')"
                           class="form-input w-full">
                    @error('form.institution')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Appointment --}}
                <div>
                    <label class="block text-sm font-medium text-blue-600 mb-1">
                        Date of Appointment / Promotion
                    </label>
                    <input type="date"
                           wire:model.defer="form.date_of_appointment"
                           class="form-input w-full">
                    @error('form.date_of_appointment')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Joining --}}
                <div>
                    <label class="block text-sm font-medium text-blue-600 mb-1">
                        Date of Joining the Institution
                    </label>
                    <input type="date" class="date-picker"
                           wire:model.defer="form.date_of_joining"
                           class="form-input w-full">
                    @error('form.date_of_joining')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- From --}}
                <div>
                    <label class="block text-sm font-medium text-blue-600 mb-1">
                        From Date
                    </label>
                    <input type="date" class="date-picker"
                           wire:model.defer="form.from_date"
                           class="form-input w-full">
                    @error('form.from_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- To --}}
                <div>
                    <label class="block text-sm font-medium text-blue-600 mb-1">
                        To Date
                    </label>
                    <input type="date"
                           wire:model.defer="form.to_date"
                           @disabled(data_get($form, 'is_current', false))
                           class="form-input w-full {{ data_get($form, 'is_current', false) ? 'bg-gray-100' : '' }}">

                    @error('form.to_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="form.is_current">
                Till Date
            </label>

            <div class="flex justify-center gap-4 pt-4">
                <button wire:click="saveForm"
                        class="px-6 py-2 bg-gray-500 text-white rounded-lg">
                    Save
                </button>

                <button wire:click="cancel"
                        class="px-6 py-2 bg-gray-300 rounded-lg">
                    Cancel
                </button>
            </div>

        </div>
    @endif

    {{-- TABLE / EMPTY STATE --}}
    @unless($showForm)

        @if(count($work) === 0)
            <div class="text-center text-gray-500 py-8">
                No work experience added, please add by clicking on
                <strong>Add Work Experience</strong>.
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center px-6 py-4 border-b bg-gray-200">
                    <h2 class="text-sm font-semibold text-gray-700 tracking-wide uppercase">
                        Work Experience details
                    </h2>

                    <div class="ml-auto text-sm text-gray-700">
                        <span class="font-semibold text-blue-900">Total Experience:</span>
                        <span class="font-semibold text-blue-900 ml-2">
                            {{ $this->totalExperience() }}
                        </span>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-gray-100">

                        <thead class="bg-gray-300 border-b border-gray-400">
                        <tr class="text-gray-600 uppercase text-xs tracking-wider">
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">#</th>
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">Designation</th>
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">Institution</th>
                            <th class="px-6 py-4 text-center border-r border-gray-400 last:border-r-0">Appointment Date</th>
                            <th class="px-6 py-4 text-center border-r border-gray-400 last:border-r-0">Joining Date</th>
                            <th class="px-6 py-4 text-center border-r border-gray-400 last:border-r-0">From Date</th>
                            <th class="px-6 py-4 text-center border-r border-gray-400 last:border-r-0">To Date</th>
                            <th class="px-6 py-4 text-center border-r border-gray-400 last:border-r-0">Experience</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                        @foreach($work as $i => $q)
                            <tr class="hover:bg-gray-100 transition">

                                <td class="px-6 py-4 text-gray-500 border-r border-gray-300 last:border-r-0">
                                    {{ $i + 1 }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 uppercase border-r border-gray-300 last:border-r-0">
                                    {{ $q['designation_name'] }}
                                </td>

                                <td class="px-6 py-4 text-gray-700 uppercase border-r border-gray-300 last:border-r-0">
                                    {{ $q['institution'] ?? $q['institution_name'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 border-r border-gray-300 last:border-r-0">
                                    {{ $this->formatDate($q['date_of_appointment'] ?? null) }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 border-r border-gray-300 last:border-r-0">
                                    {{ $this->formatDate($q['date_of_joining'] ?? null) }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 border-r border-gray-300 last:border-r-0">
                                    {{ $this->formatDate($q['from_date'] ?? null) }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 border-r border-gray-300 last:border-r-0">
                                    {{ !empty($q['is_current']) ? 'TILL DATE' : $this->formatDate($q['to_date'] ?? null) }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 border-r border-gray-300 last:border-r-0" style="text-transform: uppercase;">
                                    {{ $this->experience($q) }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex gap-4">

                                        {{-- Edit --}}
                                        <button
                                            wire:click="edit({{ $i }})"
                                            class="text-blue-600 hover:text-blue-800 transition"
                                            title="Edit">
                                            ✏️
                                        </button>

                                        {{-- Delete --}}
                                        <button
                                            wire:click="delete({{ $i }})"
                                            onclick="return confirm('Delete this work experience?')"
                                            class="text-red-600 hover:text-red-800 transition"
                                            title="Delete">
                                            🗑
                                        </button>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @endif



        <div class="flex justify-end pt-6">
            <button
                wire:click="save"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
                Save & Continue →
            </button>
        </div>

    @endunless

</div>
