<div class="space-y-4">

    {{-- BREADCRUMB --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'route' => route('users.index'),
            'title' => 'User Permissions',
            'mode'  => 'View'
        ])
    </div>

    <h1 class="font-normal text-black-400">
        User » Permissions – {{ $user->name }}
        <a href="{{ route('user.index') }}"
           class="float-right text-sm text-gray-500">
            ← Back
        </a>
    </h1>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-bordered admin-table">

                <thead>
                <tr>
                    <th width="220">Module</th>
                    <th class="text-center">Create</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                    <th class="text-center">View</th>
                    <th class="text-center">Restore</th>
                </tr>
                </thead>

                <tbody>

                @foreach($permissions as $group => $modules)

                    {{-- GROUP HEADER --}}
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="6">{{ $group }}</td>
                    </tr>

                    @foreach($modules as $module => $actions)
                        <tr>
                            <td class="pl-6 font-medium">
                                {{ ucfirst(str_replace($group.'.', '', $module)) }}
                            </td>

                            @foreach(['create','edit','delete','view','restore'] as $action)
                                <td class="text-center">
                                    @if($actions->where('action', $action)->count())
                                        <i class="fa fa-check text-success"></i>
                                    @else
                                        —
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                @endforeach

                </tbody>

            </table>
        </div>
    </div>

</div>
