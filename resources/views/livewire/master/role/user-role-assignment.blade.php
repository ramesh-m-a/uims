<div class="space-y-4">

    {{-- HEADER --}}
    <x-sub-header title="User » Role Assignment" subtitle="{{ $user->name }}">
<a href="{{ route('user.index') }}"
           class="text-sm text-gray-500">
            ← Back
        </a>
</x-sub-header>

    {{-- CARD --}}
    <div class="admin-card max-w-xl">

        @if (session()->has('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered admin-table">
            <thead>
            <tr>
                <th width="50"></th>
                <th>Role</th>
                <th>Description</th>
            </tr>
            </thead>

            <tbody>
            @foreach($roles as $role)
                <tr>
                    <td class="text-center">
                        <input
                            type="checkbox"
                            wire:model.defer="selectedRoles"
                            value="{{ $role->id }}"
                        >
                    </td>
                    <td class="font-medium">{{ $role->name }}</td>
                    <td class="text-muted">{{ $role->description ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{-- ACTION --}}
        <div class="flex justify-end mt-3">
            <button
                wire:click="save"
                class="btn btn-primary">
                <i class="fa fa-save"></i>
                Save Roles
            </button>
        </div>

    </div>
</div>
