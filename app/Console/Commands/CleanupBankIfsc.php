<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\UserProfileDraft;

class CleanupBankIfsc extends Command
{
    protected $signature = 'profile:cleanup-ifsc {--dry-run}';
    protected $description = 'One-time cleanup: normalize bank data using IFSC only';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Starting IFSC cleanup...');
        if ($dryRun) {
            $this->warn('Running in DRY RUN mode (no DB updates)');
        }

        $total = 0;
        $fixed = 0;
        $invalid = 0;

        UserProfileDraft::chunk(200, function ($drafts) use (&$total, &$fixed, &$invalid, $dryRun) {

            foreach ($drafts as $draft) {
                $total++;

                $data = $draft->data ?? [];
                $ifsc = data_get($data, 'bank.account.ifsc_code')
                    ?? data_get($data, 'bank.ifsc_code');

                if (! $ifsc) {
                    continue;
                }

                // Check IFSC exists in master
                $row = DB::table('mas_bank_details')
                    ->where('mas_ifsccode_number', $ifsc)
                    ->first();

                if (! $row) {
                    $invalid++;
                    continue;
                }

                // Normalize structure
                $data['bank']['account']['ifsc_code']   = $ifsc;
                $data['bank']['account']['bank_name']   = strtoupper($row->BANK ?? '');
                $data['bank']['account']['branch_name'] = strtoupper($row->mas_bank_details_branch_name ?? '');

                // Remove unsafe legacy IDs
                unset($data['bank']['bank_id']);
                unset($data['bank']['branch_id']);
                unset($data['bank']['account']['bank_id']);
                unset($data['bank']['account']['branch_id']);

                if (! $dryRun) {
                    $draft->update(['data' => $data]);
                }

                $fixed++;
            }
        });

        $this->info("Total drafts scanned: $total");
        $this->info("Fixed drafts: $fixed");
        $this->warn("Invalid IFSC found: $invalid");

        $this->info('Cleanup complete.');
    }
}
