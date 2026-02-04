<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class BatchRepository
{
    /**
     * Returns normalized batch-range rows for allocation engine.
     * This version matches YOUR real mas_batch_range schema.
     */
    public function rangesForScope(
        int $yearId,
        int $monthId,
        int $schemeId,
        int $degreeId,
        int $streamId
    ): array {
        $rows = DB::table('mas_batch_range as br')
            ->join('mas_batch as b', 'b.id', '=', 'br.mas_batch_range_batch_id')
            ->join('mas_subject as s', 's.id', '=', 'b.mas_batch_subject_id')
            ->leftJoin('mas_college as c', 'c.id', '=', 'br.mas_batch_range_centre_id')
            ->leftJoin('mas_college as cc', 'cc.id', '=', 'c.mas_college_canonical_college_id')
            ->where('b.mas_batch_year_id', $yearId)
           ->where('b.mas_batch_month_id', $monthId)
          ->where('b.mas_batch_revised_scheme_id', $schemeId)
           ->where('b.mas_batch_degree_id', $degreeId)
            ->where('b.mas_batch_stream_id', $streamId)
            ->select([
                'br.id as id',
                'br.mas_batch_range_batch_id as batch_id',
                'br.mas_batch_range_from_date as from_date',
                'br.mas_batch_range_to_date as to_date',

                DB::raw('COALESCE(cc.id, c.id) as centre_id'),

                'b.mas_batch_subject_id as subject_id',
                's.mas_subject_department_id as department_id',

                'b.mas_batch_revised_scheme_id as scheme_id',

                'br.mas_batch_range_batch_name as batch_name',
                's.mas_subject_name as subject_name',
                DB::raw('COALESCE(cc.mas_college_name, c.mas_college_name) as centre_name'),
            ])
            ->orderBy('b.id')
            ->orderBy('br.mas_batch_range_from_date')
            ->get();

     //   dd('BatchRepository', $rows);
        return $rows->map(function ($r) {

            // ✅ fetch attached centres from new normalized table
            $attached = DB::table('mas_batch_centre')
                ->where('mas_batch_id', $r->batch_id)
                ->where('is_attached', 1)
                ->pluck('mas_centre_id')
                ->map(fn($v) => (int) $v)
                ->values()
                ->all();

            // ✅ fallback: if none attached, allow centre itself
            if (empty($attached)) {
                $attached = [(int) ($r->centre_id ?? 0)];
            }

        //  dump($r->batch_id, $attached);

            return (object)[
                'id'            => (int) $r->id,
                'batch_id'      => (int) $r->batch_id,
                'from_date'     => $r->from_date,
                'to_date'       => $r->to_date,

                'centre_id'     => (int) ($r->centre_id ?? 0),
                'attached_ids'  => $attached,   // 🔥 REAL DATA NOW

                'subject_id'    => (int) $r->subject_id,
                'department_id' => (int) $r->department_id,

                'scheme_id'     => (int) $r->scheme_id,

                'batch_name'    => $r->batch_name ?? '',
                'subject_name'  => $r->subject_name ?? '',
                'centre_name'   => $r->centre_name ?? '',
            ];
        })->all();

    }
}
