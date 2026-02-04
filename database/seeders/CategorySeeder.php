<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = ['General', 'OBC', 'SC', 'ST', 'EWS', 'Others'];

        foreach ($data as $name) {
            DB::table('mas_category')->updateOrInsert(
                ['mas_category_name' => $name],
                [
                    'mas_category_status_id' => 1,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
