<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(
        string $table,
        ?int $recordId,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        AuditLog::create([
            'user_id'   => Auth::id(),
            'table_name'=> $table,
            'record_id' => $recordId,
            'action'    => $action,
            'old_values'=> $oldValues,
            'new_values'=> $newValues,
        ]);
    }
}
