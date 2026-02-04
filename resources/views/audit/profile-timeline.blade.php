<x-layouts.app>

    <flux:heading size="lg">
        Audit Timeline — {{ $user->name }}
    </flux:heading>

    <div class="mt-6 space-y-6">

        @forelse ($logs as $log)
            @include('audit.partials.timeline-item', ['log' => $log])
        @empty
            <p class="text-gray-500">No audit history found.</p>
        @endforelse

    </div>

</x-layouts.app>
