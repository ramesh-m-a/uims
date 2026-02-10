<?php

namespace App\Services\ExaminerAllocation\Domain;

class AllocationState
{
    /**
     * Structure:
     * [
     *   'batch|subject|centre' => [
     *       '2026-02-02' => [
     *           'Internal-C' => [12],
     *           'Internal-A' => [18],
     *           'External'   => [45],
     *           'External-O' => [51],
     *       ]
     *   ]
     * ]
     */
    protected array $history = [];

    /**
     * Examiner cache:
     * [
     *   12 => ['name' => 'GEETHA...', 'mobile' => '99xxxx'],
     * ]
     */
    protected array $examinerMeta = [];

    protected int $maxReuseDays = 3;

    public function record(
        string $seriesKey,
        string $date,
        string $slotType,
        int $examinerId,
        ?string $name = null,
        ?string $mobile = null,
        ?string $status = null,
        ?string $colour = null,
    ): void {
        $this->history[$seriesKey][$date][$slotType][] = $examinerId;

      /*  logger()->debug('STATE MUTATED', [
            'series' => $seriesKey,
            'date' => $date,
            'slot' => $slotType,
            'history' => $this->history,
            'object' => spl_object_id($this)
        ]);*/

        if ($name || $mobile) {
            $this->examinerMeta[$examinerId] = [
                'name'   => $name ?? '',
                'mobile' => $mobile ?? '',
                'status' => $status ?? 'Assigned',
                'colour' => $colour ?? 'bg-success',
            ];
        }
    }

    public function seriesKey(int $batchId, int $subjectId, int $centreId): string
    {
        // return $batchId . '|' . $subjectId . '|' . $centreId;
        return $subjectId . '|' . $centreId;
    }

    public function usedOnDate(string $date, int $examinerId): bool
    {
        foreach ($this->history as $series) {
            if (!isset($series[$date])) continue;

            foreach ($series[$date] as $ids) {
                if (in_array($examinerId, $ids, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function lastAssigned(string $seriesKey, string $slotType): ?int
    {
        if (!isset($this->history[$seriesKey])) {
            return null;
        }

        $dates = array_keys($this->history[$seriesKey]);
        rsort($dates);

        foreach ($dates as $date) {
            $ids = $this->history[$seriesKey][$date][$slotType] ?? [];
            if (!empty($ids)) {
                return end($ids);
            }
        }

        return null;
    }

    public function getExaminer(int $id): array
    {
        return $this->examinerMeta[$id]
            ?? ['name' => '', 'mobile' => '', 'status' => 'Assigned', 'colour' => 'bg-success'];
    }

    /**
     * ✅ RESTORED for DatabaseExaminerPicker compatibility
     * Returns examiner IDs used on this date for this slot type.
     */
    public function reusePool(string $slotType, string $date): array
    {
        $ids = [];

        foreach ($this->history as $series) {
            foreach ($series as $d => $types) {

                // only consider earlier dates
                if ($d >= $date) continue;

                if (!isset($types[$slotType])) continue;

                foreach ($types[$slotType] as $id) {
                    $ids[(int)$id] = true;
                }
            }
        }

        return array_values(array_keys($ids));
    }

    public function reuseCandidates(
        string $seriesKey,
        string $fromDate,
        int $batchId = 0,
        ?int $centreId = null,
        array $attachedIds = []
    ): array {
        $candIC = [];
        $candIA = [];
        $candE  = [];
        $candEO = [];

        if (empty($this->history)) {
            return [[], [], [], []];
        }

        [$curBatchId, $curSubjectId, $curCentreId] = array_pad(
            array_map('intval', explode('|', $seriesKey)),
            3,
            0
        );

        $attachedSimple = array_values(array_filter(array_map('intval', $attachedIds)));
        $onlyCentreAttached = (
            $centreId !== null &&
            count($attachedSimple) === 1 &&
            (int)$attachedSimple[0] === (int)$centreId
        );

        $addTypes = function(array $types, bool $treatCentreAsAttached = false)
        use (&$candIC, &$candIA, &$candE, &$candEO) {

            foreach (($types['Internal-C'] ?? []) as $id) $candIC[(int)$id] = true;
            foreach (($types['Internal-A'] ?? []) as $id) $candIA[(int)$id] = true;

            if ($treatCentreAsAttached) {
                foreach (($types['Internal-C'] ?? []) as $id) {
                    $candIA[(int)$id] = true;
                }
            }

            foreach (($types['External'] ?? []) as $id)   $candE[(int)$id]  = true;
            foreach (($types['External-O'] ?? []) as $id) $candEO[(int)$id] = true;
        };

        if (isset($this->history[$seriesKey])) {
            foreach ($this->history[$seriesKey] as $d => $types) {
                if ($d >= $fromDate) continue;

                $daysDiff = (int) floor((strtotime($fromDate) - strtotime($d)) / 86400);
                if ($daysDiff >= $this->maxReuseDays) continue;

                $addTypes($types, $onlyCentreAttached);
            }
        }

        return [
            array_values(array_keys($candIC)),
            array_values(array_keys($candIA)),
            array_values(array_keys($candE)),
            array_values(array_keys($candEO)),
        ];
    }

    public function debugHistory(): array
    {
        return $this->history;
    }

    public function hasSeries(string $seriesKey): bool
    {
        return isset($this->history[$seriesKey]);
    }

    public function getSeriesDates(string $seriesKey): array
    {
        return array_keys($this->history[$seriesKey] ?? []);
    }

    public function getSlotAssignments(string $seriesKey, string $date, string $slotType): array
    {
        return $this->history[$seriesKey][$date][$slotType] ?? [];
    }

    public function daysUsed(string $seriesKey, int $examinerId): int
    {
        if (!isset($this->history[$seriesKey])) {
            return 0;
        }

        $count = 0;

        foreach ($this->history[$seriesKey] as $date => $slots) {
            foreach ($slots as $slotType => $ids) {
                if (in_array($examinerId, $ids, true)) {
                    $count++;
                    break; // count only once per date
                }
            }
        }

        return $count;
    }


}
