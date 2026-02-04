@if($showDeleteModal)
    <div class="admin-loader" x-cloak>
        <div class="admin-loader-box w-[420px]" role="dialog" aria-modal="true">

            <h6 class="mb-2 font-semibold text-lg text-gray-800">
                <i class="fa fa-exclamation-triangle text-red-600 mr-1"></i>
                Delete Record
            </h6>

            <p class="text-sm text-gray-600 mt-2">
                Are you sure you want to delete this record?
            </p>

            <div class="flex justify-end gap-2 mt-5">
                <button
                    type="button"
                    wire:click="$set('showDeleteModal', false)"
                    class="btn btn-secondary btn-sm"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    wire:click="deleteConfirmed"
                    class="btn btn-danger btn-sm"
                >
                    <i class="fa fa-trash"></i> Yes, Delete
                </button>
            </div>

        </div>
    </div>
@endif
