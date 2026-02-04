<div class="space-y-6">

    {{-- =========================
     | BREADCRUMB
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => 'user.permissions',
            'title' => 'User Permissions',
            'mode'  => 'Override'
        ])
    </div>

    {{-- =========================
     | HEADER
     ========================= --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold">
                User Permission Overrides
            </h2>

            <p class="text-sm text-gray-500">
                User: <span class="font-medium">{{ $user->name }}</span>
            </p>

            <p class="text-xs text-gray-500 mt-1">
                Effective Permissions:
                <span class="font-semibold text-black">{{ $effectiveCount }}</span>
                • Overrides:
                <span class="font-semibold text-black">{{ $overrideCount }}</span>
            </p>
        </div>

        <div class="flex gap-3">
            @if($dirty)
                <span class="text-sm text-orange-600 font-medium">
                    Unsaved changes
                </span>
            @endif

            @unless($readOnly)
                <button
                    wire:click="resetToRole"
                    class="btn btn-outline-warning btn-sm">
                    Reset to Role
                </button>
            @endunless
        </div>
    </div>

    @if($readOnly)
        <div class="alert alert-warning text-sm">
            View-only access. Permissions inherited from role cannot be modified.
        </div>
    @endif

    {{-- =========================
     | SEARCH
     ========================= --}}
    <div class="flex justify-end">
        <input
            type="text"
            wire:model.debounce.300ms="search"
            placeholder="Search permissions..."
            class="form-control w-64"
        >
    </div>

    {{-- =========================
     | MATRIX
     ========================= --}}
    <div class="overflow-x-auto border rounded">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 sticky top-0 z-10">
            <tr>
                <th class="p-2 text-left">Module</th>

                @foreach(['view','create','edit','delete','restore'] as $action)
                    <th class="text-center">
                        <input
                            type="checkbox"
                            wire:click="toggleColumn('{{ $action }}')"
                            @checked($columnSelectAll[$action])
                            @disabled($readOnly)
                            title="Select all {{ $action }}"
                        >
                    </th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            @foreach($matrix as $group => $entities)
                <tr class="bg-gray-50">
                    <td colspan="6" class="px-3 py-2 font-semibold">
                        {{ $group }}
                    </td>
                </tr>

                @foreach($entities as $entity => $actions)
                    <tr>
                        <td class="px-3 py-2">{{ $entity }}</td>

                        @foreach($actions as $meta)
                            <td class="text-center">
                                <input
                                    type="checkbox"
                                    wire:click="togglePermission({{ $meta['id'] }})"
                                    @checked($meta['checked'])
                                    @disabled($readOnly || $meta['inherited'])
                                    title="{{ $meta['inherited'] ? 'Inherited from role' : '' }}"
                                    class="{{ $meta['inherited'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                >
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- =========================
     | ACTIONS
     ========================= --}}
    <div class="flex justify-end">
        <button
            wire:click="save"
            class="btn btn-primary"
            @disabled($readOnly || ! $dirty)
        >
            Save Overrides
        </button>
    </div>
</div>
