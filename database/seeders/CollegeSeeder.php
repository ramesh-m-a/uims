<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CollegeSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/colleges.csv');

        if (!file_exists($path)) {
            echo "College CSV not found at {$path}\n";
            return;
        }

        echo "Reading CSV: {$path}\n";

        $handle = fopen($path, 'r');
        if (!$handle) {
            echo "Unable to open file\n";
            return;
        }

        // Log file
        //  $logPath = storage_path('logs/college_rejects.csv');
        $logPath = base_path('database/seeders/college_rejects.csv');
        File::put($logPath, "reason,stream_id,code,name,raw\n");

        // Header
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        echo "Header detected: " . implode(', ', $header) . "\n";

        DB::disableQueryLog();

        // Load valid parents
        $validStreamIds = DB::table('mas_stream')->pluck('id')->flip();

        $count = 0;
        $skipped = 0;
        $invalid = 0;
        $malformed = 0;
        $generatedCodes = 0;

        $autoCode = 1;

        while (($row = fgetcsv($handle)) !== false) {

            // Malformed CSV
            if (count($row) !== count($header)) {
                File::append($logPath, "malformed,,,,\"" . json_encode($row) . "\"\n");
                $malformed++;
                continue;
            }

            $data = array_combine($header, $row);

            $streamId = trim($data['mas_college_stream_id'] ?? '');
            $code     = trim($data['mas_college_code'] ?? '');
            $name     = trim($data['mas_college_name'] ?? '');

            // Required
            if ($streamId === '' || $name === '') {
                File::append($logPath, "missing_required,$streamId,$code,$name,\"" . json_encode($row) . "\"\n");
                $skipped++;
                continue;
            }

            // Invalid FK
            if (!isset($validStreamIds[$streamId])) {
                File::append($logPath, "invalid_stream,$streamId,$code,$name,\"" . json_encode($row) . "\"\n");
                $invalid++;
                continue;
            }

            // Auto-generate code if missing
            if ($code === '') {
                $code = 'AUTO-' . str_pad($autoCode++, 6, '0', STR_PAD_LEFT);
                $generatedCodes++;
            }

            // Fallback uniqueness protection
            $unique = [
                'mas_college_stream_id' => $streamId,
                'mas_college_code'      => $code,
            ];

            DB::table('mas_college')->updateOrInsert(
                $unique,
                [
                    'mas_college_name'        => $name,
                    'mas_college_exam_centre' => $data['mas_college_exam_centre'] ?? 1,
                    'mas_college_type'        => $data['mas_college_type'] ?? 'G',
                    'mas_college_is_internal' => $data['mas_college_is_internal'] ?? 1,
                    'mas_college_status_id'   => 1,
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]
            );

            $count++;

            if ($count % 500 === 0) {
                echo "Processed {$count} rows...\n";
            }
        }

        fclose($handle);

        echo "\nDONE ✅\n";
        echo "Inserted/Updated: {$count}\n";
        echo "Generated Codes: {$generatedCodes}\n";
        echo "Skipped (missing required): {$skipped}\n";
        echo "Skipped (invalid stream_id): {$invalid}\n";
        echo "Malformed rows: {$malformed}\n";
        echo "Reject log: {$logPath}\n";
    }
}
