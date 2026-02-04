<?php

namespace App\Services\ExaminerAllocation\DTO;

final class AllocationRowDTO
{
    public function __construct(
        public int $examinerId,
        public string $name,
        public string $mobile,
        public string $status,
        public string $colour,

        public string $batchName,
        public string $subjectName,
        public string $centreName,

        public string $fromDate,
        public string $toDate,

        public int $rangeId,
        public int $batchId,

        public int $yearId,
        public int $monthId,
        public int $schemeId,
        public int $degreeId,

        public int $subjectId,
        public int $centreId,

        public string $examinerType,
        public int $examinerTypeId,
        public int $statusId,
    ) {}
}
