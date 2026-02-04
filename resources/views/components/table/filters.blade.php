@props([
    'columns' => [],
    'filters' => [],
])

<div class="flex flex-wrap gap-3 items-center">

    @foreach($columns as $column)

        @if(($column['filterable'] ?? false) === true)

            {{-- =====================
             | TEXT FILTER
             ===================== --}}
            @if(($column['type'] ?? 'text') === 'text')
                <div class="relative" style="width: 220px;">

                    <input
                        type="text"
                        wire:model.live.debounce.300ms="filters.{{ $column['field'] }}"
                        placeholder="Filter {{ $column['label'] }}"
                        class="form-control form-control-sm"
                        style="padding-right: 32px;"
                    />

                    {{-- ❌ CLEAR --}}
                    @if(!empty($filters[$column['field']] ?? null))
                        <span
                            wire:click="$set('filters.{{ $column['field'] }}', '')"
                            style="
                                position: absolute;
                                right: 8px;
                                top: 50%;
                                transform: translateY(-50%);
                                cursor: pointer;
                                color: #999;
                                font-size: 14px;
                            "
                            title="Clear filter"
                        >
                            ✕
                        </span>
                    @endif
                </div>
            @endif

            {{-- =====================
             | ENUM FILTER
             ===================== --}}
            @if(($column['type'] ?? null) === 'enum')
                <div class="relative" style="width: 220px;">

                    <select
                        wire:model.live="filters.{{ $column['field'] }}"
                        class="form-select form-select-sm"
                        style="padding-right: 32px;"
                    >
                        <option value="">All {{ $column['label'] }}</option>

                        @foreach($column['options'] ?? [] as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- ❌ CLEAR --}}
                    @if(!empty($filters[$column['field']] ?? null))
                        <span
                            wire:click="$set('filters.{{ $column['field'] }}', '')"
                            style="
                                position: absolute;
                                right: 8px;
                                top: 50%;
                                transform: translateY(-50%);
                                cursor: pointer;
                                color: #999;
                                font-size: 14px;
                            "
                            title="Clear filter"
                        >
                            ✕
                        </span>
                    @endif
                </div>
            @endif

        @endif

    @endforeach

</div>
