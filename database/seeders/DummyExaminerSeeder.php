<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DummyExaminerSeeder extends Seeder
{
    public function runold(): void
    {
        // -----------------------
        // 1. mas_status
        // -----------------------

        $statuses = [
            32 => ['ACTIVE',   'Active',   '#28a745'],
            52 => ['APPROVED', 'Approved', '#17a2b8'],
            44 => ['ASSIGNED', 'Assigned', '#007bff'],
            31 => ['INACTIVE', 'Inactive', '#6c757d'],
        ];

        foreach ($statuses as $id => [$name, $code, $color]) {
            DB::table('mas_status')->updateOrInsert(
                ['id' => $id],
                [
                    'mas_status_code'          => $code,
                    'mas_status_name'          => $name,
                    'mas_status_label_colour'  => $color,
                ]
            );
        }

        // -----------------------
        // 2. Create Colleges (centres)
        // -----------------------
        $centreIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $centreIds[] = DB::table('mas_college')->insertGetId([
                'mas_college_name' => "Dummy College {$i}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -----------------------
        // 3. Create Users
        // -----------------------
        $userIds = [];
        for ($i = 1; $i <= 20; $i++) {
            $userIds[] = DB::table('users')->insertGetId([
                'fname' => "Examiner",
                'mname' => "",
                'lname' => "{$i}",
                'email' => "examiner{$i}@dummy.test",
                'password' => bcrypt('password'),
                'user_mobile_number' => '90000000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'user_college_id' => $centreIds[array_rand($centreIds)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -----------------------
        // 4. basic_details
        // -----------------------
        $basicIds = [];
        foreach ($userIds as $uid) {
            $basicIds[] = DB::table('basic_details')->insertGetId([
                'basic_details_user_id' => $uid,
                'basic_details_department_id' => 464, // must match picker query
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -----------------------
        // 5. examiner_details
        // -----------------------
        foreach ($basicIds as $i => $bid) {
            DB::table('examiner_details')->insert([
                'examiner_details_basic_details_id' => $bid,
                'examiner_details_type' => $i % 2 === 0 ? 1 : 2, // mix Internal / External
                'examiner_details_rank' => rand(1, 10),
                'examiner_details_status_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Dummy examiner data seeded successfully.');
    }

    public function run(): void
    {
        // Optional: clear old data
        DB::table('examiner_details')->truncate();

        $rows = [];

        for ($i = 1; $i <= 50; $i++) {
            $rows[] = [
                'examiner_details_basic_details_id' => $i,   // fake but valid
                'examiner_details_type' => $i % 2 === 0 ? 1 : 2, // 1 = Internal, 2 = External
                'examiner_details_rank' => rand(1, 10),
                'examiner_details_status_id' =>1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('examiner_details')->insert($rows);

        $this->command->info('Seeded examiner_details only.');
    }
}
