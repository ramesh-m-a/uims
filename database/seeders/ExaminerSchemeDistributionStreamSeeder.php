<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExaminerSchemeDistributionStreamSeeder extends Seeder
{
    public function run(): void
    {
        $esds = DB::table('mas_examiner_scheme_distribution')
            ->whereNull('deleted_at')
            ->pluck('id');

        $streams = DB::table('mas_stream')
            ->whereNull('deleted_at')
            ->where('mas_stream_status_id', 1)
            ->pluck('id');

        if ($esds->isEmpty() || $streams->isEmpty()) {
            echo "No ESD or Stream data found.\n";
            return;
        }

        $now = now();
        $rows = [];

        foreach ($esds as $esdId) {
            foreach ($streams as $streamId) {

                $rows[] = [
                    'mas_examiner_scheme_distribution_id' => $esdId,
                    'mas_stream_id' => $streamId,
                    'status_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('mas_examiner_scheme_distribution_stream')->upsert(
            $rows,
            [
                'mas_examiner_scheme_distribution_id',
                'mas_stream_id',
                'deleted_at'
            ],
            ['status_id','updated_at']
        );

        echo "ExaminerSchemeDistribution ↔ Stream pivot seeded\n";
    }
}
