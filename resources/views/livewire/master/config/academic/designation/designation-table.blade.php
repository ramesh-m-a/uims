<div class="space-y-4">
{{--    @dd($designations->first()->stream ?? 'NO STREAM')--}}
    {{-- =========================
     | BREADCRUMB BAR
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'title' => 'Designation',
            'mode'  => $breadcrumbMode ?? 'View / Update',
        ])
    </div>

    {{-- =========================
     | PAGE TITLE ROW
     ========================= --}}
    <div class="flex justify-between items-center">
        <h1 class="font-normal text-black-400">
            Designation
        </h1>

        @if($canCreate)
            <button
                wire:click="create"
                class="dt-button buttons-collection btn btn-white btn-bold">
                <i class="fa fa-plus-square bigger-110 add-button-padding"></i>
                Add Designation
            </button>
        @endif
    </div>

    {{-- =========================
     | LIST TABLE
     ========================= --}}
    @include('livewire.master._crud-table', [
        'rows'              => $designations,
        'columns'           => $this->columns(),
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
        :title="$editingId ? 'Edit Designation' : 'Add Designation'"
        onSave="save"
        onCancel="cancelEdit"
    >
        <div class="modal-form">

            {{-- STREAMS (Chosen-style UX without jQuery) --}}
            <x-master.form-row
                label="Streams"
                name="form.streams"
                required
            >
                <div
                    x-data="{
                        open: false,
                        search: '',
                        options: @js($streams->map(fn($s) => ['partials' => $s->id, 'name' => $s->mas_stream_name])),
                        selected: @entangle('form.streams'),

                        get filtered() {
                            if (!this.search) return this.options;
                            return this.options.filter(o =>
                                o.name.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },

                       toggle(id) {
                            const index = this.selected.indexOf(id);

                            if (index !== -1) {
                                this.selected.splice(index, 1);   // mutate instead of replace
                            } else {
                                this.selected.push(id);           // OK (mutates)
                            }
                        },

                        label(id) {
                            return this.options.find(o => o.id === id)?.name ?? id;
                        }
                    }"
                    class="relative space-y-2"
                >

                    {{-- Selected tags --}}
                    <div class="flex flex-wrap gap-2 border rounded p-2 min-h-[42px] cursor-text"
                         @click="open = true">
                        <template x-for="id in selected" :key="id">
                            <span class="bg-gray-200 px-2 py-1 rounded text-sm flex items-center gap-1">
                                <span x-text="label(id)"></span>
                                <button type="button"
                                        @click.stop="toggle(id)"
                                        class="text-red-500 hover:text-red-700">
                                    &times;
                                </button>
                            </span>
                        </template>

                        <span x-show="selected.length === 0"
                              class="text-gray-400 text-sm">
                            Choose Stream(s)...
                        </span>
                    </div>

                    {{-- Dropdown --}}
                    <div x-show="open" @click.outside="open=false"
                         class="absolute z-50 bg-white border rounded w-full shadow p-2 space-y-2">

                        <input
                            type="text"
                            x-model="search"
                            placeholder="Search streams..."
                            class="form-control form-control-sm"
                        >

                        <div class="max-h-48 overflow-y-auto space-y-1">
                            <template x-for="item in filtered" :key="item.id">
                                <div
                                    @click="toggle(item.id)"
                                    class="px-2 py-1 rounded cursor-pointer hover:bg-gray-100 flex justify-between"
                                    :class="selected.includes(item.id) ? 'bg-blue-50 font-medium' : ''"
                                >
                                    <span x-text="item.name"></span>
                                    <span x-show="selected.includes(item.id)">✓</span>
                                </div>
                            </template>

                            <div x-show="filtered.length === 0"
                                 class="text-sm text-gray-500">
                                No results
                            </div>
                        </div>
                    </div>

                    {{-- Validation --}}
                  {{--  @error('form.streams')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>--}}
            </x-master.form-row>

            {{-- DESIGNATION NAME --}}
            <x-master.form-row
                label="Name"
                name="form.mas_designation_name"
                required
            >
                <input
                    type="text"
                    class="form-control"
                    wire:model.defer="form.mas_designation_name"
                    placeholder="Only letters, commas, and spaces are allowed"
                    oninput="this.value = this.value
                        .toUpperCase()
                        .replace(/[^A-Z, ]/g, '')"
                />
            </x-master.form-row>

            {{-- STATUS --}}
            @if($editingId)
                <x-master.form-row
                    label="Status"
                    name="form.mas_designation_status_id"
                >
                    <select
                        class="form-select"
                        wire:model.defer="form.mas_designation_status_id"
                    >
                        <option value="1">Active</option>
                        <option value="2">In Active</option>
                    </select>
                </x-master.form-row>
            @endif

        </div>
    </x-master.modal>

</div>
