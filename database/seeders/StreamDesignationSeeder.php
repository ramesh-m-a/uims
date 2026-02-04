<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StreamDesignationSeeder extends Seeder
{
    public function run(): void
    {
        $streams = DB::table('mas_stream')
            ->whereNull('deleted_at')
            ->where('mas_stream_status_id', 1)
            ->pluck('id');

        $designations = DB::table('mas_designation')
            ->whereNull('deleted_at')
            ->where('mas_designation_status_id', 1)
            ->pluck('id');

        if ($streams->isEmpty() || $designations->isEmpty()) {
            $this->command->warn('No streams or designations found. Skipping pivot seed.');
            return;
        }

        $now = now();

        $rows = [];

        foreach ($streams as $streamId) {
            foreach ($designations as $designationId) {

                $rows[] = [
                    'stream_id'      => $streamId,
                    'designation_id' => $designationId,
                    'status_id'      => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
        }

        DB::table('mas_stream_designation')->upsert(
            $rows,
            ['stream_id', 'designation_id'], // unique key
            ['status_id', 'updated_at']
        );

        $this->command->info('Stream-Designation pivot seeded successfully.');
    }
}
