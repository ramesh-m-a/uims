<div class="space-y-4">

    {{-- =========================
     | BREADCRUMB BAR
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => $pageRoute,
            'title' => 'Role',
            'mode'  => $showModal
                ? ($editingId ? 'Edit' : 'Add')
                : 'View / Update'
        ])
    </div>

    {{-- =========================
     | PAGE TITLE ROW
     ========================= --}}
    <x-sub-header title="Role" subtitle="View / Update">
@if($this->can('create'))
            <button
                wire:click="create"
                class="dt-button buttons-collection btn btn-white btn-bold"
            >
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add Role
            </button>
        @endif
</x-sub-header>

    {{-- =========================
     | LIST TABLE
     ========================= --}}
    @include('livewire.master._crud-table', [
        'rows'              => $rows,
        'columns'           => $this->columns(),
        'searchPlaceholder' => 'Search roles...',
        'canEdit'           => $this->can('edit'),
        'canDelete'         => $this->can('delete'),
        'canRestore'        => $this->can('restore'),

        // ROLE → PERMISSIONS
        'showPermissions'   => true,
        'permissionsRoute'  => 'master.role.permissions',
        'canPermissions'    => $this->can('edit'),
    ])

    {{-- =========================
     | ADD / EDIT MODAL
     ========================= --}}
    <x-master.modal
        :show="$showModal"
        :title="$editingId ? 'Edit Role' : 'Add Role'"
        onSave="save"
        onCancel="cancelEdit"
    >

        {{-- ROLE NAME --}}
        <x-master.input
            label="Role Name"
            model="form.name"
            placeholder="e.g. admin, super-admin"
            required
            autofocus
            uppercase
        />

        {{-- DESCRIPTION --}}
        <x-master.input
            label="Description"
            model="form.description"
            placeholder="Optional description"
        />

        {{-- 🔥 PARENT ROLE --}}
        <div>
            <label class="form-label">Parent Role</label>
            <select
                wire:model.defer="form.parent_role_id"
                class="form-select"
                @disabled($lockParent)
            >
                <option value="">— None (Top Level) —</option>

                @foreach($parentRoles as $parent)
                    <option value="{{ $parent->id }}">
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>

            @if($lockParent)
                <p class="text-xs text-gray-500 mt-1">
                    Parent role cannot be changed after creation.
                </p>
            @endif
        </div>

        {{-- STATUS --}}
        <div>
            <label class="form-label">Status</label>
            <select
                wire:model.defer="form.roles_status_id"
                class="form-select"
            >
                <option value="1">Active</option>
                <option value="2">In Active</option>
            </select>
        </div>

    </x-master.modal>

</div>
