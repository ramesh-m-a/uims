<div class="space-y-4">

    {{-- =========================
     | BREADCRUMB BAR
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'title' => 'IFS Code',
            'mode'  => $breadcrumbMode ?? 'View / Update',
        ])
    </div>

    {{-- =========================
     | PAGE TITLE ROW
     ========================= --}}
    <div class="flex justify-between items-center">

        <h1 class="font-normal text-black-400">
            IFS Code
        </h1>

        @if($canCreate)
            <button
                wire:click="create"
                class="dt-button buttons-collection btn btn-white btn-bold">
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add IFSC
            </button>
        @else
            <button
                class="dt-button buttons-collection btn btn-secondary"
                disabled>
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add IFSC
            </button>

            @if(!$branchesExist)
                <p class="text-xs text-gray-500 mt-1">
                    Please add at least one <strong>Bank Branch</strong> before creating IFS codes.
                </p>
            @endif
        @endif

    </div>

    {{-- =========================
     | LIST TABLE
     ========================= --}}
    @include('livewire.master._crud-table', [
        'rows'              => $rows,
        'columns'           => $columns,
        'search'            => $search,
        'searchPlaceholder' => 'Search IFSC...',
        'canEdit'           => $canEdit,
        'canDelete'         => $canDelete,
        'canRestore'        => $canRestore,
    ])

    {{-- =========================
     | ADD / EDIT MODAL
     ========================= --}}
    <x-master.modal
        :show="$showModal"
        :title="$editingId ? 'Edit IFS Code' : 'Add IFS Code'"
        onSave="save"
        onCancel="cancelEdit"
    >
        <div class="modal-form">

            {{-- BANK --}}
            <x-master.form-row
                label="Bank"
                name="form.mas_ifsc_bank_id"
                required
            >
                <select
                    class="form-select"
                    wire:model.live="form.mas_ifsc_bank_id"
                >
                    <option value="">— Select Bank —</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">
                            {{ $bank->mas_bank_name }}
                        </option>
                    @endforeach
                </select>
            </x-master.form-row>

            {{-- BRANCH (CASCADE) --}}
            <x-master.form-row
                label="Branch"
                name="form.branch_id"
                required
            >
                <select
                    class="form-select"
                    wire:model.defer="form.branch_id"
                    @disabled(empty($branches))
                >
                    <option value="">— Select Branch —</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch['id'] }}">
                            {{ $branch['branch_name'] }}
                        </option>
                    @endforeach
                </select>

                @if(empty($branches))
                    <p class="text-xs text-gray-500 mt-1">
                        Select a bank to load branches
                    </p>
                @endif
            </x-master.form-row>

            {{-- IFSC CODE --}}
            <x-master.form-row
                label="IFSC Code"
                name="form.mas_ifsc_number"
                required
            >
                <input
                    type="text"
                    class="form-control"
                    wire:model.defer="form.mas_ifsc_number"
                    placeholder="Enter IFSC Code"
                />
            </x-master.form-row>

            {{-- STATUS --}}
            @if($editingId)
                <x-master.form-row
                    label="Status"
                    name="form.mas_ifsccode_status_id"
                >
                    <select
                        class="form-select"
                        wire:model.defer="form.mas_ifsccode_status_id"
                    >
                        <option value="1">Active</option>
                        <option value="2">In Active</option>
                    </select>
                </x-master.form-row>
            @endif

        </div>
    </x-master.modal>

    {{-- =========================
     | DELETE MODAL
     ========================= --}}
    @if($showDeleteModal)
        <div class="admin-loader" x-cloak>
            <div class="admin-loader-box w-[420px]" role="dialog">

                <h6 class="mb-2 font-semibold text-lg text-gray-800">
                    <i class="fa fa-exclamation-triangle text-red-600 mr-1"></i>
                    Delete Record
                </h6>

                <p class="text-sm text-gray-600 mt-2">
                    Are you sure you want to delete this IFS Code?
                </p>

                <div class="flex justify-end gap-2 mt-5">
                    <button
                        type="button"
                        wire:click="$set('showDeleteModal', false)"
                        class="btn btn-secondary btn-sm">
                        Cancel
                    </button>

                    <button
                        type="button"
                        wire:click="deleteConfirmed"
                        class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Yes, Delete
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
