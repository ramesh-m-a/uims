<?php

namespace App\Models;

use App\Models\Admin\User;
use Illuminate\Database\Eloquent\Model;

class UserProfileDraft extends Model
{
    protected $table = 'user_profile_drafts';

    protected $fillable = [
        'user_id',
        'basic_details_id',
        'current_tab',
        'completed_tabs',
        'status_id',
        'data',
        'locked_by',
        'locked_at',
        'submitted_at',
    ];

    protected $casts = [
        'completed_tabs' => 'array',
        'data'           => 'array',
        'locked_at'      => 'datetime',
        'submitted_at'   => 'datetime',
    ];

    /**
     * 🔒 HARD GUARANTEE:
     * completed_tabs will ALWAYS be an array,
     * even if DB contains double-encoded JSON.
     */
    public function getCompletedTabsAttribute($value): array
    {
        // If already array (normal good data)
        if (is_array($value)) {
            return $value;
        }

        // If null or empty
        if (empty($value)) {
            return [];
        }

        // Try JSON decode once
        $decoded = json_decode($value, true);

        // If still string, try decode again (double encoded case)
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Always store completed_tabs cleanly as JSON array
     */
    public function setCompletedTabsAttribute($value): void
    {
        $this->attributes['completed_tabs'] = json_encode(
            is_array($value) ? array_values($value) : []
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Profile workflow status label
     * draft | submitted | approved | rejected
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? '');
    }
}
