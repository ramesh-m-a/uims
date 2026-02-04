<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTinAudit extends Model
{
    protected $fillable = [
        'user_id',
        'tin',
        'issued_from',
    ];
}
