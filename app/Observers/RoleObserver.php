<?php

namespace App\Observers;

use App\Models\Admin\Role;

class RoleObserver
{
    public function updated(Role $role): void
    {
        foreach ($role->users as $user) {
            $user->flushPermissionCache();
        }
    }
}
