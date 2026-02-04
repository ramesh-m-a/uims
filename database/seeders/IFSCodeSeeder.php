<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IFSCodeSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/ifsc.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV not found: {$path}");
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        DB::disableQueryLog();

        $count   = 0;
        $skipped = 0;
        $invalid = 0;

        $errorPath = base_path('database/seeders/ifsc_errors.csv');
        $errorHandle = fopen($errorPath, 'w');
        fputcsv($errorHandle, array_merge($header, ['error_reason']));

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);

                if (! $data) {
                    $invalid++;
                    fputcsv($errorHandle, array_merge($row, ['Invalid CSV row']));
                    continue;
                }

                $branchId = trim($data['mas_ifsc_branch_id']);
                $bankId   = trim($data['mas_ifsc_bank_id']);

                // 🔒 FK validation (this prevents your crash)
                if (
                    !DB::table('mas_bank_branch')->where('id', $branchId)->exists() ||
                    !DB::table('mas_bank')->where('id', $bankId)->exists()
                ) {
                    $invalid++;
                    fputcsv($errorHandle, array_merge($row, ['Invalid FK (bank_id or branch_id)']));
                    continue;
                }

                DB::table('mas_ifsc')->updateOrInsert(
                    ['id' => $data['id']],
                    [
                        'mas_ifsc_number'      => $data['mas_ifsc_number'],
                        'mas_ifsc_bank_id'     => $bankId,
                        'mas_ifsc_branch_id'   => $branchId,
                        'mas_ifsc_micr'   => $data['mas_ifsc_code_micr'] ?: null,
                        'mas_ifsc_status_id'   => $data['mas_ifsc_status_id'] ?: 1,
                        'created_at'           => $data['created_at'] ?: now(),
                        'updated_at'           => $data['updated_at'] ?: now(),
                    ]
                );

                $count++;

                // 🔥 progress every 1000
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

        echo "\nDONE ✅\n";
        echo "Inserted/Updated: {$count}\n";
        echo "Skipped: {$skipped}\n";
        echo "Invalid FK / Errors: {$invalid}\n";
        echo "Errors saved to: {$errorPath}\n";
    }
}
