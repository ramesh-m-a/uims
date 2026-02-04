<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReligionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Hindu', 'Muslim', 'Christian', 'Sikh', 'Buddhist',
            'Jain', 'Jewish', 'Parsi', 'Other'
        ];

        foreach ($data as $name) {
            DB::table('mas_religion')->updateOrInsert(
                ['mas_religion_name' => $name],
                [
                    'mas_religion_status_id' => 1,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
