<div class="space-y-6">

    {{-- BREADCRUMB --}}
    @include('partials.breadcrumb', [
        'route' => 'user.permissions',
        'title' => 'Bulk Permission Copy',
        'mode'  => 'Preview'
    ])

    <h2 class="text-lg font-semibold">
        Copy Permissions → {{ $targetUser->name }}
    </h2>

    {{-- SOURCE SELECT --}}
    <div class="form-group">
        <label class="required">Source User</label>
        <select wire:model="sourceUserId" class="form-control">
            <option value="">-- Select User --</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>
    </div>

    @if($sourceUserId)
        {{-- PREVIEW --}}
        <div class="grid grid-cols-2 gap-4">

            <div class="border rounded p-3">
                <h4 class="font-medium text-green-600">➕ Will be Added</h4>
                <ul class="text-sm mt-2">
                    @forelse($preview['add'] as $p)
                        <li>{{ $p }}</li>
                    @empty
                        <li class="text-gray-400">None</li>
                    @endforelse
                </ul>
            </div>

            <div class="border rounded p-3">
                <h4 class="font-medium text-red-600">➖ Will be Removed</h4>
                <ul class="text-sm mt-2">
                    @forelse($preview['remove'] as $p)
                        <li>{{ $p }}</li>
                    @empty
                        <li class="text-gray-400">None</li>
                    @endforelse
                </ul>
            </div>

        </div>

        {{-- CONFIRM --}}
        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model="confirmed">
            <label class="text-sm">
                I understand this will overwrite user overrides
            </label>
        </div>

        <button
            wire:click="apply"
            class="btn btn-danger"
            @disabled(! $confirmed)
        >
            Apply Copy
        </button>
    @endif
</div>
