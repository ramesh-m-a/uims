<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUserColleges extends Command
{
    protected $signature = 'sync:users-colleges';
    protected $description = 'Fix users.user_college_id by mapping prod college IDs to sandbox college IDs using college_code and (name + stream)';

    public function handle()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $this->info("Starting users → college remap (stream-aware)...");

        $prodCsv = base_path('database/seeders/mas_college_prod.csv');

        if (!file_exists($prodCsv)) {
            $this->error("Missing file: {$prodCsv}");
            return Command::FAILURE;
        }

        // --------------------------------------
        // 1. Load sandbox colleges
        // --------------------------------------
        $this->info("Building sandbox college lookup...");

        $byCode = [];
        $byNameStream = [];

        DB::table('mas_college')
            ->select('id', 'mas_college_code', 'mas_college_name', 'mas_college_stream_id')
            ->orderBy('id')
            ->chunk(1000, function ($rows) use (&$byCode, &$byNameStream) {
                foreach ($rows as $row) {

                    if ($row->mas_college_code) {
                        $byCode[trim(strtoupper($row->mas_college_code))] = $row->id;
                    }

                    if ($row->mas_college_name && $row->mas_college_stream_id) {
                        $key = $this->normalize($row->mas_college_name)
                            . '|' . (int)$row->mas_college_stream_id;

                        $byNameStream[$key] = $row->id;
                    }
                }
            });

        $this->info("Sandbox colleges loaded:");
        $this->info(" - By code: " . count($byCode));
        $this->info(" - By name+stream: " . count($byNameStream));

        // --------------------------------------
        // 2. Build mapping from prod CSV
        // --------------------------------------
        $this->info("Building prod → sandbox mapping...");

        $handle = fopen($prodCsv, 'r');
        $rawHeader = fgetcsv($handle);

        $header = array_map(
            fn($h) => preg_replace('/[^a-z0-9_]/', '_', strtolower(trim($h))),
            $rawHeader
        );

        $map = [];
        $unmapped = [];
        $processed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) continue;

            $data = array_combine($header, $row);

            $oldId  = (int)($data['id'] ?? 0);
            if (!$oldId) continue;

            $code   = trim(strtoupper($data['mas_college_code'] ?? ''));
            $name   = trim($data['mas_college_name'] ?? '');
            $stream = (int)($data['mas_college_stream_id'] ?? 0);

            $newId = null;

            if ($code && isset($byCode[$code])) {
                $newId = $byCode[$code];
            }

            if (!$newId && $name && $stream) {
                $key = $this->normalize($name) . '|' . $stream;
                if (isset($byNameStream[$key])) {
                    $newId = $byNameStream[$key];
                }
            }

            if ($newId) {
                $map[$oldId] = $newId;
            } else {
                $unmapped[] = [
                    'id'     => $oldId,
                    'code'   => $code,
                    'name'   => $name,
                    'stream' => $stream
                ];
            }

            if (++$processed % 1000 === 0) {
                $this->info("Processed {$processed} prod rows...");
            }
        }

        fclose($handle);

        $this->info("Mappings built: " . count($map));
        $this->info("Unmapped colleges: " . count($unmapped));

        // --------------------------------------
        // 3. Update users (with skip-if-same logic)
        // --------------------------------------
        $this->info("Updating users.user_college_id...");

        $updated = 0;
        $alreadyCorrect = 0;
        $skipped = 0;

        DB::table('users')
            ->select('id', 'user_college_id')
            ->orderBy('id')
            ->chunkById(1000, function ($users) use (&$map, &$updated, &$alreadyCorrect, &$skipped) {
                foreach ($users as $u) {
                    $current = $u->user_college_id;

                    // No value → skip
                    if (!$current) {
                        $skipped++;
                        continue;
                    }

                    // If mapping exists
                    if (isset($map[$current])) {
                        $target = $map[$current];

                        // Already same as sandbox → skip and log
                        if ((int)$current === (int)$target) {
                            $alreadyCorrect++;
                            continue;
                        }

                        // Needs update
                        DB::table('users')
                            ->where('id', $u->id)
                            ->update(['user_college_id' => $target]);

                        $updated++;
                        continue;
                    }

                    // No mapping → skip
                    $skipped++;
                }
            });

        $this->info("Users updated: {$updated}");
        $this->info("Users already correct (skipped): {$alreadyCorrect}");
        $this->info("Users skipped (null / no mapping): {$skipped}");

        // --------------------------------------
        // 4. Export unmapped colleges
        // --------------------------------------
        if (!empty($unmapped)) {
            $path = base_path('database/seeders/unmapped_colleges.csv');
            $h = fopen($path, 'w');
            fputcsv($h, ['id', 'code', 'name', 'stream']);

            foreach ($unmapped as $row) {
                fputcsv($h, $row);
            }

            fclose($h);

            $this->warn("Unmapped colleges saved to: {$path}");
        }

        // --------------------------------------
        // 5. Integrity check
        // --------------------------------------
        $broken = DB::selectOne("
            SELECT COUNT(*) c
            FROM users u
            LEFT JOIN mas_college c ON u.user_college_id = c.id
            WHERE u.user_college_id IS NOT NULL
              AND c.id IS NULL
        ")->c;

        if ($broken > 0) {
            $this->error("Integrity failed: {$broken} users invalid");
            return Command::FAILURE;
        }

        $this->info("All users mapped correctly ✅");
        $this->info("Sync completed 🎯");

        return Command::SUCCESS;
    }

    private function normalize(?string $value): string
    {
        $v = strtoupper(trim($value ?? ''));
        $v = preg_replace('/\s+/', ' ', $v);
        $v = preg_replace('/[^A-Z0-9 ]/', '', $v);
        return $v;
    }
}
