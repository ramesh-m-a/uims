<?php

namespace App\Traits;

use App\Support\Status;
use App\Enums\StatusEnum;

trait HasStatus
{
    /**
     * Set status using code (safe way)
     */
    public function setStatus(string $statusCode): void
    {
        $this->update([
            'status_id' => Status::id($statusCode),
        ]);
    }

    /**
     * Check if current model has given status
     */
    public function isStatus(string $statusCode): bool
    {
        return $this->status?->mas_status_code === $statusCode;
    }

    /**
     * Check if current status is in list
     */
    public function hasAnyStatus(array $codes): bool
    {
        return in_array($this->status?->mas_status_code, $codes, true);
    }

    /**
     * Relationship (assumes status_id FK)
     */
    public function status()
    {
        return $this->belongsTo(
            \App\Models\Master\MasStatus::class,
            'status_id'
        );
    }
}
