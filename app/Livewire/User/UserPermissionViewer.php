<?php

namespace App\Livewire\User;

use App\Models\Admin\User;
use Livewire\Component;

class UserPermissionViewer extends Component
{
    public User $user;

    public array $permissions = [];

    public function mount(User $user): void
    {
        $this->user = $user;

        // Resolve effective permissions (cached method)
        $this->permissions = $user->permissions()
            ->groupBy(function ($perm) {
                return strtoupper(
                    str_contains($perm->module, '.')
                        ? explode('.', $perm->module, 2)[0]
                        : $perm->module
                );
            })
            ->map(function ($perms) {
                return $perms->groupBy('module');
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.user.user-permission-viewer');
    }
}
