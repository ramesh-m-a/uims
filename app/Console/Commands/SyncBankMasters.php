<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncBankMasters extends Command
{
    protected $signature = 'sync:bank_masters';
    protected $description = 'Sync banks, branches and IFSC from CSV with clean IDs and correct mapping (streaming, safe for large files)';

    public function handle()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $this->info("Starting bank masters sync...");

        $bankCsv   = base_path('database/seeders/banks.csv');
        $branchCsv = base_path('database/seeders/bank_branches.csv');
        $ifscCsv   = base_path('database/seeders/ifsc.csv');

        foreach ([$bankCsv, $branchCsv, $ifscCsv] as $file) {
            if (!file_exists($file)) {
                $this->error("Missing file: {$file}");
                return Command::FAILURE;
            }
        }

        // -----------------------------
        // RESET TABLES
        // -----------------------------
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('mas_ifsc')->truncate();
        DB::table('mas_bank_branch')->truncate();
        DB::table('mas_bank')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->info("Tables truncated");

        // -----------------------------
        // 1. IMPORT BANKS
        // -----------------------------
        $this->info("Importing banks...");
        $bankMap = [];

        $handle = fopen($bankCsv, 'r');
        $header = fgetcsv($handle);
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) continue;

            $data = array_combine($header, $row);

            $oldId = (int) $data['id'];

            $newId = DB::table('mas_bank')->insertGetId([
                'mas_bank_name'      => trim($data['mas_bank_name']),
                'mas_bank_status_id' => $data['mas_bank_status_id'] ?? 1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            $bankMap[$oldId] = $newId;
            $count++;

            if ($count % 100 === 0) {
                $this->info("Banks processed: {$count}");
            }
        }
        fclose($handle);

        $this->info("Banks synced: {$count}");

        // -----------------------------
        // 2. IMPORT BRANCHES
        // -----------------------------
        $this->info("Importing branches...");
        $branchMap = [];

        $handle = fopen($branchCsv, 'r');
        $header = fgetcsv($handle);

        $inserted = 0;
        $reused   = 0;
        $skipped  = 0;
        $processed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $processed++;

            if (count($row) !== count($header)) {
                $skipped++;
                continue;
            }

            $data = array_combine($header, $row);

            $oldBranchId = (int) ($data['id'] ?? 0);
            $oldBankId   = (int) ($data['mas_bank_branch_bank_id'] ?? 0);

            if (!$oldBranchId || !$oldBankId || !isset($bankMap[$oldBankId])) {
                $skipped++;
                continue;
            }

            $bankId = $bankMap[$oldBankId];
            $branch = strtoupper(trim($data['mas_bank_branch_branch_name'] ?? ''));

            if (!$branch) {
                $skipped++;
                continue;
            }

            $existing = DB::table('mas_bank_branch')
                ->where('mas_bank_branch_bank_id', $bankId)
                ->where('mas_bank_branch_branch_name', $branch)
                ->first();

            if ($existing) {
                $branchMap[$oldBranchId] = $existing->id;
                $reused++;
            } else {
                $newId = DB::table('mas_bank_branch')->insertGetId([
                    'mas_bank_branch_bank_id'     => $bankId,
                    'mas_bank_branch_branch_name' => $branch,
                    'mas_bank_branch_address_1'   => $data['mas_bank_branch_address_1'] ?? null,
                    'mas_bank_branch_address_2'   => $data['mas_bank_branch_address_2'] ?? null,
                    'mas_bank_branch_city'        => $data['mas_bank_branch_city'] ?? null,
                    'mas_bank_branch_state'       => $data['mas_bank_branch_state'] ?? null,
                    'mas_bank_branch_status_id'   => $data['mas_bank_branch_status_id'] ?? 1,
                    'created_at'                 => now(),
                    'updated_at'                 => now(),
                ]);

                $branchMap[$oldBranchId] = $newId;
                $inserted++;
            }

            if ($processed % 1000 === 0) {
                $this->info("Branches processed: {$processed}");
            }
        }

        fclose($handle);

        $this->info("Branches inserted: {$inserted}");
        $this->info("Branches reused: {$reused}");
        $this->info("Branches skipped: {$skipped}");

        // -----------------------------
        // 3. IMPORT IFSC
        // -----------------------------
        $this->info("Importing IFSC...");

        $handle = fopen($ifscCsv, 'r');
        $header = fgetcsv($handle);

        $inserted = 0;
        $skipped  = 0;
        $processed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $processed++;

            if (count($row) !== count($header)) {
                $skipped++;
                continue;
            }

            $data = array_combine($header, $row);

            $oldBankId   = (int) ($data['mas_ifsc_bank_id'] ?? 0);
            $oldBranchId = (int) ($data['mas_ifsc_branch_id'] ?? 0);

            if (!isset($bankMap[$oldBankId], $branchMap[$oldBranchId])) {
                $skipped++;
                continue;
            }

            DB::table('mas_ifsc')->insert([
                'mas_ifsc_number'    => trim($data['mas_ifsc_number']),
                'mas_ifsc_bank_id'    => $bankMap[$oldBankId],
                'mas_ifsc_branch_id'  => $branchMap[$oldBranchId],
                'mas_ifsc_code_micr'  => $data['mas_ifsc_code_micr'] ?? null,
                'mas_ifsc_status_id'  => $data['mas_ifsc_status_id'] ?? 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            $inserted++;

            if ($processed % 2000 === 0) {
                $this->info("IFSC processed: {$processed}");
            }
        }

        fclose($handle);

        $this->info("IFSC inserted: {$inserted}");
        $this->info("IFSC skipped: {$skipped}");

        // -----------------------------
        // 4. FIX AUTO_INCREMENT
        // -----------------------------
        foreach (['mas_bank', 'mas_bank_branch', 'mas_ifsc'] as $table) {
            $max = DB::table($table)->max('id') ?? 0;
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = " . ($max + 1));
        }

        // -----------------------------
        // 5. INTEGRITY CHECK
        // -----------------------------
        $broken1 = DB::selectOne("
            SELECT COUNT(*) c
            FROM mas_bank_branch br
            LEFT JOIN mas_bank b ON br.mas_bank_branch_bank_id = b.id
            WHERE b.id IS NULL
        ")->c;

        $broken2 = DB::selectOne("
            SELECT COUNT(*) c
            FROM mas_ifsc i
            LEFT JOIN mas_bank b ON i.mas_ifsc_bank_id = b.id
            WHERE b.id IS NULL
        ")->c;

        $broken3 = DB::selectOne("
            SELECT COUNT(*) c
            FROM mas_ifsc i
            LEFT JOIN mas_bank_branch br ON i.mas_ifsc_branch_id = br.id
            WHERE br.id IS NULL
        ")->c;

        if ($broken1 + $broken2 + $broken3 > 0) {
            $this->error("Integrity failed!");
            $this->error("Broken branches→bank: {$broken1}");
            $this->error("Broken ifsc→bank: {$broken2}");
            $this->error("Broken ifsc→branch: {$broken3}");
            return Command::FAILURE;
        }

        $this->info("All relationships valid ✅");
        $this->info("Sync completed successfully 🎯");

        return Command::SUCCESS;
    }
}
