<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExaminerDetailsSeeder extends Seeder
{
    public function run(): void
    {
      //  $path = storage_path('app/seeders/examiner_details.csv');
        $path = base_path('database/seeders/examiner_details.csv');

        if (!file_exists($path)) {
            $this->command->error("CSV not found: {$path}");
            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        DB::disableQueryLog();
        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {

                $data = array_combine($header, $row);

                DB::table('examiner_details')->updateOrInsert(
                    ['id' => $data['id']],
                    [
                        'examiner_details_basic_details_id'        => $data['examiner_details_basic_details_id'],
                        'examiner_details_qualification_details_id'=> $data['examiner_details_qualification_details_id'] ?: null,
                        'examiner_details_type'                    => $data['examiner_details_type'] ?: 2,
                        'examiner_details_year_id'                 => $data['examiner_details_year_id'] ?: null,
                        'examiner_details_month_id'                => $data['examiner_details_month_id'] ?: null,
                        'examiner_details_recognition_date'        => $data['examiner_details_recognition_date'] ?: null,
                        'examiner_details_rank'                    => $data['examiner_details_rank'] ?: null,
                        'examiner_details_status_id'               => $data['examiner_details_status_id'] ?: 1,
                    ]
                );
            }

            fclose($handle);
            DB::commit();

            $this->command->info('examiner_details seeded successfully from CSV.');

        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }
    }
}
