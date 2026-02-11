{{-- 🚫 NEVER RUN INSIDE ALLOCATION MODULE --}}

@php
    $isAppointmentModule = $isAppointmentModule ?? false;
    $requestStatusMap = $requestStatusMap ?? [];

    $safeDateRows = collect($dateRows ?? []);

    /**
     * ✅ APPROVAL CHECK
     */
    $allApproved = $safeDateRows->every(function ($r) use ($requestStatusMap) {

        $r = is_array($r) ? (object) $r : $r;

        $statusData = $requestStatusMap[$r->batch_id][$r->examiner_id] ?? null;

        $label = strtolower(trim(
            $statusData['label']
            ?? $r->status_label
            ?? ''
        ));

        return $label === 'examiner approved';
    });

    /**
     * ✅ ORDER + PDF STATE DETECTION
     */
    $hasOrderNumber = !empty($row->order_number ?? null);
    $hasPdf         = !empty($row->pdf_path ?? null);

@endphp

@if($isAppointmentModule)

    {{-- ================= NOT APPROVED ================= --}}
    @if(!$allApproved)

        <span class="text-gray-500 italic">
            Yet to be generated
        </span>

        {{-- ================= APPROVED BUT ORDER NOT CREATED ================= --}}
    @elseif(!$hasOrderNumber)

        <button
            wire:click="generateAppointmentOrder({{ $row->id }})"
            class="text-blue-600 font-semibold hover:underline"
            title="Generate Appointment Order">
            ⚡ Generate Appointment
        </button>

        {{-- ================= ORDER EXISTS BUT PDF NOT READY ================= --}}
    @elseif($hasOrderNumber && !$hasPdf)

        <div class="flex items-center gap-2 justify-end">

            <span class="text-orange-600 font-semibold animate-pulse">
                ⏳ Generating PDF...
            </span>

            <button
                wire:click="retryAppointmentPdf({{ $row->id }})"
                class="text-xs text-red-600 hover:underline"
                title="Retry PDF Generation">
                Retry
            </button>

        </div>

        {{-- ================= READY ================= --}}
    @else

        <div class="flex items-center gap-3 justify-end">

            {{-- VIEW --}}
            <a href="{{ route('appointment-orders.view', $row->appointment_order_id) }}"
               target="_blank"
               class="text-green-700 hover:text-green-900"
               title="View Appointment Order">
                👁
            </a>

            {{-- DOWNLOAD --}}
            <a href="{{ route('appointment-orders.download', $row->appointment_order_id) }}"
               class="text-blue-700 hover:text-blue-900"
               title="Download Appointment Order">
                ⬇
            </a>

        </div>

    @endif

@endif
