<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/bank_details.csv');
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);

                DB::table('bank_details')->updateOrInsert(
                    ['id' => $data['id']],
                    array_map(fn($v) => $v === '' ? null : $v, $data)
                );
            }

            fclose($handle);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }
    }
}
