<div class="space-y-4">

    {{-- =========================
     | BREADCRUMB
     ========================= --}}
    @include('partials.breadcrumb', [
        'route' => 'user.index',
        'title' => 'User',
        'mode'  => 'Apply Template'
    ])

    <h2 class="font-semibold">
        Apply Permission Template → {{ $user->name }}
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

    <button
        wire:click="apply"
        class="btn btn-primary"
        @disabled(! $templateId)
    >
        Apply Template
    </button>

</div>
