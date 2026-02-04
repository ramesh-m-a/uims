<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ResequenceBanks extends Command
{
    protected $signature = 'db:resequence-banks {--start=136 : Last correct ID, next will start from +1}';
    protected $description = 'Resequence mas_bank IDs and fix all dependent foreign keys safely';

    public function handleold()
    {
        $startFrom = (int) $this->option('start');

        $this->info("Starting resequence after ID: {$startFrom}");

        try {
            DB::beginTransaction();

            $this->info('Disabling FK checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // ----------------------------
            // Backup tables
            // ----------------------------
            $this->info('Creating backups...');
            DB::statement('CREATE TABLE IF NOT EXISTS mas_bank_bak AS SELECT * FROM mas_bank');
            DB::statement('CREATE TABLE IF NOT EXISTS mas_bank_branch_bak AS SELECT * FROM mas_bank_branch');
            DB::statement('CREATE TABLE IF NOT EXISTS mas_ifsc_bak AS SELECT * FROM mas_ifsc');

            // ----------------------------
            // Build mapping
            // ----------------------------
            $this->info('Building ID mapping...');
            DB::statement('DROP TEMPORARY TABLE IF EXISTS bank_id_map');

            DB::statement("
                SET @new_id := {$startFrom};
            ");

            DB::statement("
                CREATE TEMPORARY TABLE bank_id_map AS
                SELECT
                    id AS old_id,
                    (@new_id := @new_id + 1) AS new_id
                FROM mas_bank
                WHERE id > {$startFrom}
                ORDER BY id
            ");

            $count = DB::table('bank_id_map')->count();
            $this->info("Mapped {$count} bank IDs");

            // ----------------------------
            // Update child tables first
            // ----------------------------
            $this->info('Updating mas_bank_branch...');
            DB::statement("
                UPDATE mas_bank_branch b
                JOIN bank_id_map m
                  ON b.mas_bank_branch_bank_id = m.old_id
                SET b.mas_bank_branch_bank_id = m.new_id
            ");

            $this->info('Updating mas_ifsc bank FK...');
            DB::statement("
                UPDATE mas_ifsc i
                JOIN bank_id_map m
                  ON i.mas_ifsc_bank_id = m.old_id
                SET i.mas_ifsc_bank_id = m.new_id
            ");

            // ----------------------------
            // Update parent
            // ----------------------------
            $this->info('Updating mas_bank IDs...');
            DB::statement("
                UPDATE mas_bank b
                JOIN bank_id_map m
                  ON b.id = m.old_id
                SET b.id = m.new_id
            ");

            // ----------------------------
            // Fix AUTO_INCREMENT
            // ----------------------------
            $max = DB::table('mas_bank')->max('id');
            $next = $max + 1;

            $this->info("Fixing AUTO_INCREMENT to {$next}");
            DB::statement("ALTER TABLE mas_bank AUTO_INCREMENT = {$next}");

            // ----------------------------
            // Re-enable FK
            // ----------------------------
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            DB::commit();

            $this->info('DONE. All IDs resequenced safely.');
            return Command::SUCCESS;
        }
        catch (Throwable $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            $this->error('FAILED. Everything rolled back.');
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }

    public function handle()
    {
        $startFrom = (int) $this->option('start');

        $this->info("Starting resequence after ID: {$startFrom}");

        try {

            // ---------------------------------
            // BACKUPS (outside transaction)
            // ---------------------------------
            $this->info('Creating backups...');
            DB::statement('CREATE TABLE IF NOT EXISTS mas_bank_bak AS SELECT * FROM mas_bank');
            DB::statement('CREATE TABLE IF NOT EXISTS mas_bank_branch_bak AS SELECT * FROM mas_bank_branch');
            DB::statement('CREATE TABLE IF NOT EXISTS mas_ifsc_bak AS SELECT * FROM mas_ifsc');

            // ---------------------------------
            // TRANSACTION START (DML only)
            // ---------------------------------
            DB::beginTransaction();

            $this->info('Disabling FK checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // ----------------------------
            // Build mapping
            // ----------------------------
            $this->info('Building ID mapping...');
            DB::statement('DROP TEMPORARY TABLE IF EXISTS bank_id_map');
            DB::statement("SET @new_id := {$startFrom}");

            DB::statement("
            CREATE TEMPORARY TABLE bank_id_map AS
            SELECT
                id AS old_id,
                (@new_id := @new_id + 1) AS new_id
            FROM mas_bank
            WHERE id > {$startFrom}
            ORDER BY id
        ");

            $count = DB::table('bank_id_map')->count();
            $this->info("Mapped {$count} bank IDs");

            // ----------------------------
            // Update child tables
            // ----------------------------
            $this->info('Updating mas_bank_branch...');
            DB::statement("
            UPDATE mas_bank_branch b
            JOIN bank_id_map m
              ON b.mas_bank_branch_bank_id = m.old_id
            SET b.mas_bank_branch_bank_id = m.new_id
        ");

            $this->info('Updating mas_ifsc bank FK...');
            DB::statement("
            UPDATE mas_ifsc i
            JOIN bank_id_map m
              ON i.mas_ifsc_bank_id = m.old_id
            SET i.mas_ifsc_bank_id = m.new_id
        ");

            // ----------------------------
            // Update parent
            // ----------------------------
            $this->info('Updating mas_bank IDs...');
            DB::statement("
            UPDATE mas_bank b
            JOIN bank_id_map m
              ON b.id = m.old_id
            SET b.id = m.new_id
        ");

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            DB::commit();

            // ---------------------------------
            // AUTO_INCREMENT (outside txn)
            // ---------------------------------
            $max = DB::table('mas_bank')->max('id');
            $next = $max + 1;

            $this->info("Fixing AUTO_INCREMENT to {$next}");
            DB::statement("ALTER TABLE mas_bank AUTO_INCREMENT = {$next}");

            $this->info('DONE. All IDs resequenced safely.');
            return Command::SUCCESS;

        } catch (Throwable $e) {

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            $this->error('FAILED.');
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }

}
