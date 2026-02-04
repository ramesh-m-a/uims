<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'super-admin')->firstOrFail();

        $permissions = Permission::where('module', 'user')->get();

        $role->permissions()->sync(
            $permissions->pluck('id')->toArray()
        );
    }
}
