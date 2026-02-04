<div class="space-y-4">

    {{-- =========================
   | BREADCRUMB BAR
   ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => $pageRoute,
            'title' => 'Department',
            'mode'  => $this->breadcrumbMode() ?? 'View / Update'
        ])
    </div>

    {{-- =========================
     | PAGE TITLE ROW
     ========================= --}}
    <div class="flex justify-between items-center">
        <h1 class="font-normal text-black-400">
            Department
        </h1>

        @if($this->can('create'))
            <button
                wire:click="create"
                class="dt-button buttons-collection btn btn-white btn-bold"
            >
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add Department
            </button>
        @endif
    </div>

    {{-- =========================
     | TOP BAR
     ========================= --}}
    <div class="flex justify-between items-start mt-3 mb-2">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <span>Display</span>
            <select wire:model.live="perPage" class="form-select w-auto">
                <option>10</option><option>25</option><option>50</option><option>100</option>
            </select>
            <span>records per page</span>
        </div>

        <div class="flex flex-col items-end gap-2">
            <input wire:model.live.debounce.300ms="search"
                   class="form-control input-sm w-64"
                   placeholder="Search..." />
            @include('livewire.master._export-buttons')
        </div>
    </div>

    {{-- =========================
     | TABLE
     ========================= --}}
    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-bordered admin-table">

                <thead>
                <tr>
                    <th width="40" class="text-center">
                        <input type="checkbox" wire:model.live="selectAll">
                    </th>

                    @foreach($columns as $column)
                        <th>{{ $column['label'] }}</th>
                    @endforeach

                    <th width="140" class="text-center">Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse($departments as $row)
                    <tr wire:key="row-{{ $row->id }}">

                        <td class="text-center">
                            <input type="checkbox" wire:model.live="selected" value="{{ $row->id }}">
                        </td>

                        @foreach($columns as $column)
                            <td class="align-top">

                                {{-- ✅ STREAMS — PRODUCTION PARITY --}}
                                @if($column['field'] === 'stream.mas_stream_name')
                                    @if($row->streams && $row->streams->count())
                                        <div class="space-y-1">
                                            @foreach($row->streams as $i => $stream)
                                                <div
                                                    class="
                        px-3 py-1 rounded
                        {{ $i % 2 === 0 ? 'bg-gray-300' : 'bg-gray-200' }}
                        text-gray-900
                    "
                                                >
                                                    {{ strtoupper($stream->mas_stream_name) }},
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        —
                                    @endif

                                    {{-- ENUM --}}
                                @elseif(($column['type'] ?? '') === 'enum')
                                    @php $v = data_get($row, $column['field']); @endphp
                                    <span class="admin-badge {{ $v == 1 ? 'success' : 'warning' }}">
                                        {{ $column['options'][$v] ?? '—' }}
                                    </span>

                                    {{-- DEFAULT --}}
                                @else
                                    {{ data_get($row, $column['field']) ?? '—' }}
                                @endif

                            </td>
                        @endforeach

                        <td class="text-center">
                            <div class="admin-actions">
                                @if($canEdit)
                                    <button wire:click="editRow({{ $row->id }})"
                                            class="action-btn edit">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                @endif
                                @if($canDelete)
                                    <button wire:click="confirmDelete({{ $row->id }})"
                                            class="action-btn delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 2 }}"
                            class="text-center text-muted py-4">
                            No records found
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>
