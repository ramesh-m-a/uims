<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionAudit extends Model
{
    protected $fillable = [
        'user_id',
        'role_id',
        'before',
        'after',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];
}
