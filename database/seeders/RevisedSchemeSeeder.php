<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RevisedSchemeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'name' => 'Revised Scheme 3', 'short' => 'RS3'],
            ['id' => 2, 'name' => 'Revised Scheme 4', 'short' => 'RS4'],
            ['id' => 3, 'name' => 'CBME',             'short' => 'CBME'],
        ];

        foreach ($rows as $r) {
            DB::table('mas_revised_scheme')->updateOrInsert(
                ['id' => $r['id']],
                [
                    'mas_revised_scheme_name'       => $r['name'],
                    'mas_revised_scheme_short_name' => $r['short'],
                    'mas_revised_scheme_status_id'  => 1,
                    'created_at'                    => now(),
                    'updated_at'                    => now(),
                ]
            );
        }
    }
}
