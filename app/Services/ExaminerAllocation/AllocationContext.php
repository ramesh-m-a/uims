<?php

namespace App\Services\ExaminerAllocation;

final class AllocationContext
{
    public function __construct(
        public readonly int $yearId,
        public readonly int $monthId,
        public readonly int $schemeId,
        public readonly int $degreeId,

        public readonly int $userId,
        public readonly int $streamId,
    ) {}

    public static function fromFilters(
        int $yearId,
        int $monthId,
        int $schemeId,
        int $degreeId,
        int $userId,
        int $streamId,
    ): self {
        return new self(
            yearId: $yearId,
            monthId: $monthId,
            schemeId: $schemeId,
            degreeId: $degreeId,
            userId: $userId,
            streamId: $streamId,
        );
    }

    public function scopeKey(): string
    {
        return implode(':', [
            $this->yearId,
            $this->monthId,
            $this->schemeId,
            $this->degreeId,
        ]);
    }
}
