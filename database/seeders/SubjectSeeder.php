<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/subjects.csv');

        if (!file_exists($path)) {
            $this->command->error("Missing CSV: $path");
            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);

                DB::table('mas_subject')->updateOrInsert(
                    ['id' => $data['id']],
                    [
                        'mas_subject_stream_id'     => $data['mas_subject_stream_id'],
                        'mas_subject_degree_id'     => $data['mas_subject_degree_id'],
                        'mas_subject_code'          => $data['mas_subject_code'] ?: null,
                        'mas_subject_name'          => $data['mas_subject_name'],
                        'mas_subject_status_id'     => $data['mas_subject_status_id'] ?: 1,
                        'mas_subject_department_id' => $data['mas_subject_department_id'] ?: null,
                        'created_at'                => $data['created_at'] ?: now(),
                        'updated_at'                => $data['updated_at'] ?: now(),
                    ]
                );
            }

            fclose($handle);
            DB::commit();
            $this->command->info('mas_subject seeded successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }
    }
}
