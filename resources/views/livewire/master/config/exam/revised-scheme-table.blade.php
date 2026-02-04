<div class="space-y-4">

    {{-- =========================
     | BREADCRUMB BAR
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'title' => 'Revised Scheme',
            'mode'  => $breadcrumbMode ?? 'View / Update',
        ])
    </div>

    {{-- =========================
     | PAGE TITLE ROW
     ========================= --}}
    <div class="flex justify-between items-center">
        <h1 class="font-normal text-black-400">
            Revised Scheme
        </h1>

        @if($canCreate)
            <button
                wire:click="create"
                class="dt-button buttons-collection btn btn-white btn-bold">
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add Revised Scheme
            </button>
        @endif
    </div>

    {{-- =========================
     | LIST TABLE
     ========================= --}}
    @include('livewire.master._crud-table', [
        'rows'              => $streams,
        'columns'           => $columns,
        'search'            => $search,
        'searchPlaceholder' => 'Search...',
        'canEdit'           => $canEdit,
        'canDelete'         => $canDelete,
        'canRestore'        => $canRestore,
    ])

    {{-- =========================
     | ADD / EDIT MODAL
     ========================= --}}
    <x-master.modal
        :show="$showModal"
        :title="$editingId ? 'Edit Revised Scheme' : 'Add Revised Scheme'"
        onSave="save"
        onCancel="cancelEdit"
    >
        <div class="modal-form">

            {{-- SCHEME NAME --}}
            <x-master.form-row
                label="Name"
                name="form.mas_revised_scheme_name"
                required
            >
                <input
                    type="text"
                    class="form-control"
                    wire:model.defer="form.mas_revised_scheme_name"
                    placeholder="Only letters, commas, and spaces are allowed"
                    oninput="this.value = this.value
                        .toUpperCase()
                        .replace(/[^A-Z, ]/g, '')"
                />
            </x-master.form-row>

            {{-- SCHEME CODE --}}
            <x-master.form-row
                label="Code"
                name="form.mas_revised_scheme_short_name"
                required
            >
                <input
                    type="text"
                    class="form-control"
                    wire:model.defer="form.mas_revised_scheme_short_name"
                    placeholder="Only letters & digits (no spaces)"
                    oninput="this.value = this.value
                        .toUpperCase()
                        .replace(/[^A-Z0-9]/g, '')"
                />
            </x-master.form-row>

            {{-- STATUS --}}
            @if($editingId)
                <x-master.form-row
                    label="Status"
                    name="form.mas_revised_scheme_status_id"
                >
                    <select
                        class="form-select"
                        wire:model.defer="form.mas_revised_scheme_status_id"
                    >
                        <option value="1">Active</option>
                        <option value="2">In Active</option>
                    </select>
                </x-master.form-row>
            @endif

        </div>
    </x-master.modal>

    {{-- DELETE MODAL --}}
    @if($showDeleteModal)
        <div class="admin-loader" x-cloak>
            <div class="admin-loader-box w-[420px]" role="dialog">

                <h6 class="mb-2 font-semibold text-lg text-gray-800">
                    <i class="fa fa-exclamation-triangle text-red-600 mr-1"></i>
                    Delete Record
                </h6>

                <p class="text-sm text-gray-600 mt-2">
                    Are you sure you want to delete this record?
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
