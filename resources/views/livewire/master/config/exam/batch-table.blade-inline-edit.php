<div>
    <div class="space-y-4">

        {{-- Breadcrumb + header untouched --}}
        <div class="breadcrumb-bar">
            @include('partials.breadcrumb', [
                'route' => $pageRoute,
                'title' => 'Batch',
                'mode'  => $this->breadcrumbMode() ?? 'View / Update'
            ])
        </div>

        <div class="flex justify-between items-center">
            <h1 class="font-normal text-black-400">
                Examiner » Batch » View / Update
            </h1>

            @if($this->can('create'))
                <div class="flex gap-2">
                    <button wire:click="create" class="dt-button btn btn-white">Add Batch</button>
                    <button wire:click="$dispatch('open-upload-modal')" class="dt-button btn btn-white">
                        Upload Eligible Student Details
                    </button>
                </div>
            @endif
        </div>

        {{-- GRID --}}
        <table class="w-full border text-sm">
            <thead>
            <tr>
                <th>Stream</th>
                <th>Year</th>
                <th>Month</th>
                <th>Subject</th>
                <th>Centre</th>
                <th>Attached Colleges</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach($batches as $batch)
                <tr class="border-t align-top">
                    <td>{{ $batch->stream?->mas_stream_name }}</td>
                    <td>{{ $batch->year?->mas_year_year }}</td>
                    <td>{{ $batch->month?->mas_month_name }}</td>
                    <td>{{ $batch->subject?->mas_subject_name }}</td>
                    <td>{{ $batch->centre?->mas_college_name }}</td>

                    {{-- ATTACHED COLLEGES --}}
                    <td>
                        @if($editingCentresBatchId === $batch->id)
                            <div class="space-y-2">
                                @foreach($centreEditor as $i => $row)
                                    <div class="flex gap-2 items-center">
                                        <select wire:model="centreEditor.{{ $i }}.mas_centre_id" class="border px-2 py-1">
                                            <option value="">-- Select --</option>
                                            @foreach($colleges as $c)
                                                <option value="{{ $c->id }}">{{ $c->mas_college_name }}</option>
                                            @endforeach
                                        </select>

                                        <input type="number"
                                               wire:model="centreEditor.{{ $i }}.student_count"
                                               class="border px-2 py-1 w-24"/>

                                        <button wire:click="removeCentreRow({{ $i }})">✕</button>
                                    </div>
                                @endforeach

                                <button wire:click="addCentreRow" class="text-blue-600 text-sm">+ Add College</button>

                                <div class="flex gap-2 mt-2">
                                    <button wire:click="saveCentres({{ $batch->id }})" class="btn btn-primary">Save</button>
                                    <button wire:click="cancelCentres" class="btn btn-secondary">Cancel</button>
                                </div>
                            </div>
                        @else
                            {!! $batch->mas_batch_attached_centre_id !!}
                        @endif
                    </td>

                    <td><strong>{{ $batch->mas_batch_total_students }}</strong></td>

                    <td>
                        @if($editingCentresBatchId !== $batch->id)
                            <button wire:click="editCentres({{ $batch->id }})">Edit</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <livewire:master.config.exam.batch-upload />
    </div>
</div>
