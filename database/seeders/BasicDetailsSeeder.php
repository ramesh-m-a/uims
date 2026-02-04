<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BasicDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/basic_details.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV not found: {$path}");
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        $count   = 0;
        $skipped = 0;
        $invalid = 0;

        $errorPath = base_path('database/seeders/basic_details_errors.csv');
        $errorHandle = fopen($errorPath, 'w');
        fputcsv($errorHandle, array_merge($header, ['error_reason']));

        DB::disableQueryLog();
        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {

                $data = array_combine($header, $row);

                if (! $data) {
                    $invalid++;
                    fputcsv($errorHandle, array_merge($row, ['Invalid CSV structure']));
                    continue;
                }

                if (empty($data['id'])) {
                    $invalid++;
                    fputcsv($errorHandle, array_merge($row, ['Missing ID']));
                    continue;
                }

                try {
                    DB::table('basic_details')->updateOrInsert(
                        ['id' => $data['id']],
                        [
                            'basic_details_user_id'             => $data['basic_details_user_id'] ?: null,
                            'basic_details_gender_id'           => $data['basic_details_gender_id'] ?: null,
                            'basic_details_dob'                 => $data['basic_details_dob'] ?: null,
                            'basic_details_father_name'         => $data['basic_details_father_name'] ?: null,
                            'basic_details_department_id'       => $data['basic_details_department_id'] ?: 0,
                            'basic_details_religion_id'         => $data['basic_details_religion_id'] ?: 0,
                            'basic_details_category_id'         => $data['basic_details_category_id'] ?: 0,
                            'basic_details_status_id'           => $data['basic_details_status_id'] ?: 3,
                            'basic_details_is_administrative_id'=> $data['basic_details_is_administrative_id'] ?: 0,
                            'created_at'                        => $data['created_at'] ?: null,
                            'updated_at'                        => $data['updated_at'] ?: null,
                            'created_by'                        => $data['created_by'] ?: null,
                            'updated_by'                        => $data['updated_by'] ?: null,
                        ]
                    );

                    $count++;

                    if ($count % 1000 === 0) {
                        echo "Processed {$count} rows...\n";
                    }

                } catch (\Throwable $e) {
                    $invalid++;
                    fputcsv($errorHandle, array_merge($row, [$e->getMessage()]));
                }
            }

            fclose($handle);
            fclose($errorHandle);
            DB::commit();

            echo "\nDONE ✅\n";
            echo "Inserted/Updated: {$count}\n";
            echo "Skipped: {$skipped}\n";
            echo "Invalid rows: {$invalid}\n";
            echo "Errors saved to: {$errorPath}\n";

        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            fclose($errorHandle);
            throw $e;
        }
    }
}
