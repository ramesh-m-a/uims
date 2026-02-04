<div>
    <div class="d-flex mb-3 justify-content-between align-items-center">
        <div class="d-flex gap-2 w-50">
            <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Search subject, degree, total, per day or status">
            <select wire:model="perPage" class="form-select" style="width:120px;">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>

        <div>
            <button class="btn btn-primary" wire:click="createShowModal">Add New</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th style="width:60px;">#</th>
                <th wire:click="sortBy('subject')" style="cursor:pointer;">
                    Subject
                    @if($sortField === 'subject')
                        @if($sortDirection === 'asc') ▲ @else ▼ @endif
                    @endif
                </th>
                <th wire:click="sortBy('degree')" style="cursor:pointer;">
                    Degree
                    @if($sortField === 'degree')
                        @if($sortDirection === 'asc') ▲ @else ▼ @endif
                    @endif
                </th>
                <th wire:click="sortBy('mas_student_per_batch_total_number')" style="cursor:pointer;">
                    Total
                    @if($sortField === 'mas_student_per_batch_total_number')
                        @if($sortDirection === 'asc') ▲ @else ▼ @endif
                    @endif
                </th>
                <th wire:click="sortBy('mas_student_per_batch_per_day')" style="cursor:pointer;">
                    Per Day
                    @if($sortField === 'mas_student_per_batch_per_day')
                        @if($sortDirection === 'asc') ▲ @else ▼ @endif
                    @endif
                </th>
                <th wire:click="sortBy('mas_student_per_batch_status_id')" style="cursor:pointer;">
                    Status
                    @if($sortField === 'mas_student_per_batch_status_id')
                        @if($sortDirection === 'asc') ▲ @else ▼ @endif
                    @endif
                </th>
                <th style="width:140px;">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->subject?->name ?? $row->mas_student_per_batch_subject_id }}</td>
                    <td>{{ $row->degree?->name ?? $row->mas_student_per_batch_degree_id }}</td>
                    <td>{{ $row->mas_student_per_batch_total_number }}</td>
                    <td>{{ $row->mas_student_per_batch_per_day }}</td>
                    <td>{{ $row->mas_student_per_batch_status_id }}</td>
                    <td>
                        <button wire:click="editShowModal({{ $row->id }})" class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="confirmDelete({{ $row->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No records found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div>Showing {{ $rows->firstItem() ?? 0 }} to {{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }} entries</div>
        <div>{{ $rows->links() }}</div>
    </div>

    <!-- Form Modal -->
    <div wire:ignore.self class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="save">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingId ? 'Edit' : 'Add' }} Student Per Batch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <select wire:model="subject_id" class="form-select">
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Degree</label>
                            <select wire:model="degree_id" class="form-select">
                                <option value="">-- Select Degree --</option>
                                @foreach($degrees as $d)
                                    <option value="{{ $d->mas_degree_id ?? $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @error('degree_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Students</label>
                            <input type="number" wire:model.defer="total_number" class="form-control" />
                            @error('total_number') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Per Day</label>
                            <input type="number" wire:model.defer="per_day" class="form-control" />
                            @error('per_day') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">{{ $editingId ? 'Update' : 'Save' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div wire:ignore.self class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p class="mb-3">Are you sure you want to delete?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" wire:click="delete" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // listen for Livewire events (use after Livewire scripts)
        Livewire.on('openModal', modalId => {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });

        Livewire.on('closeModal', modalId => {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();
        });

        Livewire.on('notify', payload => {
            const message = typeof payload === 'string' ? payload : (payload.message || '');
            const type = (payload && payload.type === 'success') ? 'success' : 'info';
            const el = document.createElement('div');
            el.className = 'alert alert-' + type + ' position-fixed top-0 end-0 m-3';
            el.style.zIndex = 9999;
            el.innerText = message;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 2000);
        });
    </script>
</div>
