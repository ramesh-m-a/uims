<div class="space-y-4">

    {{-- BREADCRUMB --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'title' => 'Bank Branch',
            'mode'  => $breadcrumbMode ?? 'View / Update',
        ])
    </div>

    {{-- TITLE + ADD --}}
    <div class="flex justify-between items-center">
        <h1 class="font-normal text-black-400">Bank Branch</h1>

        @if($canCreate)
            <button wire:click="create"
                    class="dt-button buttons-collection btn btn-white btn-bold">
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add Branch
            </button>
        @else
            <button class="dt-button buttons-collection btn btn-secondary" disabled>
                Add Branch
            </button>

            @if(!$banksExist)
                <p class="text-xs text-gray-500 mt-1">
                    Please add at least one <strong>Bank</strong> before creating branches.
                </p>
            @endif
        @endif
    </div>

    {{-- TABLE --}}
    @include('livewire.master._crud-table', [
        'rows'        => $rows,
        'columns'     => $columns,
        'search'      => $search,
        'canEdit'     => $canEdit,
        'canDelete'   => $canDelete,
        'canRestore'  => $canRestore,
    ])

    {{-- MODAL --}}
    <x-master.modal
        :show="$showModal"
        :title="$editingId ? 'Edit Branch' : 'Add Branch'"
        onSave="save"
        onCancel="cancelEdit"
    >
        <div class="modal-form">

            <x-master.form-row label="Bank" name="form.bank_id" required>
                <select class="form-select" wire:model.defer="form.bank_id">
                    <option value="">— Select Bank —</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">
                            {{ $bank->mas_bank_name }}
                        </option>
                    @endforeach
                </select>
            </x-master.form-row>

            <x-master.form-row label="Branch Name" name="form.branch_name" required>
                <input class="form-control"
                       wire:model.defer="form.branch_name" />
            </x-master.form-row>

            <x-master.form-row label="City" name="form.branch_city">
                <input class="form-control"
                       wire:model.defer="form.branch_city" />
            </x-master.form-row>

            <x-master.form-row label="State" name="form.branch_state">
                <input class="form-control"
                       wire:model.defer="form.branch_state" />
            </x-master.form-row>

            @if($editingId)
                <x-master.form-row label="Status" name="form.status_id">
                    <select class="form-select" wire:model.defer="form.status_id">
                        <option value="1">Active</option>
                        <option value="2">In Active</option>
                    </select>
                </x-master.form-row>
            @endif

        </div>
    </x-master.modal>

    {{-- DELETE --}}
    @if($showDeleteModal)
        <div class="admin-loader" x-cloak>
            <div class="admin-loader-box w-[420px]">
                <h6 class="font-semibold text-lg">
                    <i class="fa fa-exclamation-triangle text-red-600"></i>
                    Delete Branch
                </h6>

                <p class="text-sm mt-2">
                    Are you sure you want to delete this branch?
                </p>

                <div class="flex justify-end gap-2 mt-5">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="btn btn-secondary btn-sm">
                        Cancel
                    </button>

                    <button wire:click="deleteConfirmed"
                            class="btn btn-danger btn-sm">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
