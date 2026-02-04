<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/address_details.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV not found: {$path}");
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        $count   = 0;
        $skipped = 0;
        $invalid = 0;

        $errorPath = base_path('database/seeders/address_details_errors.csv');
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
                    DB::table('address_details')->updateOrInsert(
                        ['id' => $data['id']],
                        array_map(fn ($v) => $v === '' ? null : $v, $data)
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
