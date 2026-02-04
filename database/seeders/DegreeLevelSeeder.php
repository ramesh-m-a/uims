<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DegreeLevelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mas_degree_level')->insert([
            [
                'mas_degree_level_code'       => 'UG',
                'mas_degree_level_name'       => 'Under Graduate',
                'mas_degree_level_sort_order'   => 1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'mas_degree_level_code'       => 'PG',
                'mas_degree_level_name'       => 'Post Graduate',
                'mas_degree_level_sort_order'   => 2,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'mas_degree_level_code'       => 'SS',
                'mas_degree_level_name'       => 'Super Specialisation',
                'mas_degree_level_sort_order'   => 3,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'mas_degree_level_code'       => 'DT',
                'mas_degree_level_name'       => 'Doctorate',
                'mas_degree_level_sort_order'   => 4,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],

            [
                'mas_degree_level_code'       => 'FP',
                'mas_degree_level_name'       => 'Fellowship',
                'mas_degree_level_sort_order'   => 5,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'mas_degree_level_code'       => 'OT',
                'mas_degree_level_name'       => 'Others',
                'mas_degree_level_sort_order'   => 6,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
        ]);
    }
}
