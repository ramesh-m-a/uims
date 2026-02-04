<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            'Tamil Nadu' => ['Chennai','Coimbatore','Madurai','Trichy'],
            'Karnataka'  => ['Bengaluru Urban','Mysuru','Mangaluru'],
            'Kerala'     => ['Thiruvananthapuram','Ernakulam','Kozhikode'],
        ];

        foreach ($districts as $state => $list) {

            $stateId = DB::table('mas_state')
                ->where('mas_state_name', $state)
                ->value('id');

            if (!$stateId) {
                continue;
            }

            foreach ($list as $name) {

                DB::table('mas_district')->updateOrInsert(
                    ['mas_district_name' => $name],
                    [
                        'mas_district_status_id' => 1,
                        'created_by' => null,
                        'updated_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
