<div class="admin-card">

    <h2 class="font-semibold mb-3">Permission Change Audit</h2>

    <table class="table table-bordered admin-table">
        <thead>
        <tr>
            <th>Actor</th>
            <th>Target User</th>
            <th>Changed At</th>
            <th>Details</th>
        </tr>
        </thead>

        <tbody>
        @foreach($audits as $a)
            <tr>
                <td>{{ $a->actor_id }}</td>
                <td>{{ $a->target_user_id ?: 'ROLE' }}</td>
                <td>{{ $a->created_at }}</td>
                <td>
                    <details>
                        <summary>View</summary>
                        <pre class="text-xs">
BEFORE:
{{ json_encode($a->before, JSON_PRETTY_PRINT) }}

AFTER:
{{ json_encode($a->after, JSON_PRETTY_PRINT) }}
                        </pre>
                    </details>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $audits->links() }}
</div>
