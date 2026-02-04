<div class="space-y-4">

    {{-- =========================
     | BREADCRUMB BAR
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => $pageRoute,
            'title' => 'College',
            'mode'  => $this->breadcrumbMode() ?? 'View / Update'
        ])
    </div>

    {{-- =========================
     | PAGE TITLE ROW
     ========================= --}}
    <x-sub-header title="College" subtitle="View / Update">
@if($this->can('create'))
            <button
                wire:click="create"
                class="dt-button buttons-collection btn btn-white btn-bold"
            >
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add College
            </button>
        @endif
</x-sub-header>

    {{-- =========================
     | LIST TABLE
     ========================= --}}
    @include('livewire.master._crud-table', [
        'rows'        => $colleges,
        'columns'     => $this->columns(),
        'searchPlaceholder' => 'Search...',
        'canEdit'     => $this->can('edit'),
        'canDelete'   => $this->can('delete'),
        'canRestore'  => $this->can('restore'),
    ])

    {{-- =========================
     | ADD / EDIT MODAL
     ========================= --}}
    <x-master.modal
        :show="$showModal"
        :title="$editingId ? 'Edit College' : 'Add College'"
        onSave="save"
        onCancel="cancelEdit"
    >
        <div class="modal-form">

            {{-- STREAM (FK) --}}
            <x-master.form-row
                label="Stream"
                name="form.mas_college_stream_id"
                required
            >
                <select
                    class="form-select"
                    wire:model.defer="form.mas_college_stream_id"
                >
                    <option value="">-- Select Stream --</option>
                    @foreach($streams as $stream)
                        <option value="{{ $stream->id }}">
                            {{ $stream->mas_stream_name }}
                        </option>
                    @endforeach
                </select>
            </x-master.form-row>

            {{-- CODE --}}
            <x-master.form-row
                label="Code"
                name="form.mas_college_code"
                required
            >
                <input
                    type="text"
                    class="form-control"
                    wire:model.defer="form.mas_college_code"
                    placeholder="Only letters & digits (no spaces)"
                    oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')"
                />
            </x-master.form-row>

            {{-- NAME --}}
            <x-master.form-row
                label="Name"
                name="form.mas_college_name"
                required
            >
                <input
                    type="text"
                    class="form-control"
                    wire:model.defer="form.mas_college_name"
                />
            </x-master.form-row>

            {{-- EXAM CENTRE --}}
            <x-master.form-row
                label="Exam Centre"
                name="form.mas_college_exam_centre"
            >
                <select
                    class="form-select"
                    wire:model.defer="form.mas_college_exam_centre"
                >
                    <option value="">-- Choose --</option>
                    <option value="1">YES</option>
                    <option value="0">NO</option>
                </select>
            </x-master.form-row>

            {{-- INTERNAL --}}
            <x-master.form-row
                label="Internal"
                name="form.mas_college_is_internal"
            >
                <select
                    class="form-select"
                    wire:model.defer="form.mas_college_is_internal"
                >
                    <option value="">-- Choose --</option>
                    <option value="1">YES</option>
                    <option value="0">NO</option>
                </select>
            </x-master.form-row>

        </div>
    </x-master.modal>

</div>
