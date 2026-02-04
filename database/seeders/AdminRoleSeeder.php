<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'HOD',
            'Principal',
            'Vice Principal',
            'Dean',
            'Director',
            'Unit Chief',
            'Superintendent',
            'Special Officer',
            'Directorate of Medical Education',
            'Director - DME',
            'Deputy Registrar',
            'Assistant Registrar',
            'Joint Director - DME',
        ];

        foreach ($roles as $role) {
            DB::table('mas_admin_role')->updateOrInsert(
                ['mas_admin_role_name' => $role],
                [
                    'mas_admin_role_status_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        echo "Admin roles seeded successfully.\n";
    }
}
