<div class="space-y-4">

    @include('partials.breadcrumb', [
        'route' => 'master.role.index',
        'title' => 'Role',
        'mode'  => 'Apply Template'
    ])

    <h2 class="font-semibold">
        Apply Permission Template → {{ $role->name }}
    </h2>

    <div class="form-group">
        <label>Select Template</label>
        <select wire:model="templateId" class="form-control">
            <option value="">-- Select Template --</option>
            @foreach($templates as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
        </select>
    </div>

    @if($previewCount)
        <div class="alert alert-info text-sm">
            {{ $previewCount }} permissions will be applied.
        </div>
    @endif

    <button
        wire:click="apply"
        class="btn btn-primary"
        @disabled(! $templateId)
    >
        Apply Template
    </button>

</div>
