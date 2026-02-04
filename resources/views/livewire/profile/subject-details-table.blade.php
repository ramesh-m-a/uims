<div class="basic-tab ace-scope bg-gray-50 border rounded-lg p-6 space-y-6">

    {{-- ADD BUTTON --}}
    @unless($showForm)
        <div class="flex justify-end">
            <button wire:click="add"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg border bg-white hover:bg-gray-100">
                ➕ Add Subject
            </button>
        </div>
    @endunless

    {{-- FORM --}}
    @if($showForm)
        <div class="bg-white border rounded-lg p-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- YEAR --}}
                <div>
                    <label class="block text-sm font-medium text-blue-600 mb-1">
                        Year <span class="text-red-600">*</span>
                    </label>

                    <select wire:model.defer="form.mas_year_id" class="form-select w-full">
                        <option value="">CHOOSE YEAR</option>
                        @foreach($years as $y)
                            <option value="{{ $y['id'] }}">
                                {{ $y['mas_year_year'] }}
                            </option>
                        @endforeach
                    </select>

                    @error('form.mas_year_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SUBJECT --}}
                <div>
                    <label class="block text-sm font-medium text-blue-600 mb-1">
                        Subject <span class="text-red-600">*</span>
                    </label>

                    <select wire:model.defer="form.mas_subject_id" class="form-select w-full">
                        <option value="">CHOOSE SUBJECT</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s['id'] }}">
                                {{ strtoupper($s['mas_subject_name']) }}
                            </option>
                        @endforeach
                    </select>

                    @error('form.mas_subject_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>


            </div>

            @error('duplicate')
            <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

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

    {{-- TABLE --}}
    @unless($showForm)
        @if(count($rows) === 0)
            <div class="text-center text-gray-500 py-8">
                No subjects added, click <strong>Add Subject</strong>.
            </div>
        @else
            <div class="bg-white border rounded-xl overflow-hidden">
                <div class="flex items-center px-6 py-4 border-b bg-gray-200">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase">
                        Subject Details
                    </h2>
                </div>

                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-300">
                    <tr class="text-xs uppercase">
                        <th class="px-6 py-3 text-left">#</th>
                        <th class="px-6 py-3 text-left">Year</th>
                        <th class="px-6 py-3 text-left">Subject</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($rows as $i => $row)
                        <tr class="border-t hover:bg-gray-100">
                            <td class="px-6 py-3">{{ $i+1 }}</td>



                            <td class="px-6 py-3">
                                {{ collect($years)->firstWhere('id', $row['mas_year_id'])['mas_year_year'] ?? '-' }}
                            </td>

                            <td class="px-6 py-3 uppercase">
                                {{ collect($subjects)->firstWhere('id', $row['mas_subject_id'])['mas_subject_name'] ?? '-' }}
                            </td>

                            <td class="px-6 py-3 text-center">
                                <button wire:click="edit({{ $row['id'] }})">✏️</button>
                                <button wire:click="delete({{ $row['id'] }})">🗑</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endunless
</div>
