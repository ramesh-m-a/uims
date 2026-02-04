<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = ['Chennai','Bengaluru','Hyderabad','Mumbai','Delhi','Kolkata','Pune','Ahmedabad','Jaipur','Kochi'];

        foreach ($cities as $name) {
            DB::table('mas_city')->updateOrInsert(
                ['mas_city_name' => $name],
                [
                    'mas_city_status_id' => 1,
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
