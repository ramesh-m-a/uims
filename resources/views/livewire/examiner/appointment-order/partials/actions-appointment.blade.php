{{-- 🚫 NEVER RUN INSIDE ALLOCATION MODULE --}}
@php
    // ⭐ SAFETY DEFAULTS
    $isAppointmentModule = $isAppointmentModule ?? false;
    $requestStatusMap = $requestStatusMap ?? [];

    $safeDateRows = collect($dateRows ?? []);

    $allApproved = $safeDateRows->every(function($r) use ($requestStatusMap) {

        $r = is_array($r) ? (object) $r : $r;

        $statusData = $requestStatusMap[$r->batch_id][$r->examiner_id] ?? null;

        $label = strtolower(trim(
            $statusData['label']
            ?? $r->status_label
            ?? ''
        ));

        return $label === 'examiner approved';
    });

    $hasAppointment = !empty($row->pdf_path);
@endphp

@if($isAppointmentModule)

    {{-- ================= NOT READY ================= --}}
    @if(!$allApproved)

        <span class="text-gray-500 italic">
            Yet to be generated
        </span>

        {{-- ================= READY BUT NOT GENERATED ================= --}}
    @elseif(!$hasAppointment)

        <span class="text-blue-600 font-semibold">
            Ready for Appointment Generation
        </span>

        {{-- ================= GENERATED ================= --}}
    @else

        <a href="{{ asset($row->pdf_path) }}"
           target="_blank"
           class="text-green-700 font-semibold hover:underline"
           title="View Appointment Order">
            📄 View Appointment
        </a>

    @endif

@endif
