<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RequestStatusService
{
    public function getPendingStatusIds(): array
    {
        return DB::table('request_status_master')
            ->where('is_pending', 1)
            ->pluck('id')
            ->map(fn($v) => (int)$v)
            ->all();
    }

    public function getStatusByCode(string $code): ?object
    {
        return DB::table('request_status_master')
            ->where('code', $code)
            ->first();
    }
}
