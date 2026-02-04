<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersFromCsvSeeder extends Seeder
{
    private function clean($value)
    {
        if ($value === null) return null;

        $value = trim($value);
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, WINDOWS-1252');
        $value = preg_replace('/[^\PC\s]/u', '', $value);

        return $value;
    }

    private function normalizeFk($value, $validSet)
    {
        // treat null, '', '0', 0 as invalid
        if ($value === null) return null;

        $value = trim((string)$value);

        if ($value === '' || $value === '0') {
            return null;
        }

        $int = (int) $value;

        return isset($validSet[$int]) ? $int : null;
    }

    public function run(): void
    {
        $path = base_path('database/seeders/users.csv');

        if (!file_exists($path)) {
            echo "Users CSV not found at {$path}\n";
            return;
        }

        echo "Reading CSV: {$path}\n";

        $handle = fopen($path, 'r');
        if (!$handle) {
            echo "Unable to open file\n";
            return;
        }

        // -------------------------
        // ERROR LOG
        // -------------------------
        $errorPath = base_path('database/seeders/users_import_errors.csv');

        $dir = dirname($errorPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $errorHandle = fopen($errorPath, 'w');
        if (!$errorHandle) {
            echo "❌ Cannot create error log: {$errorPath}\n";
            return;
        }

        fputcsv($errorHandle, ['line', 'reason', 'id', 'email', 'mobile']);
        fflush($errorHandle);

        // -------------------------
        // HEADER
        // -------------------------
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        echo "Header detected: " . implode(', ', $header) . "\n";

        DB::disableQueryLog();

        // -------------------------
        // VALID FK SETS
        // -------------------------
      /*  $validStreams      = DB::table('mas_stream')->pluck('id')->flip();
        $validColleges     = DB::table('mas_college')->pluck('id')->flip();
        $validDesignations = DB::table('mas_designation')->pluck('id')->flip();*/

        $count   = 0;
        $skipped = 0;
        $fixedFk = 0;
        $line    = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (count($row) !== count($header)) {
                $skipped++;
                fputcsv($errorHandle, [$line, 'Malformed column count']);
                continue;
            }

            $data = array_combine($header, $row);

            $id     = $this->clean($data['id'] ?? '');
            $email  = $this->clean($data['email'] ?? '');
            $mobile = $this->clean($data['user_mobile'] ?? '');

            if ($id === '') {
                $skipped++;
                fputcsv($errorHandle, [$line, 'Missing ID']);
                continue;
            }

            if ($email === '' && $mobile === '') {
                $skipped++;
                fputcsv($errorHandle, [$line, 'Missing email & mobile', $id]);
                continue;
            }

            if ($email === '') {
                $email = "user{$id}@dummy.local";
            }

            // -------------------------
            // FK NORMALIZATION
            // -------------------------
         /*   $streamId      = $this->normalizeFk($data['user_stream_id'] ?? null, $validStreams);
            $collegeId     = $this->normalizeFk($data['user_college_id'] ?? null, $validColleges);
            $designationId = $this->normalizeFk($data['user_designation_id'] ?? null, $validDesignations);*/

          /*  if (($data['user_stream_id'] ?? null) && !$streamId) $fixedFk++;
            if (($data['user_college_id'] ?? null) && !$collegeId) $fixedFk++;
            if (($data['user_designation_id'] ?? null) && !$designationId) $fixedFk++;*/

            // -------------------------
            // NAME
            // -------------------------
          /*  $name = trim(
                ($data['fname'] ?? '') . ' ' .
                ($data['mname'] ?? '') . ' ' .
                ($data['lname'] ?? '')
            );

            if ($name === '') {
                $name = 'User ' . $partials;
            }*/

            $name = trim($data['name']);

            $streamId = !empty($data['user_stream_id']) ? (int) $data['user_stream_id'] : null;
            $collegeId = !empty($data['user_college_id']) ? (int) $data['user_college_id'] : null;
            $designationId = !empty($data['user_designation_id']) ? (int) $data['user_designation_id'] : null;

            // -------------------------
            // INSERT
            // -------------------------
            DB::table('users')->updateOrInsert(
                ['id' => (int)$id],
                [
                    'name'                  => $name,
                    'email'                 => $email,
                    'mobile'                => $mobile ?: null,
                    'photo_path'            => $this->clean($data['user_photo_path'] ?? null),
                    'user_stream_id'        => $streamId,
                    'user_college_id'       => $collegeId,
                    'user_designation_id'   => $designationId,
                    'password'              => Hash::make('Password@123'),
                    'force_password_change' => 1,
                  //  'created_at'            => now(),
                  //  'updated_at'            => now(),
                    'user_status_id'        => 1,
                    'user_role_id'          => 5,
                ]
            );

            $count++;

            if ($count % 1000 === 0) {
                echo "Processed {$count} rows...\n";
            }
        }

        fclose($handle);
        fclose($errorHandle);

        echo "\nDONE ✅\n";
        echo "Inserted/Updated: {$count}\n";
        echo "Skipped: {$skipped}\n";
        echo "Invalid FKs auto-fixed to NULL: {$fixedFk}\n";
        echo "Errors saved to: {$errorPath}\n";
    }
}
