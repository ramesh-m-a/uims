<div class="space-y-6">

    {{-- BREADCRUMB --}}
    @include('partials.breadcrumb', [
        'route' => 'permission-templates.index',
        'title' => 'Permission Templates',
        'mode'  => $template ? 'Edit' : 'Create'
    ])

    <div class="form-group">
        <label class="required">Template Name</label>
        <input class="form-control" wire:model.defer="name">
    </div>

    <div class="form-group">
        <label>Description</label>
        <input class="form-control" wire:model.defer="description">
    </div>

    {{-- MATRIX --}}
    <div class="border rounded overflow-x-auto">
        <table class="table table-bordered admin-table">
            <thead>
            <tr>
                <th>Module</th>
                <th>Create</th>
                <th>Edit</th>
                <th>Delete</th>
                <th>View</th>
                <th>Restore</th>
            </tr>
            </thead>

            <tbody>
            @foreach($matrix as $group => $entities)
                <tr class="bg-gray-100 font-semibold">
                    <td colspan="6">{{ $group }}</td>
                </tr>

                @foreach($entities as $entity => $actions)
                    <tr>
                        <td class="pl-4">{{ $entity }}</td>
                        @foreach($actions as $meta)
                            <td class="text-center">
                                <input type="checkbox"
                                       wire:click="toggle({{ $meta['id'] }})"
                                    @checked($meta['checked'])>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>

    <button wire:click="save" class="btn btn-primary">
        Save Template
    </button>

</div>
