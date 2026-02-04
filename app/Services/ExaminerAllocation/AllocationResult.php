<?php

namespace App\Services\ExaminerAllocation;

use Illuminate\Support\Collection;

final class AllocationResult
{
    public function __construct(
        public readonly Collection $rows,
        public readonly array $statuses = [],
        public readonly array $shortfalls = [],
    ) {}

    public static function empty(): self
    {
        return new self(collect(), [], []);
    }
}
