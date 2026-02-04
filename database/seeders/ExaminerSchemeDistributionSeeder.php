<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExaminerSchemeDistributionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [1, 1, 1, 2],
            [2, 1, 2, 2],
            [3, 2, 1, 3],
            [4, 2, 2, 1],
            [5, 3, 1, 3],
            [6, 3, 2, 1],
        ];

        foreach ($rows as [$id, $scheme, $type, $count]) {
            DB::table('mas_examiner_scheme_distribution')->updateOrInsert(
                ['id' => $id],
                [
                    'mas_examiner_scheme_distribution_scheme_id'        => $scheme,
                    'mas_examiner_scheme_distribution_examiner_type_id'  => $type,
                    'mas_examiner_scheme_distribution_examiner_type_count'=> $count,
                    'mas_examiner_scheme_distribution_status_id'        => 50,
                    'created_at'                                        => now(),
                    'updated_at'                                        => now(),
                ]
            );
        }
    }
}
