<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentPerBatchSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/student_per_batch.csv');

        if (!file_exists($path)) {
            $this->command->error("Missing CSV: $path");
            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {

            $data = array_combine($header, $row);

            // FK SAFETY CHECK
            $degreeExists = DB::table('mas_degree')
                ->where('id', $data['mas_student_per_batch_degree_id'])
                ->exists();

            $subjectExists = DB::table('mas_subject')
                ->where('id', $data['mas_student_per_batch_subject_id'])
                ->exists();

            if (!$degreeExists || !$subjectExists) {
                $this->command->warn(
                    "Skipping row. Missing FK → Subject: {$data['mas_student_per_batch_subject_id']} Degree: {$data['mas_student_per_batch_degree_id']}"
                );
                continue;
            }

            DB::table('mas_student_per_batch')->updateOrInsert(
                [
                    'mas_student_per_batch_subject_id' => $data['mas_student_per_batch_subject_id'],
                    'mas_student_per_batch_degree_id' => $data['mas_student_per_batch_degree_id'],
                ],
                [
                    'mas_student_per_batch_total_number' => $data['mas_student_per_batch_total_number'],
                    'mas_student_per_batch_status_id' => $data['mas_student_per_batch_status_id'] ?: 50,
                    'mas_student_per_batch_per_day' => $data['mas_student_per_batch_per_day'],
                    'created_at' => $data['created_at'] ?: now(),
                    'updated_at' => $data['updated_at'] ?: now(),
                    'created_by' => $data['created_by'] ?: null,
                    'updated_by' => $data['updated_by'] ?: null,
                ]
            );
        }

    }
}
