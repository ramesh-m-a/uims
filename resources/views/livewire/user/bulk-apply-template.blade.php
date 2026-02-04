<div class="space-y-4">

    @include('partials.breadcrumb', [
        'route' => 'user.index',
        'title' => 'Users',
        'mode'  => 'Bulk Apply Template'
    ])

    <h2 class="font-semibold">Bulk Apply Permission Template</h2>

    <div class="form-group">
        <label>Users</label>
        <select wire:model="userIds" multiple class="form-control h-48">
            @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Template</label>
        <select wire:model="templateId" class="form-control">
            <option value="">-- Select Template --</option>
            @foreach($templates as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
        </select>
    </div>

    @if($userIds && $templateId)
        <div class="alert alert-warning text-sm">
            {{ count($userIds) }} users will be affected.
        </div>
    @endif

    <button
        wire:click="apply"
        class="btn btn-danger"
        @disabled(! $userIds || ! $templateId)
    >
        Apply Template (Audit Enabled)
    </button>

</div>
