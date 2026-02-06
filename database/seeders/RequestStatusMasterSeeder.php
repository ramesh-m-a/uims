<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestStatusMasterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('request_status_master')->insert([
            [
                'id' => 26,
                'code' => 'REQUEST_PENDING',
                'label' => 'Requested for Examiner',
                'is_pending' => 1,
                'is_final' => 0,
                'colour' => 'warning'
            ],
            [
                'id' => 27,
                'code' => 'REQUEST_APPROVED',
                'label' => 'Approved',
                'is_pending' => 0,
                'is_final' => 1,
                'colour' => 'success'
            ],
            [
                'id' => 28,
                'code' => 'REQUEST_REJECTED',
                'label' => 'Rejected',
                'is_pending' => 0,
                'is_final' => 1,
                'colour' => 'danger'
            ],
        ]);
    }
}
