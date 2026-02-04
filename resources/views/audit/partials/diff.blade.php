@php
    use App\Support\AuditSectionMap;

    $old = is_array($old) ? $old : json_decode($old ?? '[]', true);
    $new = is_array($new) ? $new : json_decode($new ?? '[]', true);

    $changes = [];

    foreach (array_unique(array_merge(array_keys($old), array_keys($new))) as $key) {
        if (($old[$key] ?? null) != ($new[$key] ?? null)) {
            $section = AuditSectionMap::sectionFor($key);
            $changes[$section][] = [
                'field' => $key,
                'old' => $old[$key] ?? null,
                'new' => $new[$key] ?? null,
            ];
        }
    }
@endphp

@foreach ($changes as $section => $items)
    <div class="mt-4">
        <div class="font-semibold text-sm mb-2">
            {{ $section }}
        </div>

        <table class="w-full text-sm border">
            <thead class="bg-gray-100 dark:bg-zinc-800">
            <tr>
                <th class="p-2 text-left">Field</th>
                <th class="p-2 text-left">Before</th>
                <th class="p-2 text-left">After</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($items as $row)
                <tr class="border-t">
                    <td class="p-2 font-medium">{{ $row['field'] }}</td>
                    <td class="p-2 text-red-700">{{ $row['old'] ?? '—' }}</td>
                    <td class="p-2 text-green-700">{{ $row['new'] ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endforeach
