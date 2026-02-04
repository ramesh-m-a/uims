@props([
    'title' => 'Confirm Delete',
    'message' => 'Are you sure you want to delete this record?',
    'confirmAction' => '',
    'cancelAction' => '',
])

<div class="admin-loader">
    <div class="admin-loader-box">

        <h6 class="mb-2">{{ $title }}</h6>

        <p class="text-sm text-muted">
            {!! $message !!}
        </p>

        <div class="flex justify-end gap-2 mt-4">
            <button
                wire:click="{{ $cancelAction }}"
                class="btn btn-secondary btn-sm">
                Cancel
            </button>

            <button
                wire:click="{{ $confirmAction }}"
                class="btn btn-danger btn-sm">
                <i class="fa fa-trash"></i> Yes, Delete
            </button>
        </div>

    </div>
</div>
