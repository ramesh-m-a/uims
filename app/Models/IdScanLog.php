<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdScanLog extends Model
{
    protected $fillable = [
        'user_id',
        'ip',
        'device_hash',
        'user_agent',
        'valid',
        'scanned_at',
    ];
}
