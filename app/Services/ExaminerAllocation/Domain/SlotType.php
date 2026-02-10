<?php

namespace App\Services\ExaminerAllocation\Domain;

final class SlotType
{
    public const INTERNAL_C  = 'Internal-C';
    public const INTERNAL_A  = 'Internal-A';
    public const EXTERNAL    = 'External';
    public const EXTERNAL_O  = 'External-O';

    public static function all(): array
    {
        return [
            self::INTERNAL_C,
            self::INTERNAL_A,
            self::EXTERNAL,
            self::EXTERNAL_O,
        ];
    }
}
