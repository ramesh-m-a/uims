<div class="space-y-4 relative">
  {{--  @php
        dump([
            'rows_count' => $rows->count(),
            'unique_centres' => $rows->pluck('centre_name')->unique()->count(),
            'centres' => $rows->pluck('centre_name')->unique()->values(),
            'current_centre_filter' => $centre ?? null,
        ]);
    @endphp--}}
    {{-- =========================
     | BREADCRUMB
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => request()->route()?->getName() ?? '',
            'title' => 'Examiner Allocation',
            'mode'  => 'View / Allocate'
        ])
    </div>

    {{-- =========================
     | HEADER
     ========================= --}}
    <div class="flex justify-between items-center px-4">
        <h1 class="text-lg font-semibold text-gray-700">
            Examiner Allocation
            <span class="mx-1 text-gray-400">››</span>
            <span class="text-gray-400">View / Allocate</span>
        </h1>
    </div>

    {{-- =========================
     | FILTER BAR
     ========================= --}}
    <div class="card mx-4">
        <div class="card-body grid grid-cols-1 md:grid-cols-6 gap-2">
            <input wire:model.debounce.400ms="search" class="form-control" placeholder="Global search..." />
            <input wire:model.debounce.400ms="centre" class="form-control" placeholder="Centre" />
            <input wire:model.debounce.400ms="batch" class="form-control" placeholder="Batch" />
            <input wire:model.debounce.400ms="subject" class="form-control" placeholder="Subject" />
            <input wire:model.debounce.400ms="examiner" class="form-control" placeholder="Examiner" />
            <select wire:model="status" class="form-select">
                <option value="">All Status</option>
                <option value="Assigned">Assigned</option>
                <option value="Unassigned">Unassigned</option>
                <option value="Assign">Assign</option>
            </select>
        </div>
    </div>

    {{-- =========================
     | MAIN LAYOUT
     ========================= --}}
    <div class="grid grid-cols-12 gap-4 px-4">

        @php
            $grouped = $rows->groupBy('centre_name');

            if ($centre) {
                // When a centre is selected → sidebar should show only that centre
                $centres = collect([$centre]);
                $activeCentre = $centre;
            } else {
                // Default → show all centres
                $centres = $grouped->keys();
                $activeCentre = $centres->first();
            }
        @endphp

        {{-- =========================
         | LEFT CENTRES PANEL
         ========================= --}}
        <div class="col-span-12 md:col-span-3 lg:col-span-3">
            <div class="card h-full">
                <div class="card-header font-semibold flex justify-between items-center">
                    <span>Centres</span>

                    @if($centre)
                        <button
                            wire:click="$set('centre', null)"
                            class="text-xs font-semibold px-2 py-1 rounded-md
           bg-blue-50 text-blue-700
           hover:bg-blue-100 transition">
                            Show All
                        </button>
                    @endif
                </div>

                <div class="list-group list-group-flush overflow-auto text-start" style="max-height:78vh;">
                    @foreach($centres as $c)
                        <button
                            wire:click="$set('centre','{{ addslashes($c) }}')"
                            class="list-group-item list-group-item-action text-start px-3 py-2 text-sm leading-tight rounded-md mx-1 my-0.5
           transition-all duration-150
           hover:bg-gray-200 hover:text-gray-900
           {{ $centre === $c ? 'bg-gray-300 text-gray-900 shadow-sm' : 'bg-white text-gray-700' }}">
                            {{ $c }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- =========================
         | RIGHT CONTENT
         ========================= --}}
        <div class="col-span-12 md:col-span-9 lg:col-span-9 space-y-4">

            @foreach($grouped as $centreName => $centreRows)
                @if($activeCentre !== $centreName) @continue @endif

                <div class="card">
                    <div class="card-header bg-light text-orange-700 font-extrabold">
                        {{ $centreName }}
                    </div>

                    <div class="card-body space-y-4">

                        @foreach($centreRows->groupBy('batch_name') as $batch => $batchRows)
                            @foreach($batchRows->groupBy('from_date') as $date => $dateRows)

                                @php
                                    $formattedDate = \Carbon\Carbon::parse($date)->format('d-m-Y');
                                @endphp

                                <div class="border rounded-lg p-3 bg-white">

                                    {{-- HEADER --}}
                                    <div class="text-base font-semibold mb-2">
                                        {{ $batch }} - 📅 {{ $formattedDate }}
                                    </div>

                                    {{-- TABLE --}}
                                    <div class="overflow-x-auto">
                                        <table class="table table-sm table-bordered w-full">
                                            <thead>
                                            <tr class="text-gray-600 bg-light">
                                                <th class="ps-2">Type</th>
                                                <th>Examiner</th>
                                                <th class="ps-3">Mobile</th>
                                                <th>Status</th>
                                                <th class="text-end pe-3">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($dateRows as $row)
                                                <tr class="align-middle">
                                                    <td class="ps-2">{{ $row->examiner_type }}</td>

                                                    <td class="fw-semibold text-dark">
                                                        {{ $row->examiner_name }}

                                                        @if($row->is_additional ?? false)
                                                            <div style="color:#0d6efd; font-weight:700; font-size:12px;">
                                                                ADDITIONAL
                                                            </div>
                                                        @endif

                                                        {{-- Existing RESCHEDULED badge --}}
                                                        @if(($row->is_rescheduled ?? false) && !empty($row->original_from_date))
                                                            <div class="mt-1">
                                                                <div style="color:#dc3545; font-weight:700; font-size:12px;">
                                                                    CHANGED
                                                                </div>

                                                                <div style="color:#6c757d; font-size:12px; display:flex; align-items:center;">
                <span>
                    Original: {{ \Carbon\Carbon::parse($row->original_from_date)->format('d-m-Y') }}
                </span>

                                                                    <button
                                                                        wire:click="undoDateChange({{ $row->id }})"
                                                                        title="Revert to original date ({{ \Carbon\Carbon::parse($row->original_from_date)->format('d-m-Y') }})"
                                                                        style="background:none; border:none; padding:0; margin-left:8px; cursor:pointer; font-size:14px;"
                                                                    >
                                                                        ↩️
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- ✅ NEW: ADDITIONAL badge (same visual language) --}}
                                                        @if(($row->examiner_type ?? null) === 'Additional')
                                                            <div class="mt-1">
                                                                <div style="color:#0d6efd; font-weight:700; font-size:12px;">
                                                                    ADDITIONAL
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <td class="ps-3">
                                                        {{ $row->mobile }}
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-warning text-dark px-2">
                                                            {{ $row->status_label ?? 'Unassigned' }}
                                                        </span>
                                                    </td>

                                                    <td class="text-end pe-3 space-x-3 text-lg">
                                                        <button
                                                            wire:click="openDrawer({{ json_encode((array)$row) }})"
                                                            title="Change Examiner for this examiner only"
                                                            class="text-primary border-0 bg-transparent">
                                                            🔁
                                                        </button>

                                                        <button
                                                            wire:click="openContactRGUHSDrawer({{ json_encode((array)$row) }})"
                                                            title="Contact RGUHS for this slot"
                                                            class="text-warning border-0 bg-transparent">
                                                            🔒
                                                        </button>
                                                        <button
                                                            wire:click="openChangeDateDrawer({{ json_encode((array)$row) }})"
                                                            title="Change Date for this examiner only"
                                                            class="text-primary border-0 bg-transparent"
                                                        >
                                                            📅
                                                        </button>
                                                        <button
                                                            wire:click="openAddDrawer({{ $row->id }})"
                                                            title="Add Additional Examiner"
                                                            style="background:none; border:none; cursor:pointer;"
                                                        >
                                                            ➕
                                                        </button>
                                                        @if($row->is_additional ?? false)
                                                            <button
                                                                wire:click="undoAdditionalExaminer({{ $row->id }})"
                                                                title="Remove additional examiner"
                                                                style="background:none; border:none; cursor:pointer;"
                                                            >
                                                                ❌
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- =========================
     | DRAWER (ONLY PART MODIFIED)
     ========================= --}}
    @if($showDrawer)
        <div class="fixed inset-0 bg-dark bg-opacity-25 z-40" wire:click="closeDrawer"></div>

        <div class="fixed top-0 right-0 w-full sm:w-[620px] h-full bg-white shadow-lg z-50 p-5 overflow-auto">

            <div class="flex justify-between items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Slot Details</h5>
                    <div class="text-muted text-sm">Modify or review examiner assignment</div>
                </div>
                <button class="btn btn-sm btn-light" wire:click="closeDrawer">✕</button>
            </div>

            <div class="border rounded p-3 bg-light mb-3 text-sm space-y-1">
                <div><strong>Centre:</strong> {{ $selectedRow['centre_name'] ?? '' }}</div>
                <div><strong>Batch:</strong> {{ $selectedRow['batch_name'] ?? '' }}</div>
                <div><strong>Date:</strong> {{ $selectedRow['from_date'] ?? '' }}</div>
                <div><strong>Subject:</strong> {{ $selectedRow['subject_name'] ?? '' }}</div>
                <div><strong>Current Examiner:</strong>
                    {{ $selectedRow['examiner_name'] ?? '' }}
                    ({{ $selectedRow['examiner_type'] ?? '' }})
                </div>
                <div><strong>Mobile:</strong> {{ $selectedRow['mobile'] ?? '' }}</div>
            </div>

            <input wire:model.debounce.400ms="pickerSearch"
                   class="form-control mb-3"
                   placeholder="Search examiner by name or mobile..." />

            <div class="border rounded">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th width="150">Mobile</th>
                        <th width="90"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($pickerResults as $ex)
                        <tr>
                            <td class="pe-4">{{ $ex->name }}</td>
                            <td class="ps-4">{{ $ex->mobile }}</td>
                            <td class="text-end">
                                @if($isAddingAdditional)
                                    <button wire:click="addAdditionalExaminer({{ $ex->id }})"
                                            class="btn btn-success btn-xs">
                                        Add
                                    </button>
                                @else
                                    <button wire:click="assignExaminer({{ $ex->id }})"
                                            class="btn btn-primary btn-xs">
                                        Select
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">
                                No examiners found
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 flex justify-center">
                {{ $pickerResults->links() }}
            </div>

            <div class="mt-4 text-center">
                <button class="btn btn-info"
                        wire:click="openAddModal">
                    + Add Examiner
                </button>
            </div>
        </div>
    @endif

    @if($showDateDrawer)
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-dark bg-opacity-25 z-40"
             wire:click="$set('showDateDrawer', false)"></div>

        {{-- Drawer --}}
        <div class="fixed top-0 right-0 w-full sm:w-[520px] h-full bg-white shadow-lg z-50 p-5 overflow-auto">

            {{-- Header --}}
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Change Schedule Date</h5>
                    <div class="text-muted text-sm">
                        This change applies only to this examiner
                    </div>
                </div>
                <button class="btn btn-sm btn-light"
                        wire:click="$set('showDateDrawer', false)">✕</button>
            </div>

            {{-- Context Card --}}
            <div class="border rounded p-3 bg-light mb-4 text-sm space-y-1">
                <div><strong>Centre:</strong> {{ $selectedRow['centre_name'] ?? '' }}</div>
                <div><strong>Batch:</strong> {{ $selectedRow['batch_name'] ?? '' }}</div>
                <div><strong>Subject:</strong> {{ $selectedRow['subject_name'] ?? '' }}</div>
                <div><strong>Type:</strong> {{ $selectedRow['examiner_type'] ?? '' }}</div>
                <div><strong>Examiner:</strong> {{ $selectedRow['examiner_name'] ?? '' }}</div>
                <div><strong>Mobile:</strong> {{ $selectedRow['mobile'] ?? '' }}</div>
            </div>

            {{-- Date Section --}}
            <div class="border rounded p-3 mb-4">
                <div class="fw-semibold mb-3">Schedule</div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="text-xs text-muted">Current Date</label>
                        <input class="form-control" disabled value="{{ \Carbon\Carbon::parse($current_from_date)->format('d/m/Y') }}">
                    </div>

                    <div>
                        <label class="text-xs text-muted">New Date</label>
                        <input
                            type="date"
                            wire:model="new_from_date"
                            class="form-control"
                            min="{{ now()->toDateString() }}"
                        >
                        @error('new_from_date')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="text-muted text-xs">
                    ℹ Only this examiner’s schedule date will be updated.
                </div>
            </div>

            {{-- Action --}}
            <div class="mt-4 text-end">
                <button wire:click="saveDateChange"
                        class="btn btn-success px-4">
                    Update Date
                </button>
            </div>

        </div>
    @endif



</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('toast', (data) => {
            const toast = document.createElement('div');
            toast.innerText = data.message;

            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = '#198754';
            toast.style.color = '#fff';
            toast.style.padding = '10px 16px';
            toast.style.borderRadius = '6px';
            toast.style.fontSize = '14px';
            toast.style.zIndex = '9999';
            toast.style.boxShadow = '0 4px 10px rgba(0,0,0,0.2)';

            document.body.appendChild(toast);

            setTimeout(() => toast.remove(), 2500);
        });
    });
</script>
