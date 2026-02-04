<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RevisedSchemeStreamSeeder extends Seeder
{
    public function run(): void
    {
        $schemes = DB::table('mas_revised_scheme')
            ->whereNull('deleted_at')
            ->where('mas_revised_scheme_status_id', 1)
            ->pluck('id');

        $streams = DB::table('mas_stream')
            ->whereNull('deleted_at')
            ->where('mas_stream_status_id', 1)
            ->pluck('id');

        if ($schemes->isEmpty() || $streams->isEmpty()) {
            echo "No scheme/stream data found.\n";
            return;
        }

        $now = now();
        $rows = [];

        foreach ($schemes as $schemeId) {
            foreach ($streams as $streamId) {
                $rows[] = [
                    'mas_revised_scheme_id' => $schemeId,
                    'mas_stream_id' => $streamId,
                    'mas_revised_scheme_stream_status_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('mas_revised_scheme_stream')->upsert(
            $rows,
            ['mas_revised_scheme_id','mas_stream_id','deleted_at'],
            ['mas_revised_scheme_stream_status_id','updated_at']
        );

        echo "RevisedScheme ↔ Stream pivot seeded\n";
    }
}
