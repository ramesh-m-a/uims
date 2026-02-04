<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DegreeGroupSpecialisationGroupSeeder extends Seeder
{
    public function run(): void
    {
        $degreeGroups = DB::table('mas_degree_group')->pluck('id', 'mas_degree_group_code');
        $specGroups   = DB::table('mas_specialisation_group')->pluck('id', 'mas_specialisation_group_code');

        $rules = [
            'MED_UG'    => ['GENERIC'],
            'MED_PG'    => ['MED_CORE', 'GENERIC'],
            'MED_SS'    => ['MED_SUPER'],
            'DENTAL_PG' => ['DENTAL'],
            'PHYSIO_PG' => ['PHYSIO'],
            'PHARMA_PG' => ['PHARMA'],
            'ALLIED_PG' => ['ALLIED'],
        ];

        foreach ($rules as $degGroup => $specGroupList) {
            foreach ($specGroupList as $specGroup) {
                DB::table('mas_degree_group_specialisation_group_map')->updateOrInsert([
                    'degree_group_id' => $degreeGroups[$degGroup],
                    'specialisation_group_id' => $specGroups[$specGroup],
                ]);
            }
        }
    }
}
