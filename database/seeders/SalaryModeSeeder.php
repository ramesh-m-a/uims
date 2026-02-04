<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalaryModeSeeder extends Seeder
{
    public function run(): void
    {
        $modes = ['NEFT','IMPS','CHEQUE'];

        foreach ($modes as $name) {
            DB::table('mas_salary_mode')->updateOrInsert(
                ['mas_salary_mode_name' => $name],
                [
                    'mas_salary_mode_status_id' => 1,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
