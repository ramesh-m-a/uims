{{-- resources/views/livewire/master/_crud-table.blade.php --}}

<div class="space-y-4">

    @php
        $showActions =
            ($canEdit ?? false) ||
            ($canDelete ?? false) ||
            ($canRestore ?? false) ||
            ($canPermissions ?? false) ||
            ($isUserTable ?? false) ||
            ($showPermissions ?? false);
    @endphp

    {{-- =========================
     | TOP BAR (PER PAGE + SEARCH)
     ========================= --}}
    <div class="flex justify-between items-start mt-3 mb-2">
        <div class="flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap">
            <span>Display</span>

            <select wire:model.live="perPage"
                    class="form-select form-select-sm w-auto">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>

            <span>records per page</span>
        </div>
        <div class="flex flex-col items-end gap-2">
            <div class="flex items-center gap-2">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="{{ $searchPlaceholder ?? 'Search...' }}"
                       class="form-control form-control-sm w-64">

                @if(!empty($search))
                    <button wire:click="$set('search', '')"
                            class="btn btn-link text-danger p-0"
                            title="Clear search">
                        <i class="fa fa-times"></i>
                    </button>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @include('livewire.master._export-buttons')
            </div>
        </div>

    </div>

    {{-- =========================
     | BULK ACTION BAR
     ========================= --}}
    @if(($canDelete ?? false) && !empty($selected))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded px-4 py-2">
            <span class="text-sm text-red-700 font-medium">
                {{ count($selected) }} selected
            </span>

            <button
                wire:click="deleteSelected"
                class="btn btn-sm btn-danger">
                <i class="fa fa-trash mr-1"></i>
                Delete Selected
            </button>
        </div>
    @endif

    {{-- =========================
     | TABLE
     ========================= --}}
    <div class="admin-card">
        <div class="table-responsive" style="max-height: 70vh; overflow: auto;">
            <table class="table table-bordered admin-table">

                <thead>
                <tr>
                    <th width="40"
                        class="text-center"
                        style="position: sticky; top: 0; left: 0; background: #fff; z-index: 10;">
                        @if(($canDelete ?? false) || ($canRestore ?? false))
                            <input type="checkbox" wire:model.live="selectAll">
                        @endif
                    </th>

                    @foreach($columns as $column)
                        <th
                            style="position: sticky; top: 0; background: #fff; z-index: 9;"
                            class="
                                {{ ($column['sortable'] ?? false) ? 'sortable' : '' }}
                                {{ in_array($column['type'] ?? '', ['enum','code']) ? 'text-center' : '' }}
                            "
                            @if($column['sortable'] ?? false)
                                wire:click="sortBy('{{ $column['field'] }}')"
                            @endif
                        >
                            {{ $column['label'] }}

                            @if(($sortField ?? null) === $column['field'])
                                <i class="fa {{ ($sortDirection ?? 'asc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                            @endif
                        </th>
                    @endforeach

                    @if($showActions)
                        <th width="140"
                            class="text-center"
                            style="position: sticky; top: 0; background: #fff; z-index: 9;">
                            Actions
                        </th>
                    @endif
                </tr>

                <tr class="bg-gray-50">
                    <th style="position: sticky; top: 42px; left: 0; background: #f9fafb; z-index: 8;"></th>

                    @foreach($columns as $column)
                        <th
                            style="position: sticky; top: 42px; background: #f9fafb; z-index: 8;"
                            class="{{ in_array($column['type'] ?? '', ['enum','code']) ? 'text-center' : '' }}"
                        >
                            @if(($column['filterable'] ?? false) && in_array(($column['type'] ?? 'text'), ['text', 'code']))
                            <div class="relative">
                                    <input
                                        type="text"
                                        wire:model.live.debounce.400ms="filters.{{ $column['field'] }}"
                                        class="form-control form-control-sm pr-7"
                                        placeholder="Filter {{ $column['label'] }}"
                                    />

                                    @php
                                        $filterValue = data_get($filters, $column['field']);
                                    @endphp

                                    @if(!empty($filterValue))
                                        <button
                                            type="button"
                                            wire:click="$set('filters.{{ $column['field'] }}', '')"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
                                        >
                                            <i class="fa fa-times text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            @endif

                            @if(($column['type'] ?? '') === 'enum')
                                <div class="flex justify-center">
                                    <select
                                        wire:model.live="filters.{{ $column['field'] }}"
                                        class="form-select form-select-sm w-auto text-center">
                                        <option value="">All</option>
                                        @foreach($column['options'] ?? [] as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </th>
                    @endforeach

                    @if($showActions)
                        <th style="position: sticky; top: 42px; background: #f9fafb; z-index: 8;"></th>
                    @endif
                </tr>
                </thead>

                <tbody>
                @forelse($rows as $row)
                    <tr wire:key="row-{{ $row->id }}" title="ID: {{ $row->id }}">

                        <td class="text-center"
                            style="position: sticky; left: 0; background: #fff; z-index: 5;">
                            <input type="checkbox"
                                   wire:model.live="selected"
                                   value="{{ (string) $row->id }}">
                        </td>

                        @foreach($columns as $column)
                            @php
                                $value = data_get($row, $column['field']);
                            @endphp

                            {{-- CODE TYPE --}}
                            @if(($column['type'] ?? '') === 'code')
                                <td class="text-center align-middle">
                                    <div class="flex justify-center">
                                        <code
                                            style="
                                                font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
                                                background: #f3f4f6;
                                                padding: 4px 10px;
                                                border-radius: 6px;
                                                font-size: 0.85rem;
                                                white-space: nowrap;
                                            "
                                        >
                                            {{ $value ?? '—' }}
                                        </code>
                                    </div>
                                </td>

                                {{-- ENUM --}}
                            @elseif(($column['type'] ?? '') === 'enum')
                                <td class="text-center align-middle">
                                    <div class="flex justify-center">
                                        <span
                                            class="admin-badge {{ $value == 1 ? 'success' : 'warning' }}"
                                            style="
                                                min-width: 84px;
                                                text-align: center;
                                                display: inline-flex;
                                                justify-content: center;
                                                align-items: center;
                                                padding: 4px 10px;
                                            "
                                        >
                                            {{ $column['options'][$value] ?? '—' }}
                                        </span>
                                    </div>
                                </td>

                            @elseif($value instanceof \Illuminate\Support\Collection)
                                <td>
                                    <div class="flex flex-col">
                                        @foreach($value as $item)
                                            <div class="text-sm leading-5 mb-2" style="white-space: nowrap;">
                                                @if(is_scalar($item))
                                                    {!! preg_replace(
                                                        '/\((\d+)\)/',
                                                        '<span class="font-semibold text-blue-500">($1)</span>',
                                                        e($item)
                                                    ) !!}
                                                @else
                                                    {{ data_get($item, last(explode('.', $column['field']))) }}
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @else
                                <td>
                                    <div style="white-space: nowrap;">
                                        {{ $value ?? '—' }}
                                    </div>
                                </td>
                            @endif
                        @endforeach

                        @if($showActions)
                            <td class="text-center align-middle">
                                <div class="admin-actions">

                                    @if((!method_exists($row, 'trashed') || !$row->trashed()) && ($canEdit ?? false))
                                        <button wire:click="editRow({{ $row->id }})" class="action-btn edit">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>
                                    @endif

                                    @if((!method_exists($row, 'trashed') || !$row->trashed()) && ($canDelete ?? false))
                                        <button wire:click="confirmDelete({{ $row->id }})" class="action-btn delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif

                                    @if(method_exists($row, 'trashed') && $row->trashed() && ($canRestore ?? false))
                                        <button
                                            wire:click="restoreRow({{ $row->id }})"
                                            class="action-btn restore"
                                            title="Restore"
                                        >
                                            <i class="fa fa-rotate-left"></i>
                                        </button>
                                    @endif

                                    @if(($isUserTable ?? false) && ($canPermissions ?? false))
                                        <a
                                            href="{{ route('user.permissions', $row->id) }}"
                                            class="action-btn permissions"
                                            title="User Permissions"
                                        >
                                            <i class="fa fa-shield-halved"></i>
                                        </a>
                                    @endif

                                    @if(($showPermissions ?? false) && ($canPermissions ?? false))
                                        <a
                                            href="{{ route($permissionsRoute, $row->id) }}"
                                            class="action-btn permissions"
                                            title="Role Permissions"
                                        >
                                            <i class="fa fa-shield-halved"></i>
                                        </a>
                                    @endif

                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + ($showActions ? 2 : 1) }}"
                            class="text-center text-muted py-4">
                            No records found !
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

        <div class="admin-footer flex justify-between items-center">
            <div class="text-muted small">
                Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }}
                of {{ $rows->total() }} records
            </div>

            {{ $rows->links('livewire.pagination-only-links') }}
        </div>
    </div>

    @include('components.photo-preview-modal')
</div>
