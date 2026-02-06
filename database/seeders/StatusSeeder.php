<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatusSeeder extends Seeder
{
    public function run1(): void
    {
        $path = base_path('database/seeders/status.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV not found: {$path}");
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        // -------------------------------------------------
        // ERROR FILE (CSV)
        // -------------------------------------------------
        $errorPath = base_path('database/seeders/status_import_errors.csv');

        if (!is_dir(dirname($errorPath))) {
            mkdir(dirname($errorPath), 0777, true);
        }

        $errorHandle = fopen($errorPath, 'w');
        fputcsv($errorHandle, [
            'line',
            'reason',
            'id',
            'raw_row',
            'error_message'
        ]);

        // -------------------------------------------------
        // LOG FILE (Laravel log)
        // -------------------------------------------------
        $logChannel = 'daily';

        $count = 0;
        $skipped = 0;
        $invalid = 0;
        $line = 1;

        DB::disableQueryLog();
        DB::beginTransaction();

        try {

            while (($row = fgetcsv($handle)) !== false) {

                $line++;

                try {

                    // -----------------------------------------
                    // COLUMN MISMATCH
                    // -----------------------------------------
                    if (count($row) !== count($header)) {
                        $invalid++;
                        fputcsv($errorHandle, [
                            $line,
                            'Column mismatch',
                            null,
                            json_encode($row),
                            null
                        ]);
                        continue;
                    }

                    $data = array_combine($header, $row);

                    // -----------------------------------------
                    // INVALID ID
                    // -----------------------------------------
                    if (!$data || empty($data['id'])) {
                        $invalid++;
                        fputcsv($errorHandle, [
                            $line,
                            'Missing ID',
                            null,
                            json_encode($row),
                            null
                        ]);
                        continue;
                    }

                    // -----------------------------------------
                    // CLEAN DATA
                    // -----------------------------------------
                    $cleanData = array_map(
                        fn($v) => $v === '' ? null : trim($v),
                        $data
                    );

                    // -----------------------------------------
                    // DB UPSERT
                    // -----------------------------------------
                    DB::table('mas_status')->updateOrInsert(
                        ['id' => $cleanData['id']],
                        $cleanData
                    );

                    $count++;

                    if ($count % 1000 === 0) {
                        echo "Processed {$count} rows...\n";
                    }

                } catch (\Throwable $rowException) {

                    $skipped++;

                    // CSV ERROR LOG
                    fputcsv($errorHandle, [
                        $line,
                        'DB Exception',
                        $data['id'] ?? null,
                        json_encode($row),
                        $rowException->getMessage()
                    ]);

                    // LARAVEL LOG
                    Log::channel($logChannel)->error('Status Import Row Failed', [
                        'line' => $line,
                        'row' => $row,
                        'error' => $rowException->getMessage(),
                        'trace' => $rowException->getTraceAsString()
                    ]);

                    continue;
                }
            }

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::channel($logChannel)->critical('Status Import Fatal Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;

        } finally {
            fclose($handle);
            fclose($errorHandle);
        }

        echo "\nDONE ✅\n";
        echo "Inserted/Updated: {$count}\n";
        echo "Skipped: {$skipped}\n";
        echo "Invalid rows: {$invalid}\n";
        echo "Error CSV: {$errorPath}\n";
    }
    public function run2(): void
    {
        $path = base_path('database/seeders/status.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV not found: {$path}");
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        $errorPath = base_path('database/seeders/status_import_errors.csv');
        $errorHandle = fopen($errorPath, 'w');

        fputcsv($errorHandle, ['line', 'reason', 'raw_row', 'error']);

        DB::disableQueryLog();
        DB::beginTransaction();

        $line = 1;
        $count = 0;

        try {

            while (($row = fgetcsv($handle)) !== false) {

                $line++;

                try {

                    if (count($row) !== count($header)) {
                        fputcsv($errorHandle, [$line, 'Column mismatch', json_encode($row), null]);
                        continue;
                    }

                    $data = array_combine($header, $row);

                    // -----------------------------------
                    // CLEAN DATA
                    // -----------------------------------
                    $clean = array_map(fn($v) => $v === '' ? null : trim($v), $data);

                    // -----------------------------------
                    // ID LOGIC
                    // -----------------------------------
                    $id = $clean['id'] ?? null;

                    if ($id) {

                        // UPSERT USING ID
                        DB::table('mas_status')->updateOrInsert(['id' => $id], $clean);

                    } else {

                        // INSERT WITHOUT ID (AUTO INCREMENT)
                        unset($clean['id']);

                        DB::table('mas_status')->insert($clean);
                    }

                    $count++;

                } catch (\Throwable $rowException) {

                    fputcsv($errorHandle, [$line, 'DB Exception', json_encode($row), $rowException->getMessage()]);

                    Log::error('Status import row failed', ['line' => $line, 'row' => $row, 'error' => $rowException->getMessage()]);
                }
            }

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::critical('Status import failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            throw $e;

        } finally {
            fclose($handle);
            fclose($errorHandle);
        }

        echo "\nDONE ✅ Imported {$count}\n";
    }

    public function run(): void
    {
        $path = base_path('database/seeders/status.csv');

        if (!file_exists($path)) {
            throw new \Exception("CSV not found: {$path}");
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        DB::disableQueryLog();

        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {

            $data = array_combine($header, $row);

            $clean = array_map(
                fn($v) => $v === '' ? null : trim($v),
                $data
            );

            DB::table('mas_status')->insert($clean);

            $count++;
        }

        fclose($handle);

        echo "\nDONE ✅ Inserted {$count} rows\n";
    }
}
