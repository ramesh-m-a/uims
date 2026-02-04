<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DegreeGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['code' => 'MED_UG',    'name' => 'Medical UG'],
            ['code' => 'MED_PG',    'name' => 'Medical PG'],
            ['code' => 'MED_SS',    'name' => 'Medical Super Speciality'],
            ['code' => 'DENTAL_PG', 'name' => 'Dental PG'],
            ['code' => 'PHYSIO_PG', 'name' => 'Physiotherapy PG'],
            ['code' => 'PHARMA_PG', 'name' => 'Pharmacy PG'],
            ['code' => 'ALLIED_PG', 'name' => 'Allied Health PG'],
            ['code' => 'GENERIC',   'name' => 'Generic / General'],
        ];

        foreach ($groups as $g) {
            DB::table('mas_degree_group')->updateOrInsert(
                ['mas_degree_group_code' => $g['code']],
                [
                    'mas_degree_group_name' => $g['name'],
                    'status_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
