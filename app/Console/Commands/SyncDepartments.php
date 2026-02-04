<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncDepartments extends Command
{
    protected $signature = 'sync:departments';
    protected $description = 'Remap department IDs across sandbox using prod CSV (basic_details + mas_subject)';

    public function handle()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $this->info("Starting stream-aware department remap...");

        $prodCsv = base_path('database/seeders/mas_department_prod.csv');

        if (!file_exists($prodCsv)) {
            $this->error("Missing file: {$prodCsv}");
            return Command::FAILURE;
        }

        // --------------------------------------
        // Error log setup
        // --------------------------------------
        $logPath = base_path('database/seeders/department_sync_errors.csv');
        $logDir = dirname($logPath);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logHandle = fopen($logPath, 'w');
        if (!$logHandle) {
            $this->error("Cannot create error log file: {$logPath}");
            return Command::FAILURE;
        }

        fputcsv($logHandle, [
            'type',
            'message',
            'prod_department_id',
            'mas_department_name',
            'stream_id',
            'entity',
            'entity_id',
            'old_department_id'
        ]);

        // --------------------------------------
        // 1. Load sandbox departments
        // --------------------------------------
        $this->info("Loading sandbox departments...");

        $sandbox = [];

        DB::table('mas_department')
            ->select('id', 'mas_department_name', 'mas_department_stream_id')
            ->orderBy('id')
            ->chunk(1000, function ($rows) use (&$sandbox) {
                foreach ($rows as $row) {
                    if (!$row->mas_department_name || !$row->mas_department_stream_id) continue;
                    $sandbox[$this->makeKey($row->mas_department_name, $row->mas_department_stream_id)] = $row->id;
                }
            });

        $this->info("Sandbox departments loaded: " . count($sandbox));

        // --------------------------------------
        // 2. Build prod → sandbox mapping
        // --------------------------------------
        $this->info("Building prod → sandbox mapping...");

        $map = [];

        $handle = fopen($prodCsv, 'r');
        $rawHeader = fgetcsv($handle);
        $header = array_map(fn($h) => preg_replace('/[^a-z0-9_]/', '_', strtolower(trim($h))), $rawHeader);

        $idKey = 'id';
        $nameKey = 'mas_department_name';
        $streamKey = 'mas_department_stream_id';

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) continue;
            $data = array_combine($header, $row);

            $oldId  = (int)($data[$idKey] ?? 0);
            $name   = trim($data[$nameKey] ?? '');
            $stream = (int)($data[$streamKey] ?? 0);

            if (!$oldId || !$name || !$stream) continue;

            $key = $this->makeKey($name, $stream);

            if (isset($sandbox[$key])) {
                $map[$oldId] = $sandbox[$key];
            } else {
                fputcsv($logHandle, ['unmatched', 'No sandbox match found', $oldId, $name, $stream, '', '', '']);
            }
        }

        fclose($handle);

        $this->info("Mappings built: " . count($map));

        // --------------------------------------
        // 3A. Update mas_subject (safe logic)
        // --------------------------------------
        $this->info("Updating mas_subject...");

        $subjectUpdated = 0;
        $subjectAlreadyCorrect = 0;
        $subjectSkipped = 0;

        DB::table('mas_subject')
            ->select('id', 'mas_subject_department_id')
            ->orderBy('id')
            ->chunkById(1000, function ($rows) use (&$map, &$subjectUpdated, &$subjectAlreadyCorrect, &$subjectSkipped, $logHandle) {
                foreach ($rows as $row) {
                    $current = $row->mas_subject_department_id;

                    if (!$current) {
                        $subjectSkipped++;
                        continue;
                    }

                    if (isset($map[$current])) {
                        $target = $map[$current];

                        if ((int)$current === (int)$target) {
                            $subjectAlreadyCorrect++;
                            continue;
                        }

                        DB::table('mas_subject')
                            ->where('id', $row->id)
                            ->update(['mas_subject_department_id' => $target]);

                        $subjectUpdated++;
                        continue;
                    }

                    fputcsv($logHandle, [
                        'subject_unmapped',
                        'No mapping for subject department',
                        '',
                        '',
                        '',
                        'mas_subject',
                        $row->id,
                        $current
                    ]);

                    $subjectSkipped++;
                }
            });

        $this->info("mas_subject updated: {$subjectUpdated}");
        $this->info("mas_subject already correct: {$subjectAlreadyCorrect}");
        $this->info("mas_subject skipped/unmapped: {$subjectSkipped}");

        // --------------------------------------
        // 3B. Update basic_details (safe logic)
        // --------------------------------------
        $this->info("Updating basic_details...");

        $updated = 0;
        $alreadyCorrect = 0;
        $skipped = 0;

        DB::table('basic_details')
            ->select('id', 'basic_details_department_id')
            ->orderBy('id')
            ->chunkById(1000, function ($rows) use (&$map, &$updated, &$alreadyCorrect, &$skipped, $logHandle) {
                foreach ($rows as $row) {
                    $current = $row->basic_details_department_id;

                    if (!$current) {
                        $skipped++;
                        continue;
                    }

                    if (isset($map[$current])) {
                        $target = $map[$current];

                        if ((int)$current === (int)$target) {
                            $alreadyCorrect++;
                            continue;
                        }

                        DB::table('basic_details')
                            ->where('id', $row->id)
                            ->update(['basic_details_department_id' => $target]);

                        $updated++;
                        continue;
                    }

                    fputcsv($logHandle, [
                        'basic_unmapped',
                        'No mapping for basic_details department',
                        '',
                        '',
                        '',
                        'basic_details',
                        $row->id,
                        $current
                    ]);

                    $skipped++;
                }
            });

        $this->info("basic_details updated: {$updated}");
        $this->info("basic_details already correct: {$alreadyCorrect}");
        $this->info("basic_details skipped/unmapped: {$skipped}");

        fclose($logHandle);

        $this->info("Sync completed. Check log:");
        $this->info($logPath);

        return Command::SUCCESS;
    }

    private function makeKey(string $name, int $streamId): string
    {
        return $this->normalize($name) . '|' . $streamId;
    }

    private function normalize(string $value): string
    {
        $v = strtoupper(trim($value));
        $v = preg_replace('/\s+/', ' ', $v);
        $v = preg_replace('/[^A-Z0-9 ]/', '', $v);
        return $v;
    }
}
