<?php

namespace App\Services\ExaminerAllocation\Pickers;

use App\Services\ExaminerAllocation\Domain\AllocationState;
use Illuminate\Support\Facades\DB;

class DatabaseExaminerPicker implements ExaminerPicker
{
    public function __construct(
        protected AllocationState $state
    ) {}

    public function pick(
        string $slotType,
        int $departmentId,
        int $centreId,
        array $attachedCentreIds,
        array $excludeExaminerIds,
        string $date
    ): ?array {

        $reuseIds = $this->state->reusePool($slotType, $date);

        if (!empty($reuseIds)) {
            $picked = $this->fetchFromIds($reuseIds, $excludeExaminerIds, $departmentId);
            if ($picked) {
                return $picked;
            }
        }

        return $this->pickFresh(
            slotType: $slotType,
            departmentId: $departmentId,
            centreId: $centreId,
            attachedCentreIds: $attachedCentreIds,
            exclude: $excludeExaminerIds
        );
    }

    protected function fetchFromIds(array $ids, array $exclude, int $departmentId): ?array
    {
        if (!empty($ids)) {
            $exclude = array_diff($exclude, $ids);
        }

        $ids = array_values(array_diff($ids, $exclude));
        if (empty($ids)) return null;

        $row = DB::table('examiner_details as ed')
            ->join('basic_details as bd', 'ed.examiner_details_basic_details_id', '=', 'bd.id')
            ->join('users as usr', 'bd.basic_details_user_id', '=', 'usr.id')

            // 👇 canonical-aware joins for users
            ->leftJoin('mas_college as uc', 'uc.id', '=', 'usr.user_college_id')
            ->leftJoin('mas_college as ucc', 'ucc.id', '=', 'uc.mas_college_canonical_college_id')

            ->leftJoin('mas_status as ms', 'ed.examiner_details_status_id', '=', 'ms.id')
            ->select(
                'ed.id',
                'usr.name as name',
                'usr.mobile as mobile',
                'ms.mas_status_name as status',
                'ms.mas_status_label_colour as colour'
            )
            ->whereIn('ed.id', $ids)

            // HARD GUARANTEE: never cross department
            ->where('bd.basic_details_department_id', $departmentId)

            ->orderBy('ed.examiner_details_rank', 'asc')
            ->first();

        return $row ? (array)$row : null;
    }

    protected function pickFresh(
        string $slotType,
        int $departmentId,
        int $centreId,
        array $attachedCentreIds,
        array $exclude
    ): ?array {

        $base = DB::table('examiner_details as ed')
            ->join('basic_details as bd', 'ed.examiner_details_basic_details_id', '=', 'bd.id')
            ->join('users as usr', 'bd.basic_details_user_id', '=', 'usr.id')

            ->leftJoin('mas_college as uc', 'uc.id', '=', 'usr.user_college_id')
            ->leftJoin('mas_college as ucc', 'ucc.id', '=', 'uc.mas_college_canonical_college_id')

            ->leftJoin('mas_status as ms', 'ed.examiner_details_status_id', '=', 'ms.id')
            ->select(
                'ed.id',
                'usr.name as name',
                'usr.mobile as mobile',
                'ms.mas_status_name as status',
                'ms.mas_status_label_colour as colour'
            )
            ->whereNotIn('ed.id', $exclude);

        // Department guard for all internal type logic
        if (str_contains($slotType, 'Internal') || $slotType === 'External-O') {
            $base->where('bd.basic_details_department_id', $departmentId);
        }

        $effectiveCollege = DB::raw('COALESCE(ucc.id, uc.id)');

        // Normalise attached list
        // Remove centre from attached if present (critical fix)
        $attached = array_values(array_diff(
            array_filter($attachedCentreIds),
            [$centreId]
        ));

        $blocked = array_unique(array_merge([$centreId], $attached));

        // ------------------------------------------------
        // INTERNAL-C → centre only, type = 1
        // ------------------------------------------------
        if ($slotType === 'Internal-C') {
            $row = (clone $base)
                ->where('ed.examiner_details_type', 1)
                ->where($effectiveCollege, '=', $centreId)
                ->orderBy('ed.examiner_details_rank')
                ->first();

            return $row ? (array)$row : null;
        }

        // ------------------------------------------------
        // INTERNAL-A → attached first, fallback centre
        // ------------------------------------------------
        if ($slotType === 'Internal-A') {

            // 1️⃣ Attached first
            if (!empty($attached)) {
                $row = (clone $base)
                    ->where('ed.examiner_details_type', 1)
                    ->whereIn($effectiveCollege, $attached)
                    ->orderBy('ed.examiner_details_rank')
                    ->first();

                if ($row) return (array)$row;
            }

            // 2️⃣ Fallback to centre
            $row = (clone $base)
                ->where('ed.examiner_details_type', 1)
                ->where($effectiveCollege, '=', $centreId)
                ->orderBy('ed.examiner_details_rank')
                ->first();

            return $row ? (array)$row : null;
        }

        // ------------------------------------------------
        // EXTERNAL → real external examiners only (type = 2)
        // ------------------------------------------------
        if ($slotType === 'External') {
            $row = (clone $base)
                ->where('ed.examiner_details_type', 2)
                ->orderBy('ed.examiner_details_rank')
                ->first();

            return $row ? (array)$row : null;
        }

        // ------------------------------------------------
        // EXTERNAL-O → internal examiners NOT from centre/attached
        // ------------------------------------------------
        if ($slotType === 'External-O') {
            $row = (clone $base)
                ->where('ed.examiner_details_type', 1)       // INTERNAL only
                ->whereNotIn($effectiveCollege, $blocked)    // NOT centre, NOT attached
                ->orderBy('ed.examiner_details_rank')
                ->first();

            return $row ? (array)$row : null;
        }

        return null;
    }
}
