<div class="space-y-4">

    {{-- =========================
     | BREADCRUMB
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'title' => 'Degree Stream Mapping',
            'mode'  => $breadcrumbMode ?? 'View / Update',
        ])
    </div>

    {{-- =========================
     | HEADER
     ========================= --}}
    <div class="flex justify-between items-center">
        <h1 class="font-normal text-black-400">
            Degree Stream Mapping
        </h1>

        @if($canCreate)
            <button
                wire:click="create"
                class="dt-button buttons-collection btn btn-white btn-bold">
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add Mapping
            </button>
        @endif
    </div>

    {{-- =========================
     | TABLE
     ========================= --}}
    @include('livewire.master._crud-table', [
        'rows'      => $rows,
        'columns'   => $columns,
        'search'    => $search,
        'canEdit'   => $canEdit,
        'canDelete' => $canDelete,
    ])

    {{-- =========================
     | MODAL
     ========================= --}}
    <x-master.modal
        :show="$showModal"
        :title="$editingId ? 'Edit Mapping' : 'Add Mapping'"
        onSave="save"
        onCancel="cancelEdit"
    >
        <div class="modal-form">

            {{-- DEGREE --}}
            <x-master.form-row label="Degree" name="form.mas_degree_id" required>
                <select class="form-select" wire:model.defer="form.mas_degree_id">
                    <option value="">Select Degree</option>
                    @foreach($degrees as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </x-master.form-row>

            {{-- STREAM --}}
            <x-master.form-row label="Stream" name="form.mas_stream_id" required>
                <select class="form-select" wire:model.defer="form.mas_stream_id">
                    <option value="">Select Stream</option>
                    @foreach($streams as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </x-master.form-row>

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
                    Delete Mapping
                </h6>

                <p class="text-sm text-gray-600 mt-2">
                    Are you sure you want to delete this mapping?
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
