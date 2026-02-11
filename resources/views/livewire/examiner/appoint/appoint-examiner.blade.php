<div class="space-y-4">

    {{-- =========================
  | BREADCRUMB
  ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => request()->route()?->getName() ?? '',
            'title' => 'Examiner Allocation same blade',
            'mode'  => 'View / Allocate'
        ])
    </div>

    {{-- =========================
     | HEADER
     ========================= --}}
    <x-sub-header title="Appoint Examiner" subtitle="View / Update" subtitleClass="text-gray-500">
    </x-sub-header>

    <div class="flex justify-center">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 w-full max-w-5xl items-center">

            <select wire:model.live="yearId" class="form-select" style="text-transform: uppercase;">
                <option value="">CHOOSE YEAR</option>
                @foreach($years as $y)
                    <option value="{{ $y->id }}">{{ $y->mas_year_year }}</option>
                @endforeach
            </select>

            <select wire:model.live="monthId" class="form-select" @disabled(!$yearId) style="text-transform: uppercase;">
                <option value="">CHOOSE MONTH</option>
                @foreach($months as $m)
                    <option value="{{ $m->id }}">{{ $m->mas_month_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="schemeId" class="form-select" @disabled(!$monthId) style="text-transform: uppercase;">
                <option value="">CHOOSE SCHEME</option>
                @foreach($schemes as $s)
                    <option value="{{ $s->id }}">{{ $s->mas_revised_scheme_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="degreeId" class="form-select" @disabled(!$schemeId) style="text-transform: uppercase;">
                <option value="">CHOOSE DEGREE</option>
                <option value="1">UNDER GRADUATE</option>
                <option value="2">POST GRADUATE</option>
                <option value="3">POST GRADUATE - SUPER SPECIALITY</option>
            </select>

        </div>
    </div>

    {{-- Helper message --}}
    @if(!$yearId || !$monthId || !$schemeId || !$degreeId)
        <div class="flex justify-center mt-6">
            <div class="text-gray-500 text-sm bg-gray-50 border rounded-lg px-6 py-3">
                Please select Year, Month, Scheme and Degree to continue.
            </div>
        </div>
    @endif

    {{-- Reset button --}}
    @if($yearId || $monthId || $schemeId || $degreeId)
        <div class="flex justify-center mt-4">
            <button
                wire:click="resetFilters"
                class="px-4 py-2 text-sm bg-gray-100 border rounded hover:bg-gray-200"
            >
                Reset Filters
            </button>
        </div>
    @endif


</div>
