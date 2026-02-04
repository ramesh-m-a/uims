@php
    use App\Support\AuditSectionMap;

    /**
     * Build unified audit timeline grouped by section
     */
    $sections = [];

    foreach ($auditLogs as $log) {

        /**
         * ===============================
         * SUBMIT / APPROVE / REJECT
         * ===============================
         */
        if (in_array($log->action, ['submit', 'approve', 'reject'])) {

            $sections['Workflow'][] = [
                'date'    => $log->created_at,
                'change'  => strtoupper($log->action),
                'details' => 'Profile ' . strtoupper($log->action),
                'by'      => $log->actorName(),
            ];

            continue;
        }

        /**
         * ===============================
         * FIELD LEVEL CHANGES
         * ===============================
         */
        $old = $log->old_values ?? [];
        $new = $log->new_values ?? [];

        foreach (array_unique(array_merge(array_keys($old), array_keys($new))) as $field) {

            // 🔐 skip sensitive fields
            if (in_array($field, [
                'password',
                'remember_token',
                'two_factor_secret',
                'two_factor_recovery_codes',
            ])) {
                continue;
            }

            $section = AuditSectionMap::sectionFor($field);

            $sections[$section][] = [
                'date'    => $log->created_at,
                'change'  => strtoupper(str_replace('_', ' ', $field)),
                'details' => ($old[$field] ?? '-') . ' → ' . ($new[$field] ?? '-'),
                'by'      => $log->actorName(),
            ];
        }
    }
@endphp

<div class="bg-white border rounded-lg p-4 mt-6">

    <h2 class="text-md font-semibold text-gray-800 mb-4">
        Modification Details
    </h2>

    @if(empty($sections))
        <div class="text-sm text-gray-500">
            No modification history available.
        </div>
    @else

        <div class="space-y-3">

            @foreach($sections as $sectionName => $rows)

                <details class="border rounded-lg">
                    <summary class="cursor-pointer select-none px-4 py-2 font-semibold bg-gray-50">
                        {{ $sectionName }}
                        <span class="text-xs text-gray-500 ml-2">
                            ({{ count($rows) }})
                        </span>
                    </summary>

                    <div class="p-3 overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2">Date</th>
                                <th class="border px-3 py-2">Change</th>
                                <th class="border px-3 py-2">Details</th>
                                <th class="border px-3 py-2">By</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="border px-3 py-2 whitespace-nowrap">
                                        {{ $row['date']?->format('d-m-Y H:i') }}
                                    </td>
                                    <td class="border px-3 py-2 font-medium">
                                        {{ $row['change'] }}
                                    </td>
                                    <td class="border px-3 py-2 text-gray-700">
                                        {{ $row['details'] }}
                                    </td>
                                    <td class="border px-3 py-2 font-semibold">
                                        {{ $row['by'] }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>

            @endforeach

        </div>
    @endif
</div>
