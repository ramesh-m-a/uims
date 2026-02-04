<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            'Junior Resident',
            'Senior Resident',
            'Assistant Professor',
            'Associate Professor',
            'Professor',
            'Others',
            'Tutor',
            'Lecturer',
            'Reader',
            'Assistant Librarian',
            'Assistant Director of Physical Education',
            'Deputy Director of Physical Education',
            'Director of Physical Education',
            'Deputy Librarian',
            'Demonstrator',
            'Senior Registrar',
            'Consultant Specialist',
            'Clinical Instructor / Assistant Lecturer',
            'Librarian',
        ];

        foreach ($designations as $designation) {
            DB::table('mas_designation')->updateOrInsert(
                ['mas_designation_name' => $designation],
                [
                    'mas_designation_status_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        echo "Designation seeded successfully.\n";
    }
}
