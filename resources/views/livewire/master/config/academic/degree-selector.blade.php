<div class="bg-gray-50 border rounded-lg p-6 space-y-6">

    {{-- DEGREE LEVEL --}}
    <div>
        <label class="block text-sm font-medium mb-1">
            Degree Level
        </label>

        <select
            wire:model.live="degreeLevelId"
            class="form-select w-full"
        >
            <option value="">Select Degree Level</option>

            @foreach($degreeLevels as $level)
                <option value="{{ $level->id }}">
                    {{ $level->name }}
                </option>
            @endforeach
        </select>
        @error('degreeLevelId')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- DEGREE --}}
    @if(! empty($degrees))
        <div>
            <label class="block text-sm font-medium mb-1">
                Degree
            </label>

            <select
                wire:model.live="degreeId"
                class="form-select w-full"
            >
                <option value="">Select Degree</option>

                @foreach($degrees as $degree)
                    <option value="{{ $degree->id }}">
                        {{ $degree->code }} — {{ $degree->name }}
                    </option>
                @endforeach
            </select>
            @error('degreeId')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror

        </div>
    @endif

    {{-- SPECIALISATION --}}
    @if($requiresSpecialisation)

        <div>
            <label class="block text-sm font-medium mb-1">
                Specialisation
                <span class="text-red-600">*</span>
            </label>

            <select
                wire:model.live="specialisationId"
                class="form-select w-full"
            >
                <option value="">Select Specialisation</option>

                @foreach($specialisations as $spec)
                    <option value="{{ $spec->id }}">
                        {{ $spec->name }}
                    </option>
                @endforeach
            </select>

            @error('specialisationId')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-500 mt-1">
                Specialisation is mandatory for this degree
            </p>
        </div>

    @endif

</div>
