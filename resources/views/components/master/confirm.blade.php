<div
    x-data
    x-show="$store.confirm.show"
    x-cloak
    class="admin-loader"
>
    <div class="admin-loader-box w-[420px]" role="dialog" aria-modal="true">

        <h6 class="mb-2 font-semibold text-lg text-gray-800">
            <i class="fa fa-exclamation-triangle text-red-600 mr-1"></i>
            <span x-text="$store.confirm.title"></span>
        </h6>

        <p class="text-sm text-gray-600 mt-2"
           x-text="$store.confirm.message">
        </p>

        <div class="flex justify-end gap-2 mt-5">
            <button
                type="button"
                @click="$store.confirm.close()"
                class="btn btn-secondary btn-sm"
            >
                Cancel
            </button>

            <button
                type="button"
                @click="$store.confirm.confirm()"
                class="btn btn-danger btn-sm"
            >
                <i class="fa fa-trash"></i> Yes, Delete
            </button>
        </div>

    </div>
</div>
