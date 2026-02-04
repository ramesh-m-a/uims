<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/departments.csv');

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

                DB::table('mas_department')->updateOrInsert(
                    ['id' => $data['id']],
                    array_map(fn($v) => $v === '' ? null : $v, $data)
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
}
