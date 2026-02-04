<div class="basic-tab ace-scope bg-gray-50 border rounded-lg p-6 space-y-6">

    {{-- ADD BUTTON --}}
    @unless($showForm)
        <div class="flex justify-end">
            <button
                wire:click="add"
                class="flex items-center gap-2 px-4 py-2 rounded-lg border bg-white hover:bg-gray-100"
            >
                ➕ Add Qualification
            </button>
        </div>
    @endunless

    {{-- ADD / EDIT FORM --}}
        @if($showForm)
            <div class="bg-white border rounded-lg p-6 space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- DEGREE --}}
                    <div>
                        <label class="block text-sm font-medium text-blue-600 mb-1">
                            Degree <span class="text-red-600">*</span>
                        </label>

                        <select wire:model.live="form.degree_id" class="form-select w-full">
                            <option value="">CHOOSE DEGREE</option>
                            @foreach($degrees as $degree)
                                <option value="{{ $degree['id'] }}">
                                    {{ strtoupper($degree['name']) }}
                                </option>
                            @endforeach
                        </select>

                        @error('form.degree_id')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SPECIALISATION --}}
                    <div>
                        <label class="block text-sm font-medium text-blue-600 mb-1">
                            Specialisation
                            @if($specialisationMode === 2)
                                <span class="text-red-600">*</span>
                            @endif
                        </label>

                        @if($specialisationMode === 0)
                            <input type="text" value="NONE" readonly
                                   class="form-input w-full bg-gray-100 cursor-not-allowed">
                        @else
                            <select wire:model.defer="form.specialisation_id" class="form-select w-full">

                                @if($specialisationMode === 1)
                                    <option value="">NONE</option>
                                @elseif($specialisationMode === 2)
                                    <option value="">CHOOSE SPECIALISATION</option>
                                @endif

                                @foreach($specialisations as $spec)
                                    <option value="{{ $spec['id'] }}">
                                        {{ strtoupper($spec['mas_specialisation_name']) }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        @error('form.specialisation_id')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- INSTITUTION --}}
                    <div>
                        <label class="block text-sm font-medium text-blue-600 mb-1">
                            Name of the Institution <span class="text-red-600">*</span>
                        </label>

                        <input type="text" wire:model.defer="form.institution"
                               oninput="this.value = this.value.replace(/[^A-Za-z0-9,\s]/g, '')"
                               class="form-input w-full">

                        @error('form.institution')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-blue-600 mb-1">
                            Year of Exam <span class="text-red-600">*</span>
                        </label>
                        <input type="text" wire:model.defer="form.year_of_exam"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,4)"
                               class="form-input w-full">
                        @error('form.year_of_exam')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-blue-600 mb-1">
                            Year of Award <span class="text-red-600">*</span>
                        </label>
                        <input type="text" wire:model.defer="form.year_of_award"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,4)"
                               class="form-input w-full">
                        @error('form.year_of_award')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-blue-600 mb-1">
                            Professional Council Registration Number
                        </label>
                        <input type="text" wire:model.defer="form.registration_no"
                               oninput="this.value = this.value.replace(/[^A-Za-z0-9,\s]/g, '')"
                               class="form-input w-full">
                    </div>
                </div>

                <div class="flex justify-center gap-4 pt-4">
                    <button wire:click="saveForm"
                            class="px-6 py-2 rounded-lg bg-gray-400 text-white hover:bg-gray-500">
                        ✓ Save
                    </button>

                    <button wire:click="cancel"
                            class="px-6 py-2 rounded-lg bg-gray-300 text-gray-700 hover:bg-gray-400">
                        ↩ Cancel
                    </button>
                </div>
            </div>
        @endif

    {{-- TABLE / EMPTY STATE --}}
    @unless($showForm)

        @if(count($qualification) === 0)
            <div class="text-center text-gray-500 py-8">
                No qualifications added, please add by clicking on
                <strong>Add Qualification</strong>.
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="flex items-center px-6 py-4 border-b bg-gray-200">
                    <h2 class="text-sm font-semibold text-gray-700 tracking-wide uppercase">
                        Qualification Details
                    </h2>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-gray-100">
                        <thead class="bg-gray-300 border-b border-gray-400">
                        <tr class="text-gray-600 uppercase text-xs tracking-wider">
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">#</th>
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">Degree</th>
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">Specialisation</th>
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">Institution</th>
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">Exam Year</th>
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">Award Year</th>
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">Registration Number</th>
                            <th class="px-6 py-4 text-left border-r border-gray-400 last:border-r-0">Action</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                        @foreach($qualification as $i => $q)
                            <tr class="hover:bg-gray-100 transition">

                                <td class="px-6 py-4 text-gray-500 border-r border-gray-300 last:border-r-0">
                                    {{ $i + 1 }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 uppercase border-r border-gray-300 last:border-r-0">
                                    {{ $q['degree_code'] }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 uppercase border-r border-gray-300  last:border-r-0">
                                    {{ $q['specialisation_name'] ?? 'NONE' }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 uppercase border-r border-gray-300  last:border-r-0">
                                    {{ $q['institution'] ?? $q['university'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 text-center border-r border-gray-300  last:border-r-0">
                                    {{ $q['year_of_exam'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 text-center border-r border-gray-300  last:border-r-0">
                                    {{ $q['year_of_award'] ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 uppercase border-r border-gray-300  last:border-r-0">
                                    {{ $q['registration_no'] ?? '-' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex gap-4">
                                        <button
                                            wire:click="edit({{ $i }})"
                                            class="text-blue-600 hover:text-blue-800 transition"
                                            title="Edit">
                                            ✏️
                                        </button>

                                        <button
                                            wire:click="delete({{ $i }})"
                                            onclick="return confirm('Delete this qualification?')"
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
