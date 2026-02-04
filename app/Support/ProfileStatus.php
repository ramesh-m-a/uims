<?php

namespace App\Support;

use App\Models\UserProfileDraft;

class ProfileStatus
{
    /**
     * Resolve status_id ENUM (single source of truth)
     */
    public static function status(UserProfileDraft $draft): string
    {
        return $draft->status_id ?? 'draft';
    }

    /**
     * Editable until approved
     */
    public static function canEdit(UserProfileDraft $draft): bool
    {
        return self::status($draft) !== 'approved';
    }

    /**
     * Can submit ONLY when draft
     */
    public static function canSubmit(UserProfileDraft $draft): bool
    {
        return self::status($draft) === 'draft';
    }

    /**
     * Locked only after approval
     */
    public static function isLocked(UserProfileDraft $draft): bool
    {
        return self::status($draft) === 'approved';
    }
}
