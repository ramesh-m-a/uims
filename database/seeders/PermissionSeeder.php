<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionMap = [

            // MASTER
            'master.stream'        => ['view','create','edit','delete','restore'],
            'master.college'       => ['view','create','edit','delete','restore'],
            'master.designation'   => ['view','create','edit','delete','restore'],
            'master.gender'        => ['view','create','edit','delete'],
            'master.religion'      => ['view','create','edit','delete'],
            'master.degree'        => ['view','create','edit','delete','restore'],
            'master.degree-stream' => ['view','create','edit','delete','restore'],


            // 🔐 ROLE MANAGEMENT (CRITICAL)
            'master.role'         => ['view','edit'],

            // USERS
            'user'                => ['view','create','edit','delete','restore'],

            // EXAMINER
            'exam'            => ['view','edit','export'],
        ];

        foreach ($permissionMap as $module => $actions) {
            foreach ($actions as $action) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => "{$module}.{$action}"],
                    [
                        'module'     => $module,
                        'action'     => $action,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
