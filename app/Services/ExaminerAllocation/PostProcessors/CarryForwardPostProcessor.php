<?php

namespace App\Services\ExaminerAllocation\PostProcessors;

use App\Services\ExaminerAllocation\DTO\AllocationRowDTO;
use Illuminate\Support\Collection;

class CarryForwardPostProcessor
{
    /**
     * @param Collection<int, AllocationRowDTO> $rows
     * @return Collection<int, AllocationRowDTO>
     */
    public function process(Collection $rows): Collection
    {
        if ($rows->isEmpty()) {
            return $rows;
        }

        // Group by logical exam series:
        // centre + subject + normalized batch
        $grouped = $rows->groupBy(function (AllocationRowDTO $r) {
            return implode('|', [
                $r->centreId,
                $r->subjectId,
                $this->normalizeBatch($r->batchName),
            ]);
        });

        $final = collect();

        foreach ($grouped as $seriesRows) {

            // Group by date
            $byDate = $seriesRows->groupBy(fn($r) => $r->fromDate)->sortKeys();

            $dates = $byDate->keys()->values();

            $realByDate = [];

            foreach ($dates as $i => $date) {
                $dayRows = $byDate[$date];

                // Detect real examiners for this day
                $realByDate[$date] = $dayRows
                    ->filter(fn($r) => $this->isReal($r))
                    ->keyBy(fn($r) => $r->examinerType);

                // First day → keep as-is
                if ($i === 0) {
                    $final = $final->concat($dayRows);
                    continue;
                }

                // Find nearest previous day with real examiners
                $sourceDate = null;
                for ($j = $i - 1; $j >= 0; $j--) {
                    if (!empty($realByDate[$dates[$j]])) {
                        $sourceDate = $dates[$j];
                        break;
                    }
                }

                if ($sourceDate === null) {
                    $final = $final->concat($dayRows);
                    continue;
                }

                $sourceRoles = $realByDate[$sourceDate];

                $current = $dayRows->keyBy(fn($r) => $r->examinerType);

                foreach ($sourceRoles as $type => $srcRow) {
                    if (isset($current[$type])) {
                        $target = $current[$type];

                        if ($this->isPlaceholder($target)) {
                            $this->copyExaminer($srcRow, $target, $date);
                        }
                    } else {
                        // Missing entirely → clone
                        $clone = clone $srcRow;
                        $clone->fromDate = $date;
                        $clone->toDate   = $date;
                        $clone->batchName = $this->adjustBatch($clone->batchName, $sourceDate, $date);

                        $current->push($clone);
                    }
                }

                $final = $final->concat($current->values());
            }
        }

        return $final->values();
    }

    private function isReal(AllocationRowDTO $r): bool
    {
        return $r->examinerId > 0
            && !str_contains(strtoupper($r->name), 'SHORTFALL ramesh')
            && !str_contains(strtoupper($r->name), 'ASSIGN ramesh')
            && !str_contains(strtoupper($r->name), 'TBD ramesh');
    }

    private function isPlaceholder(AllocationRowDTO $r): bool
    {
        return !$this->isReal($r);
    }

    private function copyExaminer(AllocationRowDTO $src, AllocationRowDTO $target, string $date): void
    {
        $target->examinerId = $src->examinerId;
        $target->name       = $src->name;
        $target->mobile     = $src->mobile;
        $target->status     = $src->status;
        $target->colour     = $src->colour;
        $target->fromDate   = $date;
        $target->toDate     = $date;
        $target->batchName  = $this->adjustBatch($src->batchName, $src->fromDate, $date);
    }

    private function normalizeBatch(string $name): string
    {
        return trim(preg_replace('/\s*-\s*DAY\s*\d+\s*$/i', '', $name));
    }

    private function adjustBatch(string $batch, string $sourceDate, string $targetDate): string
    {
        if (!preg_match('/(.*?)-\s*DAY\s*(\d+)/i', $batch, $m)) {
            return $batch;
        }

        $prefix = trim($m[1]);
        $day = (int)$m[2];

        $diff = (strtotime($targetDate) - strtotime($sourceDate)) / 86400;

        return $prefix . ' - DAY ' . ($day + (int)$diff);
    }
}
