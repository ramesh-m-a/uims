<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Proof of Photo identity',
            'Proof of Address',
            'Proof of Date of Birth',
            'Proof of Qualification - UG',
            'Proof of Qualification - PG',
            'Proof of Qualification - SS',
            'Proof of Qualification - PD',
            'Proof of Qualification - OT',
            'Proof of Council Registration',
            'Proof of Appointment',
            'Proof of Promotion',
            'Proof of Resignation / Reliving / Retirement',
            'Proof of Experience Assistant Professor',
            'Proof of Experience Associate Professor',
            'Proof of Experience Professor',
            'Proof of Joining/Reporting',
            'Proof of PAN card',
            'Latest Form 16 / 16A / 26AS or 1 Year bank Statement',
            'Proof of Experience Lecturer',
            'UG Guide Recognition Letter',
            'PG Guide Recognition Letter',
        ];

        foreach ($data as $index => $name) {
            DB::table('mas_document')->updateOrInsert(
                ['mas_document_name' => $name],
                [
                    'mas_document_description' => $name,
                    'mas_document_status_id'   => 1,
                    'mas_document_sort_order'  => $index + 1,
                    'mas_document_type'        => 0, // General by default
                    'mas_document_is_required_global' => false,
                ]
            );
        }
    }
}
