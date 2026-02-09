{{-- ================= ADMIN MAIN DRAWER ================= --}}
@if(!$isCollegeUser && ($showDrawer ?? false))

    <div class="fixed inset-0 bg-dark bg-opacity-25 z-40" wire:click="closeDrawer"></div>

    <div class="fixed top-0 right-0 w-full sm:w-[620px] h-full bg-white shadow-lg z-50 p-5 overflow-auto">

        <div class="flex justify-between items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1">Slot Details</h5>
                <div class="text-muted text-sm">Modify or review examiner assignment</div>
            </div>
            <button class="btn btn-sm btn-light" wire:click="closeDrawer">✕</button>
        </div>

        <div class="border rounded p-3 bg-light mb-3 text-sm space-y-1">
            <div><strong>Centre:</strong> {{ $selectedRow['centre_name'] ?? '' }}</div>
            <div><strong>Batch:</strong> {{ $selectedRow['batch_name'] ?? '' }}</div>
            <div><strong>Date:</strong> {{ $selectedRow['from_date'] ?? '' }}</div>
            <div><strong>Subject:</strong> {{ $selectedRow['subject_name'] ?? '' }}</div>
            <div><strong>Current Examiner:</strong>
                {{ $selectedRow['examiner_name'] ?? '' }}
                ({{ $selectedRow['examiner_type'] ?? '' }})
            </div>
            <div><strong>Mobile:</strong> {{ $selectedRow['mobile'] ?? '' }}</div>
        </div>

        <input wire:model.debounce.400ms="pickerSearch"
               class="form-control mb-3"
               placeholder="Search examiner by name or mobile..." />

        <div class="border rounded">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th width="150">Mobile</th>
                    <th width="90"></th>
                </tr>
                </thead>

                <tbody>
                @forelse($this->pickerResults as $ex)
                    <tr>
                        <td class="pe-4">{{ $ex->name }}</td>
                        <td class="ps-4">{{ $ex->mobile }}</td>
                        <td class="text-end">
                            @if($isAddingAdditional)
                                <button wire:click="addAdditionalExaminer({{ $ex->id }})"
                                        class="btn btn-primary btn-xs">
                                    Add
                                </button>
                            @else
                                <button wire:click="assignExaminer({{ $ex->id }})"
                                        class="btn btn-primary btn-xs">
                                    Select
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">
                            No examiners found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 flex justify-center">
            {{ $this->pickerResults->links() }}
        </div>

        <div class="mt-4 text-center">
            <button class="btn btn-primary" wire:click="openAddModal">
                + Add Examiner
            </button>
        </div>

    </div>
@endif



{{-- ================= ADMIN DATE DRAWER ================= --}}
@if(!$isCollegeUser && ($showDateDrawer ?? false))

    <div class="fixed inset-0 bg-dark bg-opacity-25 z-40"
         wire:click="$set('showDateDrawer', false)"></div>

    <div class="fixed top-0 right-0 w-full sm:w-[520px] h-full bg-white shadow-lg z-50 p-5 overflow-auto">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h5 class="fw-bold mb-1">Change Schedule Date</h5>
                <div class="text-muted text-sm">
                    This change applies only to this examiner
                </div>
            </div>
            <button class="btn btn-sm btn-light"
                    wire:click="$set('showDateDrawer', false)">✕</button>
        </div>

        <div class="border rounded p-3 bg-light mb-4 text-sm space-y-1">
            <div><strong>Centre:</strong> {{ $selectedRow['centre_name'] ?? '' }}</div>
            <div><strong>Batch:</strong> {{ $selectedRow['batch_name'] ?? '' }}</div>
            <div><strong>Subject:</strong> {{ $selectedRow['subject_name'] ?? '' }}</div>
            <div><strong>Type:</strong> {{ $selectedRow['examiner_type'] ?? '' }}</div>
            <div><strong>Examiner:</strong> {{ $selectedRow['examiner_name'] ?? '' }}</div>
            <div><strong>Mobile:</strong> {{ $selectedRow['mobile'] ?? '' }}</div>
        </div>

        <div class="border rounded p-3 mb-4">
            <div class="fw-semibold mb-3">Schedule</div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="text-xs text-muted">Current Date</label>
                    <input class="form-control" disabled
                           value="{{ \Carbon\Carbon::parse($current_from_date)->format('d/m/Y') }}">
                </div>

                <div>
                    <label class="text-xs text-muted">New Date</label>
                    <input type="date"
                           wire:model="new_from_date"
                           class="form-control"
                           min="{{ now()->toDateString() }}">
                    @error('new_from_date')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="text-muted text-xs">
                ℹ Only this examiner’s schedule date will be updated.
            </div>
        </div>

        <div class="mt-4 text-end">
            <button wire:click="saveDateChange"
                    class="btn btn-success px-4">
                Update Date
            </button>
        </div>

    </div>
@endif



{{-- ================= COLLEGE REQUEST DRAWER ================= --}}
@if($isCollegeUser && ($showRequestDrawer ?? false))

    <div class="fixed inset-0 bg-dark bg-opacity-25 z-40"
         wire:click="$set('showRequestDrawer', false)"></div>

    <div class="fixed top-0 right-0 w-full sm:w-[620px] h-full bg-white shadow-lg z-50 p-5 overflow-auto">

        <div class="flex justify-between items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1">Request Examiner Change</h5>
                <div class="text-muted text-sm">
                    Select examiner to send request to RGUHS
                </div>
            </div>

            <button class="btn btn-sm btn-light"
                    wire:click="$set('showRequestDrawer', false)">✕</button>
        </div>

        <input wire:model.debounce.400ms="pickerSearch"
               class="form-control mb-3"
               placeholder="Search examiner by name or mobile..." />

        <div class="border rounded">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th width="150">Mobile</th>
                    <th width="90"></th>
                </tr>
                </thead>

                <tbody>
                @forelse($this->pickerResults as $ex)
                    <tr>
                        <td>{{ $ex->name }}</td>
                        <td>{{ $ex->mobile }}</td>
                        <td class="text-end">
                            <button wire:click="submitRequest({{ $ex->id }})"
                                    class="btn btn-warning btn-xs">
                                Request
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">
                            No examiners found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $this->pickerResults->links() }}
        </div>

    </div>

@endif
