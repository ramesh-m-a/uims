<div>

    {{-- GLOBAL LOADER --}}
    <div wire:loading.delay.long class="admin-loader">
        <div class="admin-loader-box">
            <h6>Please wait</h6>
            <p>Working on the records, do not refresh...</p>
        </div>
    </div>

    <div class="admin-card">

        {{-- EXPORT --}}
        <div class="d-flex justify-content-end mb-2 gap-2">
            <button wire:click="exportExcel" class="btn btn-success btn-sm">
                <i class="fa fa-file-excel"></i> Excel
            </button>
        </div>

        {{-- SEARCH --}}
        <div class="mb-2">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control form-control-sm w-25"
                placeholder="Search name or mobile">
        </div>

        <div class="table-responsive">
            <table class="table table-bordered admin-table">

                <thead>
                <tr>
                    <th width="70">Photo</th>

                    <th class="sortable" wire:click="sortBy('ms.mas_stream_name')">
                        Stream
                    </th>

                    <th class="sortable" wire:click="sortBy('mc.mas_college_name')">
                        College
                    </th>

                    <th class="sortable" wire:click="sortBy('mdt.mas_department_name')">
                        Department
                    </th>

                    <th>Name</th>
                    <th>Designation</th>
                    <th class="text-center">Rank</th>
                    <th>Mobile</th>
                    <th class="text-center">Type</th>
                    <th width="80" class="text-center">Action</th>
                </tr>

                {{-- COLUMN FILTERS --}}
                <tr class="admin-filters">
                    <th></th>

                    <th>
                        <input wire:model.live.debounce.300ms="columnFilters.stream"
                               class="form-control form-control-sm" placeholder="Filter">
                    </th>

                    <th>
                        <input wire:model.live.debounce.300ms="columnFilters.college"
                               class="form-control form-control-sm" placeholder="Filter">
                    </th>

                    <th>
                        <input wire:model.live.debounce.300ms="columnFilters.department"
                               class="form-control form-control-sm" placeholder="Filter">
                    </th>

                    <th>
                        <input wire:model.live.debounce.300ms="columnFilters.name"
                               class="form-control form-control-sm" placeholder="Filter">
                    </th>

                    <th>
                        <input wire:model.live.debounce.300ms="columnFilters.designation"
                               class="form-control form-control-sm" placeholder="Filter">
                    </th>

                    <th></th>

                    <th>
                        <input wire:model.live.debounce.300ms="columnFilters.mobile"
                               class="form-control form-control-sm" placeholder="Filter">
                    </th>

                    <th>
                        <select wire:model.live="columnFilters.type"
                                class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="INTERNAL">INTERNAL</option>
                            <option value="EXTERNAL">EXTERNAL</option>
                        </select>
                    </th>

                    <th></th>
                </tr>
                </thead>

                <tbody>
                @forelse($examiners as $row)
                    <tr>
                        <td class="text-center">
                            @if($row->user_photo_path)
                                <img
                                    src="http://localhost/storage/{{ ltrim($row->user_photo_path,'/') }}"
                                    class="rounded-circle"
                                    style="width:40px;height:40px;object-fit:cover"
                                >
                            @else
                                <i class="fa fa-user-circle fa-2x text-muted"></i>
                            @endif
                        </td>

                        <td>{{ $row->mas_stream_name }}</td>
                        <td>{{ $row->mas_college_name }}</td>
                        <td>{{ $row->mas_department_name }}</td>
                        <td>{{ $row->fname }}</td>
                        <td>{{ $row->mas_designation_name }}</td>
                        <td class="text-center">{{ $row->examiner_details_rank }}</td>
                        <td>{{ $row->user_mobile_number }}</td>

                        <td class="text-center">
                            <span class="admin-badge {{ $row->examiner_details_type === 'INTERNAL' ? 'success' : 'warning' }}">
                                {{ $row->examiner_details_type }}
                            </span>
                        </td>

                        <td class="text-center">
                            <button wire:click="edit({{ $row->examinerId }})"
                                    class="action-btn edit">
                                <i class="fa fa-pencil-alt"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            No records found
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="admin-footer">
            <div class="text-muted small">
                Showing {{ $examiners->firstItem() }}
                to {{ $examiners->lastItem() }}
                of {{ $examiners->total() }} results
            </div>

            <div>
                {{ $examiners->links() }}
            </div>
        </div>

    </div>

    {{-- EDIT MODAL --}}
    @if($showEditModal)
        <div class="admin-loader">
            <div class="admin-loader-box w-[420px]">
                <h6>Edit Examiner</h6>

                <label>Rank</label>
                <input wire:model="editForm.rank" class="form-control mb-2">

                <label>Type</label>
                <select wire:model="editForm.type" class="form-select mb-3">
                    <option value="INTERNAL">INTERNAL</option>
                    <option value="EXTERNAL">EXTERNAL</option>
                </select>

                <div class="text-end">
                    <button wire:click="$set('showEditModal', false)"
                            class="btn btn-secondary btn-sm">Cancel</button>
                    <button wire:click="updateExaminer"
                            class="btn btn-primary btn-sm">Save</button>
                </div>
            </div>
        </div>
    @endif

</div>
