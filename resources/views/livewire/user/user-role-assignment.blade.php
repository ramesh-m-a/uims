<div class="space-y-4">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <h1 class="text-lg font-semibold">
            Assign Roles
        </h1>

        <a href="{{ route('user.index') }}"
           class="btn btn-sm btn-secondary">
            ← Back to Users
        </a>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ACTION BUTTONS --}}
    <div class="flex gap-2">
        <button
            type="button"
            wire:click="selectAllRoles"
            class="btn btn-sm btn-outline-primary"
        >
            Select All
        </button>

        <button
            type="button"
            wire:click="clearRoles"
            class="btn btn-sm btn-outline-secondary"
        >
            Clear All
        </button>
    </div>

    {{-- ROLE LIST --}}
    <div class="admin-card">
        <form wire:submit.prevent="save">

            <table class="table table-bordered admin-table">
                <thead>
                <tr>
                    <th width="80" class="text-center">Assign</th>
                    <th>Role</th>
                    <th>Description</th>
                </tr>
                </thead>

                <tbody>
                @foreach($roles as $role)
                    @php
                        $isPrimary = $role->id === $user->user_role_id;
                    @endphp

                    <tr class="{{ $isPrimary ? 'bg-light' : '' }}">
                        <td class="text-center">
                            <input
                                type="checkbox"
                                wire:model="selectedRoles"
                                value="{{ $role->id }}"
                                {{ $isPrimary ? 'checked disabled' : '' }}
                            >
                        </td>

                        <td>
                            {{ $role->name }}

                            @if($isPrimary)
                                <span class="badge bg-primary ms-2">
                                    Primary
                                </span>
                            @endif
                        </td>

                        <td class="text-muted">
                            {{ $role->description ?? '—' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-3 flex justify-end gap-2">
                <button type="submit" class="btn btn-primary">
                    Save Roles
                </button>
            </div>

        </form>
    </div>

</div>
