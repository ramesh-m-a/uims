<?php

namespace App\Support\Examiner;

use Illuminate\Support\Collection;

trait ExaminerUniversalSort
{
    /**
     * Universal Examiner Type Order
     */
    protected array $examinerTypeOrder = [
        'Internal-C' => 1,
        'Internal-A' => 2,
        'External-O' => 3,
        'External'   => 4,
    ];

    /**
     * Public Entry — Sort Any Examiner Rows Collection
     */
    public function sortExaminerRows(Collection $rows): Collection
    {
        if ($rows->isEmpty()) {
            return $rows;
        }

        return $rows
            ->map(fn($row) => $this->normalizeRow($row))
            ->sort(function ($a, $b) {
                return $this->compareRows($a, $b);
            })
            ->values();
    }

    /**
     * Normalize row (Object OR Array safe)
     */
    protected function normalizeRow($row): array
    {
        if (is_array($row)) {
            return $row;
        }

        return (array) $row;
    }

    /**
     * Master Comparison Logic
     */
    protected function compareRows(array $a, array $b): int
    {
        return
            $this->cmp($a['centre_name'] ?? '', $b['centre_name'] ?? '') ?:
                $this->cmp($a['batch_name'] ?? '', $b['batch_name'] ?? '') ?:
                    $this->cmpDate($a['from_date'] ?? null, $b['from_date'] ?? null) ?:
                        $this->cmpExaminerType(
                            $a['examiner_type'] ?? '',
                            $b['examiner_type'] ?? ''
                        ) ?:
                            $this->cmp($a['examiner_name'] ?? '', $b['examiner_name'] ?? '');
    }

    /**
     * Safe string compare
     */
    protected function cmp($a, $b): int
    {
        return strcmp(
            (string) $a,
            (string) $b
        );
    }

    /**
     * Date Compare (Null Safe)
     */
    protected function cmpDate($a, $b): int
    {
        if (!$a && !$b) return 0;
        if (!$a) return -1;
        if (!$b) return 1;

        return strcmp($a, $b);
    }

    /**
     * Examiner Type Compare
     */
    protected function cmpExaminerType(string $a, string $b): int
    {
        $orderA = $this->examinerTypeOrder[$a] ?? 999;
        $orderB = $this->examinerTypeOrder[$b] ?? 999;

        return $orderA <=> $orderB;
    }
}
