<div class="space-y-4">

    {{-- =========================
     | BREADCRUMB BAR
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => $pageRoute,
            'title' => 'User',
            'mode'  => 'View / Update'
        ])
    </div>

    {{-- =========================
     | PAGE TITLE ROW
     ========================= --}}
    <x-sub-header title="User" subtitle="View / Update">
@if($this->can('create'))
            <button wire:click="create"
                    class="btn btn-primary">
                <i class="fa fa-plus"></i> Add User
            </button>
        @endif
</x-sub-header>

    {{-- =========================
     | LIST TABLE
     ========================= --}}
    @include('livewire.master._crud-table', [
        'rows'            => $rows,
        'columns'         => $this->columns(),
        'canEdit'         => $this->can('edit'),
        'canDelete'       => false,
        'canRestore'      => false,
        'canPermissions'  => $this->can('edit'),
        'isUserTable'     => true,   // 🔥 CRITICAL
    ])

    {{-- =========================
     | ADD / EDIT MODAL
     ========================= --}}
    <x-master.modal-row
        :show="$showModal"
        :title="$editingId ? 'Edit User' : 'Add User'"
        onSave="syncAndSave"
        onCancel="cancelEdit"
    >
        <div class="space-y-3">

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM FIELDS --}}
            <div class="form-group">
                <label class="required">Mobile</label>
                <input class="form-control" wire:model.defer="form.mobile">
            </div>

            <div class="form-group">
                <label class="required">Email</label>
                <input class="form-control" wire:model.defer="form.email">
            </div>

            <div class="form-group">
                <label class="required">First Name</label>
                <input class="form-control" wire:model.defer="form.first_name">
            </div>

            <div class="form-group">
                <label>Middle Name</label>
                <input class="form-control" wire:model.defer="form.middle_name">
            </div>

            <div class="form-group">
                <label class="required">Last Name</label>
                <input class="form-control" wire:model.defer="form.last_name">
            </div>

            <div class="form-group">
                <label class="required">Stream</label>
                <select class="form-control" wire:model.defer="form.user_stream_id">
                    <option value="">-- Select Stream --</option>
                    @foreach($streams as $s)
                        <option value="{{ $s->id }}">{{ $s->mas_stream_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="required">College</label>
                <select class="form-control" wire:model.defer="form.user_college_id">
                    <option value="">-- Select College --</option>
                    @foreach($colleges as $c)
                        <option value="{{ $c->id }}">{{ $c->mas_college_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="required">Designation</label>
                <select class="form-control" wire:model.defer="form.user_designation_id">
                    <option value="">-- Select Designation --</option>
                    @foreach($designations as $d)
                        <option value="{{ $d->id }}">{{ $d->mas_designation_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="required">Primary Role</label>
                <select class="form-control" wire:model.defer="form.user_role_id">
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="required">Status</label>
                <select class="form-control" wire:model.defer="form.user_status_id">
                    @foreach($statuses as $s)
                        <option value="{{ $s->id }}">{{ $s->mas_status_name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </x-master.modal-row>

</div>
