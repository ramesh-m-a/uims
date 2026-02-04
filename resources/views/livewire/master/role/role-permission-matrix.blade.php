<div class="space-y-4">

    {{-- BREADCRUMB --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => $pageRoute,
            'title' => 'Role Permissions',
            'mode'  => 'Manage'
        ])
    </div>

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <h1 class="font-normal text-black-400">
            Role ›› Permissions – {{ strtoupper($role->name) }}
        </h1>

        <a href="{{ route('master.role.index') }}"
           class="btn btn-secondary btn-sm">
            ← Back
        </a>
    </div>

    {{-- MATRIX --}}
    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-bordered admin-table">

                <thead>
                <tr>
                    <th width="200">Module</th>

                    @foreach($actions as $action)
                        <th class="text-center">
                            <div class="flex flex-col items-center gap-1">
                                {{ ucfirst($action) }}

                                <input type="checkbox"
                                       wire:model="selectAllColumns.{{ $action }}"
                                    @disabled(!$canEdit)>
                            </div>
                        </th>
                    @endforeach
                </tr>
                </thead>

                <tbody>
                @foreach($permissionsMatrix as $module => $perms)
                    <tr>
                        <td class="font-semibold">
                            <div class="flex items-center gap-2">
                                {{ strtoupper($module) }}

                                <input type="checkbox"
                                       wire:model="selectAllRows.{{ $module }}"
                                    @disabled(!$canEdit)>
                            </div>
                        </td>

                        @foreach($actions as $action)
                            <td class="text-center">
                                @if(array_key_exists($action, $perms))
                                    <input type="checkbox"
                                           wire:model.defer="permissionsMatrix.{{ $module }}.{{ $action }}"
                                        @disabled(!$canEdit)>
                                @else
                                    —
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

        {{-- FOOTER --}}
        <div class="flex justify-end mt-3">
            @if($canEdit)
                <button wire:click="save"
                        class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Permissions
                </button>
            @else
                <span class="text-muted text-sm">
                    View only – no edit permission
                </span>
            @endif
        </div>
    </div>

</div>
