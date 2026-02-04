<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['SAVINGS','CURRENT','OVER DRAFT'];

        foreach ($types as $name) {
            DB::table('mas_account_type')->updateOrInsert(
                ['mas_account_type_name' => $name],
                [
                    'mas_account_type_status_id' => 1,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
