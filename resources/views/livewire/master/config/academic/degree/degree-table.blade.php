<div class="space-y-4">

    {{-- =========================
     | BREADCRUMB
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'title' => 'Degree',
            'mode'  => $breadcrumbMode ?? 'View / Update',
        ])
    </div>

    {{-- =========================
     | HEADER
     ========================= --}}
    <div class="flex justify-between items-center">
        <h1 class="font-normal text-black-400">Degree</h1>

        @if($canCreate)
            <button
                wire:click="create"
                class="dt-button buttons-collection btn btn-white btn-bold">
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add Degree
            </button>
        @endif
    </div>

    {{-- =========================
     | TABLE
     ========================= --}}
    @include('livewire.master._crud-table', [
        'rows'      => $degrees,
        'columns'           => $this->columns(),
        'search'    => $search,
        'searchPlaceholder' => 'Search...',
        'canEdit'   => $canEdit,
        'canDelete' => $canDelete,
        'canRestore'        => $canRestore,
    ])

    {{-- =========================
     | MODAL
     ========================= --}}
    <x-master.modal
        :show="$showModal"
        :title="$editingId ? 'Edit Degree' : 'Add Degree'"
        onSave="save"
        onCancel="cancelEdit"
    >
        <div class="modal-form space-y-3">

            {{-- DEGREE CODE --}}
            <x-master.form-row label="Degree Code" name="form.mas_degree_code" required>
                <input
                    type="text"
                    class="form-control"
                    wire:model.defer="form.mas_degree_code"
                    placeholder="Eg: MBBS, MD, MS"
                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')"
                />
            </x-master.form-row>

            {{-- DEGREE NAME --}}
            <x-master.form-row label="Degree Name" name="form.mas_degree_name" required>
                <input
                    type="text"
                    class="form-control"
                    wire:model.defer="form.mas_degree_name"
                    placeholder="Eg: Bachelor of Medicine"
                />
            </x-master.form-row>

            {{-- DEGREE LEVEL --}}
            <x-master.form-row label="Degree Level" name="form.mas_degree_level_id" required>
                <select class="form-select" wire:model.defer="form.mas_degree_level_id">
                    <option value="">Select Level</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">
                            {{ $level->mas_degree_level_name }}
                        </option>
                    @endforeach
                </select>
            </x-master.form-row>

            {{-- STREAMS (MULTI SELECT) --}}
            <x-master.form-row label="Streams" name="form.streams" required>
                <div class="border rounded p-2 max-h-48 overflow-y-auto space-y-1">

                    @foreach($streams as $stream)
                        <label class="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                wire:model.defer="form.streams"
                                value="{{ $stream->id }}"
                            >
                            {{ $stream->mas_stream_name }}
                        </label>
                    @endforeach

                </div>
            </x-master.form-row>

            {{-- SPECIALISATION MODE --}}
            <x-master.form-row label="Specialisation Mode" name="form.mas_degree_specialisation_mode" required>
                <select class="form-select" wire:model.defer="form.mas_degree_specialisation_mode">
                    <option value="0">None</option>
                    <option value="1">Optional</option>
                    <option value="2">Mandatory</option>
                </select>
            </x-master.form-row>

            {{-- STATUS (ONLY WHILE EDITING) --}}
            @if($editingId)
                <x-master.form-row label="Status" name="form.mas_degree_status_id">
                    <select class="form-select" wire:model.defer="form.mas_degree_status_id">
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
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
                    Delete Degree
                </h6>

                <p class="text-sm text-gray-600 mt-2">
                    Are you sure you want to delete this degree?
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
