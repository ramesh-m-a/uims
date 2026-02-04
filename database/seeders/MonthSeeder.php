<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthSeeder extends Seeder
{
    public function run(): void
    {
        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];

        $count = 0;

        foreach ($months as $month) {
            DB::table('mas_month')->updateOrInsert(
                ['mas_month_name' => $month],
                [
                    'mas_month_status_id' => 1,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $count++;
        }

        echo "\nDONE ✅\n";
        echo "Months seeded: {$count}\n";
    }
}
