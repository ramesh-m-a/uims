<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TalukSeeder extends Seeder
{
    public function run(): void
    {
        $taluks = ['T Nagar','Velachery','Tambaram','Mylapore'];

        foreach ($taluks as $name) {
            DB::table('mas_taluk')->updateOrInsert(
                ['mas_taluk_name' => $name],
                [
                    'mas_taluk_status_id' => 1,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
