<div class="rounded border p-4 bg-white dark:bg-zinc-900">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-2">
        <div class="font-semibold">
            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
        </div>

        <div class="text-xs text-gray-500">
            {{ $log->created_at->format('d M Y, h:i A') }}
        </div>
    </div>

    {{-- Actor --}}
    <div class="text-sm text-gray-600 mb-3">
        Performed by:
        <strong>{{ optional($log->user)->name ?? 'System' }}</strong>
    </div>

    {{-- Diff --}}
    @include('audit.partials.diff', [
        'old' => $log->old_values,
        'new' => $log->new_values,
    ])

</div>
