<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialisationGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['code' => 'MED_CORE',  'name' => 'Core Medical'],
            ['code' => 'MED_SUPER', 'name' => 'Medical Super Speciality'],
            ['code' => 'DENTAL',    'name' => 'Dental'],
            ['code' => 'PHYSIO',    'name' => 'Physiotherapy'],
            ['code' => 'PHARMA',    'name' => 'Pharmacy'],
            ['code' => 'ALLIED',    'name' => 'Allied Health'],
            ['code' => 'GENERIC',   'name' => 'General / Common'],
        ];

        foreach ($groups as $g) {
            DB::table('mas_specialisation_group')->updateOrInsert(
                ['mas_specialisation_group_code' => $g['code']],
                [
                    'mas_specialisation_group_name' => $g['name'],
                    'status_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
