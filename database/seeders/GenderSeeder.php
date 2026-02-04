<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Male',
            'Female',
            'Other',
        ];

        foreach ($data as $name) {
            DB::table('mas_gender')->updateOrInsert(
                ['mas_gender_name' => $name],
                [
                    'mas_gender_status_id' => 1,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
