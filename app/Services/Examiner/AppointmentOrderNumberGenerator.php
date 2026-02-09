<?php

namespace App\Services\Examiner;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentOrderNumberGenerator
{

    public function generate()
    {
        return DB::transaction(function () {

            $now = Carbon::now();

            $year  = $now->year;
            $month = $now->month;

            // LOCK ROW FOR UPDATE
            $sequenceRow = DB::table('appointment_order_sequences')
                ->where('year', $year)
                ->where('month', $month)
                ->lockForUpdate()
                ->first();

            if (!$sequenceRow) {

                DB::table('appointment_order_sequences')->insert([
                    'year' => $year,
                    'month' => $month,
                    'current_sequence' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $sequence = 1;

            } else {

                $sequence = $sequenceRow->current_sequence + 1;

                DB::table('appointment_order_sequences')
                    ->where('id', $sequenceRow->id)
                    ->update([
                        'current_sequence' => $sequence,
                        'updated_at' => now(),
                    ]);
            }

            return $this->formatOrderNumber($year, $month, $sequence);
        });
    }


    private function formatOrderNumber($year, $month, $sequence)
    {
        return sprintf(
            'RGUHS/EXAM/APPT/%d/%02d/%06d',
            $year,
            $month,
            $sequence
        );
    }
}
