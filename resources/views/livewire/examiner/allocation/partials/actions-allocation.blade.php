@php
    // ⭐ SAFETY DEFAULTS
    $requestStatusMap = $requestStatusMap ?? [];
    $isAppointmentModule = $isAppointmentModule ?? false;

    $statusData = $requestStatusMap[$row->batch_id][$row->examiner_id] ?? null;
    $isPendingRequest = $statusData && ($statusData['is_pending'] ?? false);
@endphp

{{-- 🚫 NEVER SHOW ALLOCATION ACTIONS INSIDE APPOINTMENT MODULE --}}
@if(!$isAppointmentModule)

    {{-- ================= ADMIN ACTIONS ================= --}}
    @if(!$isCollegeUser)

        @if($isPendingRequest)
            <span style="color:#fd7e14;">⏳</span>
        @else
            <button wire:click="openDrawer(@js($row))" title="Change Examiner">🔁</button>
            <button wire:click="openContactRGUHSDrawer(@js($row))" title="Contact RGUHS">🔒</button>
            <button wire:click="openChangeDateDrawer(@js($row))" title="Change Date">📅</button>
            <button wire:click="openAddDrawer({{ $row->id }})" title="Add Additional Examiner">➕</button>
        @endif

    @endif

    {{-- ================= COLLEGE ACTIONS ================= --}}
    @if($isCollegeUser)

        @if($isPendingRequest)
            <span style="color:#fd7e14;">⏳</span>
        @else
            <button class="uims-icon-action"
                    wire:click="openRequestDrawer({{ json_encode((array)$row) }})">

                <x-uims-icon name="request" size="md" />

            </button>
        @endif

    @endif

@endif
