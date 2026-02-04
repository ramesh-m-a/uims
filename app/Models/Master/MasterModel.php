<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

abstract class MasterModel extends Model
{
    use SoftDeletes;

    /**
     * Academic timestamps
     */
    public $timestamps = true;

    /**
     * Auto-handle created_by & updated_by
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (SchemaHasColumn($model, 'created_by')) {
                $model->created_by = Auth::check()
                    ? Auth::user()->id
                    : null;
            }

            if (SchemaHasColumn($model, 'updated_by')) {
                $model->updated_by = Auth::check()
                    ? Auth::user()->id
                    : null;
            }
        });

        static::updating(function ($model) {
            if (SchemaHasColumn($model, 'updated_by')) {
                $model->updated_by = Auth::check()
                    ? Auth::user()->id
                    : null;
            }
        });
    }
}

/**
 * Helper to safely check column existence
 */
function SchemaHasColumn($model, string $column): bool
{
    try {
        return \Schema::hasColumn($model->getTable(), $column);
    } catch (\Throwable $e) {
        return false;
    }
}
