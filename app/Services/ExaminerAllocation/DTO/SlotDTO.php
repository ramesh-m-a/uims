<?php

namespace App\Services\ExaminerAllocation\DTO;

final class SlotDTO
{
    public string $status;
    public string $colour;

    public function __construct(
        public string $type,
        public ?int $examinerId = null,
        public ?string $name = null,
        public ?string $mobile = null,
        ?string $status = null,
        ?string $colour = null,
    ) {
        $this->status = $status ?? 'Assigned';
        $this->colour = $colour ?? '#6c757d';
    }

    public function isFilled(): bool
    {
        return !empty($this->examinerId);
    }
}
