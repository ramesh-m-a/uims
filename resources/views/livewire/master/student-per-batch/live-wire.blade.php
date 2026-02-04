<div>
    <div class="mb-3 d-flex justify-content-between">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search subject/degree" class="form-control w-50" />
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#livewireModal">Add New</button>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Subject</th>
            <th>Degree</th>
            <th>Total</th>
            <th>Per Day</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->subject?->name ?? $row->mas_student_per_batch_subject_id }}</td>
                <td>{{ $row->degree?->name ?? $row->mas_student_per_batch_degree_id }}</td>
                <td>{{ $row->mas_student_per_batch_total_number }}</td>
                <td>{{ $row->mas_student_per_batch_per_day }}</td>
                <td>
                    <button wire:click="edit({{ $row->id }})" class="btn btn-sm btn-warning">Edit</button>
                    <button wire:click="delete({{ $row->id }})" class="btn btn-sm btn-danger"
                            onclick="confirm('Delete?') || event.stopImmediatePropagation()">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $rows->links() }}

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="livewireModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $editingId ? 'update' : 'store' }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingId ? 'Edit' : 'Add' }} Student Per Batch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Subject</label>
                            <select wire:model="subject_id" class="form-control">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Degree</label>
                            <select wire:model="degree_id" class="form-control">
                                <option value="">Select Degree</option>
                                @foreach($degrees as $d)
                                    <option value="{{ $d->mas_degree_id ?? $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @error('degree_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Total Students</label>
                            <input type="number" wire:model.defer="total_number" class="form-control" />
                            @error('total_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Per Day</label>
                            <input type="number" wire:model.defer="per_day" class="form-control" />
                            @error('per_day') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit">{{ $editingId ? 'Update' : 'Save' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // listen for showing modal when edit() is called
        window.addEventListener('openModal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('livewireModal'));
            myModal.show();
        });

        window.addEventListener('notify', event => {
            // simple toast using DOM
            const box = document.createElement('div');
            box.className = 'alert alert-' + (event.detail.type === 'success' ? 'success' : 'info');
            box.innerText = event.detail.message;
            document.querySelector('body').prepend(box);
            setTimeout(() => box.remove(), 2000);

            // close modal after operations
            const modalEl = document.getElementById('livewireModal');
            if (modalEl) {
                const bs = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                bs.hide();
            }
        });

        Livewire.on('refreshComponent', () => {
            // optional: react to custom emitted events
        });
    </script>
</div>
