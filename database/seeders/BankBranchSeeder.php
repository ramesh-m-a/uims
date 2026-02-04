<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankBranchSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/bank_branches.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV not found: " . $path);
        }

        $rows = array_map('str_getcsv', file($path));
        $header = array_shift($rows);

        $count   = 0;
        $skipped = 0;
        $invalid = 0;

        $seen = [];

        $errorPath = base_path('database/seeders/bank_branch_errors.csv');
        $errorHandle = fopen($errorPath, 'w');
        fputcsv($errorHandle, array_merge($header, ['error_reason']));

        foreach ($rows as $row) {
            try {
                $data = array_combine($header, $row);

                if (! $data) {
                    $invalid++;
                    fputcsv($errorHandle, array_merge($row, ['Invalid CSV row']));
                    continue;
                }

                $bankId = trim($data['mas_bank_branch_bank_id']);
                $branch = strtoupper(trim($data['mas_bank_branch_branch_name']));

                // Validate FK
                if (! DB::table('mas_bank')->where('id', $bankId)->exists()) {
                    $invalid++;
                    fputcsv($errorHandle, array_merge($row, ['Invalid bank_id']));
                    continue;
                }

                // Deduplication key
                $key = $bankId . '|' . $branch;

                if (isset($seen[$key])) {
                    $skipped++;
                    continue;
                }

                $seen[$key] = true;

                DB::table('mas_bank_branch')->updateOrInsert(
                    [
                        'id' => (int) $data['id'], // ✅ preserve original ID
                    ],
                    [
                        'mas_bank_branch_bank_id'     => $bankId,
                        'mas_bank_branch_branch_name' => $branch,

                        'mas_bank_branch_address_1' => $data['mas_bank_branch_address_1'] ?: null,
                        'mas_bank_branch_address_2' => $data['mas_bank_branch_address_2'] ?: null,
                        'mas_bank_branch_city'      => $data['mas_bank_branch_city'] ?: null,
                        'mas_bank_branch_state'     => $data['mas_bank_branch_state'] ?: null,
                        'mas_bank_branch_status_id' => 1,
                        'created_at'                => now(),
                        'updated_at'                => now(),
                    ]
                );

                $count++;

                // ✅ Progress every 1000
                if ($count % 1000 === 0) {
                    echo "Processed {$count} rows...\n";
                }

            } catch (\Throwable $e) {
                $invalid++;
                fputcsv($errorHandle, array_merge($row, [$e->getMessage()]));
            }
        }

        fclose($errorHandle);

        echo "\nDONE ✅\n";
        echo "Inserted/Updated: {$count}\n";
        echo "Skipped (duplicates): {$skipped}\n";
        echo "Invalid rows: {$invalid}\n";
        echo "Errors saved to: {$errorPath}\n";
    }
}
