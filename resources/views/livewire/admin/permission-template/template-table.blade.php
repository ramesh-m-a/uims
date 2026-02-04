<div class="admin-card">

    <div class="flex justify-between mb-3">
        <h2 class="font-semibold">Permission Templates</h2>

        <a href="{{ route('permission-templates.create') }}"
           class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> New Template
        </a>
    </div>

    <table class="table table-bordered admin-table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th width="160">Actions</th>
        </tr>
        </thead>

        <tbody>
        @foreach($templates as $t)
            <tr>
                <td>{{ $t->name }}</td>
                <td>{{ $t->description }}</td>
                <td>
                    <a href="{{ route('permission-templates.edit', $t) }}"
                       class="action-btn edit">
                        <i class="fa fa-pencil"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
