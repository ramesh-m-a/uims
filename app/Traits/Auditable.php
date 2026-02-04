<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            self::logAudit('create', $model, null, $model->getAttributes());
        });

        static::updating(function ($model) {
            self::logAudit(
                'update',
                $model,
                $model->getOriginal(),
                $model->getDirty()
            );
        });

        static::deleted(function ($model) {
            self::logAudit('delete', $model, $model->getOriginal(), null);
        });

        static::restored(function ($model) {
            self::logAudit('restore', $model, null, $model->getAttributes());
        });
    }

    protected static function logAudit(
        string $action,
               $model,
        ?array $oldValues,
        ?array $newValues
    ): void {
        DB::table('audit_logs')->insert([
            'user_id'    => Auth::id(),
            'table_name' => $model->getTable(),
            'record_id'  => $model->getKey(),
            'action'     => $action,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }
}
