<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAuditTrail
{
    protected static function bootHasAuditTrail(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                if (Auth::check()) {
                    $model->updated_by = Auth::id();
                    $model->saveQuietly();
                }
            }
        });

        static::restoring(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
