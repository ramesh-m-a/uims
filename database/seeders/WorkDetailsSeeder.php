<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/work_details.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV not found: {$path}");
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        $count   = 0;
        $skipped = 0;
        $invalid = 0;

        DB::disableQueryLog();
        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {

                $data = array_combine($header, $row);

                if (! $data || empty($data['id'])) {
                    $invalid++;
                    continue;
                }

                DB::table('work_details')->updateOrInsert(
                    ['id' => $data['id']],
                    [
                        'work_details_basic_details_id'    => $data['work_details_basic_details_id'] ?: null,
                        'work_details_work_designation_id' => $data['work_details_work_designation_id'] ?: null,
                        'work_details_last_institution_name'=> $data['work_details_last_institution_name'] ?: null,

                        'work_details_date_of_appointment' => $this->normalizeDate($data['work_details_date_of_appointment']),
                        'work_details_from_date'           => $this->normalizeDate($data['work_details_from_date']),
                        'work_details_to_date'             => $this->normalizeDate($data['work_details_to_date']),
                        'work_details_date_of_joining'     => $this->normalizeDate($data['work_details_date_of_joining']),

                   //     'work_details_work_department_id'  => $data['work_details_work_department_id'] ?: null,
                        'work_details_till_date'           => $data['work_details_till_date'] ?: 0,
                    ]
                );

                $count++;

                if ($count % 1000 === 0) {
                    echo "Processed {$count} rows...\n";
                }
            }

            fclose($handle);
            DB::commit();

            echo "\nDONE ✅\n";
            echo "Inserted/Updated: {$count}\n";
            echo "Skipped: {$skipped}\n";
            echo "Invalid rows: {$invalid}\n";

        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }
    }

    private function normalizeDate($value)
    {
        if (empty($value)) return null;

        // Already valid
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // dd/mm/yy or dd/mm/yyyy
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{2,4})$/', $value, $m)) {
            $year = strlen($m[3]) === 2 ? '20' . $m[3] : $m[3];
            return "{$year}-{$m[2]}-{$m[1]}";
        }

        return null;
    }
}
