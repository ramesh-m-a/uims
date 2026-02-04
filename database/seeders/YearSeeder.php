<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YearSeeder extends Seeder
{
    public function run(): void
    {
        $years = range(2024, 2028); // adjust if you want

        $count = 0;

        foreach ($years as $year) {
            DB::table('mas_year')->updateOrInsert(
                ['mas_year_year' => (string)$year],
                [
                    'mas_year_status_id' => 1,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $count++;
        }

        echo "\nDONE ✅\n";
        echo "Years seeded: {$count}\n";
    }
}
