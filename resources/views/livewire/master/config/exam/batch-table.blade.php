<div>
    <div class="space-y-4">

        <div class="breadcrumb-bar">
            @include('partials.breadcrumb', [
                'route' => $pageRoute,
                'title' => 'Batch',
                'mode'  => $this->breadcrumbMode() ?? 'View / Update'
            ])
        </div>

        <div class="flex justify-between items-center">
            <h1 class="font-normal text-black-400">
               Batch
            </h1>

            <div class="flex gap-2">
                <button wire:click="create" class="btn btn-white">Add Batch</button>
                <button wire:click="$dispatch('open-upload-modal')" class="btn btn-white">
                    Upload Eligible Student Details
                </button>
            </div>
        </div>

        @include('livewire.master._crud-table', [
            'rows'    => $batches,
            'columns' => $this->columns(),
            'searchPlaceholder' => 'Search...',
            'canEdit' => $this->can('edit'),
            'canDelete' => $this->can('delete'),
        ])

        @if($showAllocationModal)
            <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="min-h-screen flex items-start justify-center pt-10">
                <div class="bg-white rounded-xl w-[950px] p-6 space-y-6 shadow-xl border">

                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold">Edit Batch Allocation</h2>
                        <button wire:click="$set('showAllocationModal', false)">✕</button>
                    </div>

                    {{-- Allocation List --}}
                    <div class="max-h-[300px] overflow-y-auto space-y-2">
                        @foreach($allocation as $centreId => $count)
                            <div class="flex items-center gap-3 border rounded px-3 py-2
                                {{ $highlightCentreId === (int)$centreId ? 'border-yellow-400 bg-yellow-50' : '' }}">
                                <div class="flex-1 text-sm">
                                    {{ \App\Models\Master\Config\Academic\College::find($centreId)?->mas_college_name }}
                                </div>

                                <input type="number"
                                       wire:model.defer="allocation.{{ $centreId }}"
                                       class="border px-2 py-1 w-28 rounded">

                                <button wire:click="removeCollege({{ $centreId }})"
                                        class="text-red-600 font-bold">✕</button>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-right font-semibold text-lg">
                        Total Students: {{ $this->computedTotal }}
                    </div>

                    {{-- Add --}}
                    <div class="flex gap-2 items-center border-t pt-4">
                        <select wire:model="newCollegeId" class="form-select w-1/2">
                            <option value="">Select college</option>
                            @foreach($colleges as $college)
                                @if(!array_key_exists($college->id, $allocation))
                                    <option value="{{ $college->id }}">
                                        {{ $college->mas_college_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>

                        <input type="number"
                               wire:model.defer="newCollegeCount"
                               min="1"
                               placeholder="Students"
                               class="form-control w-40">

                        <button type="button"
                                wire:click="addCollege"
                                class="btn btn-primary">
                            Add
                        </button>
                    </div>

                    {{-- Meta --}}
                    <div class="grid grid-cols-3 gap-4 border-t pt-6">

                        <div>
                            <label class="text-sm">Month</label>
                            <select wire:model="form.mas_batch_month_id" class="form-select">
                                <option value="">Select Month</option>
                                @foreach($months as $month)
                                    <option value="{{ $month->id }}">{{ $month->mas_month_name }}</option>
                                @endforeach
                            </select>
                            @error('form.mas_batch_month_id')
                            <div class="text-xs text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm">Exam Start Date</label>
                            <input type="date" wire:model="form.mas_batch_start_date" class="form-control">
                            @error('form.mas_batch_start_date')
                            <div class="text-xs text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm">Status</label>
                            <select wire:model="form.mas_batch_status_id" class="form-select">
                                <option value="">Select Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->mas_status_name }}</option>
                                @endforeach
                            </select>
                            @error('form.mas_batch_status_id')
                            <div class="text-xs text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t">
                        <button wire:click="$set('showAllocationModal', false)" class="btn btn-secondary">Cancel</button>
                        <button wire:click="saveAllocation" class="btn btn-primary">Save Changes</button>
                    </div>

                </div>
            </div>
        </div>
    @endif

        <livewire:master.config.exam.batch-upload />

    </div>
</div>
