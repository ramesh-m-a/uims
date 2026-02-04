<?php

// app/Models/Admin/PermissionChangeAudit.php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PermissionChangeAudit extends Model
{
    protected $fillable = [
        'actor_id',
        'target_user_id',
        'source_user_id',
        'before',
        'after',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];
}
