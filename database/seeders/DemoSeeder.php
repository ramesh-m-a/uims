<?php

namespace Database\Seeders;

use App\Models\Master\Common\Designation;
use App\Models\Master\Config\Academic\Stream;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->isLocal()) {
            return;
        }

        Designation::updateOrCreate(['mas_designation_name' => 'Demo Designation'], ['mas_designation_short_code' => 'DEMO', 'mas_designation_status_id' => 50, 'created_by' => 1, 'updated_by' => 1,]);
        Stream::updateOrCreate(['mas_stream_name' => 'Demo Stream'], ['mas_stream_short_code' => 'DEMO', 'mas_stream_status_id' => 50, 'created_by' => 1, 'updated_by' => 1,]);
    }

}
