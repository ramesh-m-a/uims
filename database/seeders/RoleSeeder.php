<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ROLE HIERARCHY
        |--------------------------------------------------------------------------
        |
        | super-admin
        |   └── admin
        |         └── college-admin
        |               └── principal
        |                     └── my-details
        |
        */

        // 🔝 SUPER ADMIN (ROOT)
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin'],
            [
                'description'     => 'System Super Administrator',
                'parent_role_id'  => null,
                'roles_status_id' => 1,
            ]
        );

        // 🛠 ADMIN
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'description'     => 'Application Administrator',
                'parent_role_id'  => $superAdmin->id,
                'roles_status_id' => 1,
            ]
        );

        // 🏫 COLLEGE ADMIN
        $collegeAdmin = Role::firstOrCreate(
            ['name' => 'college-admin'],
            [
                'description'     => 'College Administrator',
                'parent_role_id'  => $admin->id,
                'roles_status_id' => 1,
            ]
        );

        // 🎓 PRINCIPAL
        $principal = Role::firstOrCreate(
            ['name' => 'principal'],
            [
                'description'     => 'College Principal',
                'parent_role_id'  => $collegeAdmin->id,
                'roles_status_id' => 1,
            ]
        );

        // 👨‍🏫 TEACHER
        $teacher = Role::firstOrCreate(
            ['name' => 'my-details'],
            [
                'description'     => 'Teaching Staff',
                'parent_role_id'  => $principal->id,
                'roles_status_id' => 1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | PERMISSION ASSIGNMENT
        |--------------------------------------------------------------------------
        */

        // 🔥 SUPER ADMIN → ALL PERMISSIONS
        $superAdmin->permissions()->sync(
            Permission::pluck('id')->toArray()
        );

        // 🎯 ADMIN → EXPLICIT (INTENTIONALLY LIMITED)
        $adminPermissions = Permission::whereIn('name', [

            // USERS
            'user.view',
            'user.create',
            'user.edit',

            // ROLE MANAGEMENT (VIEW ONLY)
            'master.role.view',

            // MASTER DATA
            'master.stream.view',
            'master.stream.create',
            'master.stream.edit',

            'master.college.view',
            'master.college.create',
            'master.college.edit',

            'master.designation.view',
            'master.designation.create',
            'master.designation.edit',

        ])->pluck('id')->toArray();

        $admin->permissions()->sync($adminPermissions);

        /*
        |--------------------------------------------------------------------------
        | INHERITED ROLES (NO DIRECT PERMISSIONS)
        |--------------------------------------------------------------------------
        |
        | college-admin, principal, my-details
        | → permissions flow from parent
        |
        */

        $collegeAdmin->permissions()->sync([]);
        $principal->permissions()->sync([]);
        $teacher->permissions()->sync([]);
    }
}
