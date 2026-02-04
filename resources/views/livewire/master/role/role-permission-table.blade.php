<div class="space-y-6">

    {{-- =========================
 | BREADCRUMB BAR
 ========================= --}}
    <div class="breadcrumb-bar mb-4">
        @include('partials.breadcrumb', [
            'route' => route('master.role.index'),
            'title' => 'Role',
            'mode'  => 'Permissions'
        ])
    </div>

    {{-- =========================
     | HEADER
     ========================= --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold">
                Role Permissions
            </h2>
            <p class="text-sm text-gray-500">
                Managing permissions for role:
                <span class="font-medium text-black">
                    {{ $role->name }}
                </span>
            </p>
        </div>

        @if($dirty)
            <span class="text-sm text-orange-600 font-medium">
                Unsaved changes
            </span>
        @endif
    </div>

    {{-- =========================
     | READ ONLY WARNING
     ========================= --}}
    @if($readOnly)
        <div class="rounded border border-yellow-300 bg-yellow-50 px-4 py-2 text-sm">
            You have view-only access. Changes are disabled.
        </div>
    @endif

    {{-- =========================
     | PERMISSION TABLE
     ========================= --}}
    <div class="overflow-x-auto border rounded">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">Module</th>
                @foreach(array_keys($columnSelectAll) as $action)
                    <th class="p-2 text-center">
                        <input
                            type="checkbox"
                            wire:click="toggleColumn('{{ $action }}')"
                            @disabled($readOnly)
                            @checked($columnSelectAll[$action])
                        >
                        <div class="text-xs capitalize">{{ $action }}</div>
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
                        <td class="px-3 py-2">
                            {{ $entity }}
                        </td>

                        @foreach($actions as $action => $meta)
                            <td class="text-center">
                                <input
                                    type="checkbox"
                                    wire:click="togglePermission({{ $meta['id'] }})"
                                    @checked($meta['checked'])
                                    @disabled($readOnly || $meta['inherited'])
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
     | FOOTER ACTIONS
     ========================= --}}
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600">
            Effective permissions:
            <span class="font-medium">
                {{ collect($this->permissionSummary())->flatten()->unique()->count() }}
            </span>
        </div>

        <button
            wire:click="save"
            class="px-4 py-2 rounded bg-black text-white disabled:opacity-50"
            @disabled($readOnly || ! $dirty)
        >
            Save Permissions
        </button>
    </div>
</div>
