<?php

namespace Database\Seeders;

use App\Models\Admin\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    private function clean($value)
    {
        if ($value === null) return null;

        $value = trim($value);
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, WINDOWS-1252');
        $value = preg_replace('/[^\PC\s]/u', '', $value);

        return $value === '' ? null : $value;
    }

    private function normalizeInt($value)
    {
        if ($value === null) return null;

        $value = trim((string)$value);

        if ($value === '' || $value === '0') {
            return null;
        }

        return is_numeric($value) ? (int)$value : null;
    }

    /*public function run(): void
    {
        $path = base_path('database/seeders/users.csv');

        if (!file_exists($path)) {
            echo "❌ Users CSV not found: {$path}\n";
            return;
        }

        echo "📄 Reading CSV: {$path}\n";

        $handle = fopen($path, 'r');
        if (!$handle) {
            echo "❌ Unable to open file\n";
            return;
        }

        // -------------------------
        // ERROR LOG
        // -------------------------
        $errorPath = base_path('database/seeders/users_import_errors.csv');
        if (!is_dir(dirname($errorPath))) {
            mkdir(dirname($errorPath), 0777, true);
        }

        $errorHandle = fopen($errorPath, 'w');
        fputcsv($errorHandle, ['line', 'reason', 'id', 'email', 'mobile']);

        // -------------------------
        // HEADER
        // -------------------------
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        echo "Header detected: " . implode(', ', $header) . "\n";

        DB::disableQueryLog();

        $count = 0;
        $skipped = 0;
        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (count($row) !== count($header)) {
                $skipped++;
                fputcsv($errorHandle, [$line, 'Malformed column count']);
                continue;
            }

            $data = array_combine($header, $row);

            $partials = $this->clean($data['id'] ?? null);
            $email = $this->clean($data['email'] ?? null);
            $mobile = $this->clean($data['user_mobile'] ?? null);

            if (!$partials) {
                $skipped++;
                fputcsv($errorHandle, [$line, 'Missing ID']);
                continue;
            }

            if (!$email && !$mobile) {
                $skipped++;
                fputcsv($errorHandle, [$line, 'Missing email & mobile', $partials]);
                continue;
            }

            if (!$email) {
                $email = "user{$partials}@dummy.local";
            }

            $name = $this->clean($data['name'] ?? null);
            if (!$name) {
                $name = "User {$partials}";
            }

            $streamId = $this->normalizeInt($data['user_stream_id'] ?? null);
            $collegeId = $this->normalizeInt($data['user_college_id'] ?? null);
            $designationId = $this->normalizeInt($data['user_designation_id'] ?? null);

            try {
                DB::table('users')->updateOrInsert(['id' => (int)$partials], ['name' => $name, 'email' => $email, 'mobile' => $mobile, 'photo_path' => $this->clean($data['user_photo_path'] ?? null),

                        'user_stream_id' => $streamId, 'user_college_id' => $collegeId, 'user_designation_id' => $designationId,

                        'password' => Hash::make('Password@123'), 'force_password_change' => 1,

                        'user_status_id' => 1, 'user_role_id' => 5,

                        'updated_at' => now(), 'created_at' => now(),]);
            } catch (\Throwable $e) {
                $skipped++;
                fputcsv($errorHandle, [$line, $e->getMessage(), $partials, $email, $mobile]);
                continue;
            }

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
        echo "Errors saved to: {$errorPath}\n";
    }*/

    /*public function run(): void
    {
        $path = base_path('database/seeders/users.csv');

        if (!file_exists($path)) {
            echo "❌ CSV not found: {$path}\n";
            return;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            echo "❌ Cannot open file\n";
            return;
        }

        echo "Reading: {$path}\n";

        // Read header
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        DB::disableQueryLog();

        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {

            if (count($row) !== count($header)) {
                continue;
            }

            $data = array_combine($header, $row);

            $partials = (int)$data['id'];

            if (!$partials) {
                continue;
            }

            DB::table('users')->updateOrInsert(['id' => $partials], ['name' => trim($data['name']), 'email' => trim($data['email']) ?: "user{$partials}@dummy.local", 'mobile' => trim($data['mobile']) ?: null, 'photo_path' => trim($data['photo_path']) ?: null,

                    'user_stream_id' => $data['user_stream_id'] !== '' ? (int)$data['user_stream_id'] : null, 'user_college_id' => $data['user_college_id'] !== '' ? (int)$data['user_college_id'] : null, 'user_designation_id' => $data['user_designation_id'] !== '' ? (int)$data['user_designation_id'] : null,

                    'password' => Hash::make('Password@123'), 'force_password_change' => 1,

                    'user_status_id' => 1, 'user_role_id' => 5,

                    'created_at' => now(), 'updated_at' => now(),]);

            $count++;

            if ($count % 1000 === 0) {
                echo "Imported {$count} rows...\n";
            }
        }

        fclose($handle);

        echo "\nDONE ✅ Imported {$count} users\n";
    }*/

    public function runhardcode(): void
    {
        $path = base_path('database/seeders/users.csv');

        if (!file_exists($path)) {
            echo "❌ CSV not found\n";
            return;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            echo "❌ Cannot open CSV\n";
            return;
        }

        echo "Importing users...\n";

        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        DB::disableQueryLog();
        DB::beginTransaction();

        $passwordHash = Hash::make('Password@123');

        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {

            if (count($row) !== count($header)) {
                continue;
            }

            $data = array_combine($header, $row);

            $id = (int)$data['id'];
            if (!$id) continue;

            DB::statement("
                INSERT INTO users
                (id, name, email, mobile, photo_path,
                 user_stream_id, user_college_id, user_designation_id,
                 password, force_password_change,
                 user_status_id, user_role_id, created_at, updated_at)

                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1, 5, NOW(), NOW())

                ON DUPLICATE KEY UPDATE
                    name = VALUES(name),
                    email = VALUES(email),
                    mobile = VALUES(mobile),
                    photo_path = VALUES(photo_path),
                    user_stream_id = VALUES(user_stream_id),
                    user_college_id = VALUES(user_college_id),
                    user_designation_id = VALUES(user_designation_id),
                    updated_at = NOW()
            ", [$id, trim($data['name']), trim($data['email']) ?: "user{$id}@dummy.local", trim($data['mobile']) ?: null, trim($data['photo_path']) ?: null, $data['user_stream_id'] !== '' ? (int)$data['user_stream_id'] : null, $data['user_college_id'] !== '' ? (int)$data['user_college_id'] : null, $data['user_designation_id'] !== '' ? (int)$data['user_designation_id'] : null, $passwordHash,]);

            $count++;

            if ($count % 5000 === 0) {
                echo "Imported {$count}\n";
            }
        }

        DB::commit();
        fclose($handle);

        echo "\nDONE ✅ Imported {$count} users\n";
    }

    public function run(): void
    {
        $path = base_path('database/seeders/users.csv');

        $passwordHash = Hash::make('Password@123');

        if (!file_exists($path)) {
            echo "❌ CSV not found\n";
            return;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            echo "❌ Cannot open CSV\n";
            return;
        }

        echo "🚀 Importing users from CSV...\n";

        $header = fgetcsv($handle);
        if (!$header) {
            echo "❌ Empty CSV\n";
            return;
        }

        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        DB::disableQueryLog();
        DB::beginTransaction();

        $processed = 0;
        $upserted = 0;
        $skipped = 0;
        $errors = 0;

        $errorLogPath = base_path('database/seeders/users_import_errors.csv');
        $errorHandle = fopen($errorLogPath, 'w');
        fputcsv($errorHandle, ['row_number', 'reason', 'raw_data']);

        try {

            while (($row = fgetcsv($handle)) !== false) {

                $processed++;

                if (count($row) !== count($header)) {
                    $skipped++;
                    fputcsv($errorHandle, [$processed, 'Column mismatch', json_encode($row)]);
                    continue;
                }

                $data = array_combine($header, $row);

                $id = (int)($data['id'] ?? 0);
                if (!$id) {
                    $skipped++;
                    fputcsv($errorHandle, [$processed, 'Missing ID', json_encode($row)]);
                    continue;
                }

                try {

                    DB::statement("
                    INSERT INTO users
                    (
                        id,
                        name,
                        email,
                        mobile,
                        photo_path,
                        user_stream_id,
                        user_college_id,
                        user_designation_id,
                        password,
                        force_password_change,
                        user_status_id,
                        user_role_id,
                        created_at,
                        updated_at
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)

                    ON DUPLICATE KEY UPDATE
                        name = VALUES(name),
                        email = VALUES(email),
                        mobile = VALUES(mobile),
                        photo_path = VALUES(photo_path),
                        user_stream_id = VALUES(user_stream_id),
                        user_college_id = VALUES(user_college_id),
                        user_designation_id = VALUES(user_designation_id),
                        password = VALUES(password),
                        force_password_change = VALUES(force_password_change),
                        user_status_id = VALUES(user_status_id),
                        user_role_id = VALUES(user_role_id),
                        updated_at = VALUES(updated_at)
                ", [
                        $id,
                        trim($data['name'] ?? ''),
                        trim($data['email'] ?? '') ?: null,
                        trim($data['mobile'] ?? '') ?: null,
                        trim($data['photo_path'] ?? '') ?: null,
                        ($data['user_stream_id'] ?? '') !== '' ? (int)$data['user_stream_id'] : null,
                        ($data['user_college_id'] ?? '') !== '' ? (int)$data['user_college_id'] : null,
                        ($data['user_designation_id'] ?? '') !== '' ? (int)$data['user_designation_id'] : null,
                        $passwordHash,
                        1,
                        ($data['user_status_id'] ?? '') !== '' ? (int)$data['user_status_id'] : null,
                        ($data['user_role_id'] ?? '') !== '' ? (int)$data['user_role_id'] : null,
                        trim($data['created_at'] ?? '') ?: now(),
                        trim($data['updated_at'] ?? '') ?: now(),
                    ]);

                    $upserted++;

                } catch (\Throwable $e) {

                    $errors++;
                    fputcsv($errorHandle, [
                        $processed,
                        $e->getMessage(),
                        json_encode($row)
                    ]);
                }

                if ($processed % 5000 === 0) {
                    echo "Processed: {$processed} | Upserted: {$upserted} | Skipped: {$skipped} | Errors: {$errors}\n";
                }
            }

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();
            fclose($handle);
            fclose($errorHandle);

            echo "❌ IMPORT FAILED: " . $e->getMessage() . "\n";
            return;
        }

        fclose($handle);
        fclose($errorHandle);

        echo "\n✅ IMPORT COMPLETE\n";
        echo "Processed : {$processed}\n";
        echo "Upserted  : {$upserted}\n";
        echo "Skipped   : {$skipped}\n";
        echo "Errors    : {$errors}\n";
        echo "Error Log : {$errorLogPath}\n";
    }


}


        /*
        |--------------------------------------------------------------------------
        | SYSTEM USER (ROOT)
        |--------------------------------------------------------------------------
        | ⚠️ No master dependencies here
        | All FK columns MUST be nullable in user table
        */

    /*    User::firstOrCreate(
            ['email' => 'system@uims.com'],
            [
                'name'              => 'System',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),

                // ✅ IMPORTANT: no master FKs at bootstrap
                'user_stream_id'      => null,
                'user_college_id'     => null,
                'user_designation_id' => null,
                'user_status_id'      => 1,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@uims.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),

                // ✅ STILL NULL
                'user_stream_id'      => null,
                'user_college_id'     => null,
                'user_designation_id' => null,
                'user_status_id'      => 1,
            ]
        );*/
