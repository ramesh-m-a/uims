@php
    // Ensure the blade always has these variables even when rendered outside Livewire
    $items = $items ?? (isset($this) && property_exists($this, 'items') ? $this->items : collect());
    $mas_student_per_batch_id = $mas_student_per_batch_id ?? (isset($this) && property_exists($this, 'mas_student_per_batch_id') ? $this->mas_student_per_batch_id : null);
    $mas_student_per_batch_subject_id = $mas_student_per_batch_subject_id ?? (isset($this) && property_exists($this, 'mas_student_per_batch_subject_id') ? $this->mas_student_per_batch_subject_id : null);
    $mas_student_per_batch_degree_id = $mas_student_per_batch_degree_id ?? (isset($this) && property_exists($this, 'mas_student_per_batch_degree_id') ? $this->mas_student_per_batch_degree_id : null);
    $mas_student_per_batch_total_number = $mas_student_per_batch_total_number ?? (isset($this) && property_exists($this, 'mas_student_per_batch_total_number') ? $this->mas_student_per_batch_total_number : null);
    $mas_student_per_batch_per_day = $mas_student_per_batch_per_day ?? (isset($this) && property_exists($this, 'mas_student_per_batch_per_day') ? $this->mas_student_per_batch_per_day : null);
    $mas_student_per_batch_status_id = $mas_student_per_batch_status_id ?? (isset($this) && property_exists($this, 'mas_student_per_batch_status_id') ? $this->mas_student_per_batch_status_id : 50);
@endphp

<div>
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div class="d-flex gap-2">
            <input wire:model.debounce.300ms="search" class="form-control" placeholder="Search..." style="min-width:220px">
            <select wire:model="perPage" class="form-select" style="width:120px">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>

        <div>
            <button wire:click="create" class="btn btn-primary">Create New</button>
        </div>
    </div>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Subject ID</th>
            <th>Degree ID</th>
            <th>Total Number</th>
            <th>Per Day</th>
            <th>Status</th>
            <th width="180">Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $i => $row)
            <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td>{{ $row->mas_student_per_batch_subject_id }}</td>
                <td>{{ $row->mas_student_per_batch_degree_id }}</td>
                <td>{{ $row->mas_student_per_batch_total_number }}</td>
                <td>{{ $row->mas_student_per_batch_per_day }}</td>
                <td>{{ $row->mas_student_per_batch_status_id }}</td>
                <td>
                    <button wire:click="edit({{ $row->id }})" class="btn btn-sm btn-outline-secondary">Edit</button>
                    <button wire:click="confirmDelete({{ $row->id }})" class="btn btn-sm btn-outline-danger">Delete</button>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center">No records found</td></tr>
        @endforelse
        </tbody>
    </table>

    <div>
        @if($items && method_exists($items, 'links'))
            {{ $items->links() }}
        @else
            {{-- optional: show nothing or a simple fallback pager --}}
            {{-- <nav><ul class="pagination"><li class="page-item"><span class="page-link">No pages</span></li></ul></nav> --}}
        @endif
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="crudModal" tabindex="-1" aria-labelledby="crudModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="save" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crudModalLabel">{{ $mas_student_per_batch_id ? 'Edit' : 'Create' }} Student Per Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Subject ID <span class="text-danger">*</span></label>
                        <input type="number" wire:model.defer="mas_student_per_batch_subject_id" class="form-control">
                        @error('mas_student_per_batch_subject_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Degree ID</label>
                        <input type="number" wire:model.defer="mas_student_per_batch_degree_id" class="form-control">
                        @error('mas_student_per_batch_degree_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Total Number <span class="text-danger">*</span></label>
                        <input type="number" wire:model.defer="mas_student_per_batch_total_number" class="form-control">
                        @error('mas_student_per_batch_total_number') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Per Day <span class="text-danger">*</span></label>
                        <input type="number" wire:model.defer="mas_student_per_batch_per_day" class="form-control">
                        @error('mas_student_per_batch_per_day') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select wire:model.defer="mas_student_per_batch_status_id" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('mas_student_per_batch_status_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="$set('showModal', false)">Close</button>
                    <button type="submit" class="btn btn-primary">{{ $mas_student_per_batch_id ? 'Update' : 'Save' }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('confirmingDeleteId', null)"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" wire:click="$set('confirmingDeleteId', null)">Cancel</button>
                    <button type="button" wire:click="delete" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    {{-- small inline JS to toggle bootstrap modals when Livewire changes showModal / confirmingDeleteId --}}
    @push('scripts')
        <script>
            document.addEventListener('livewire:load', function () {
                const modalEl = document.getElementById('crudModal');
                const deleteEl = document.getElementById('deleteModal');
                const bsModal = new bootstrap.Modal(modalEl,{backdrop:'static'});
                const bsDelete = new bootstrap.Modal(deleteEl,{backdrop:'static'});

                // open modal when showModal true
                Livewire.hook('message.processed', (message, component) => {
                    const show = component.get('showModal');
                    if (show) {
                        bsModal.show();
                    } else {
                        bsModal.hide();
                    }

                    const delId = component.get('confirmingDeleteId');
                    if (delId) {
                        bsDelete.show();
                    } else {
                        bsDelete.hide();
                    }
                });

                // notifications (basic)
                window.addEventListener('notify', e => {
                    const { type, message } = e.detail;
                    // you can swap for toast library. For now just alert:
                    // small non-blocking toast would be better in production
                    const el = document.createElement('div');
                    el.innerHTML = `<div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
                    document.body.appendChild(el);
                    setTimeout(()=> el.remove(), 3000);
                });

                // show delete confirm event from component
                window.addEventListener('show-delete-confirm', () => {
                    bsDelete.show();
                });
            });
        </script>
    @endpush
</div>
