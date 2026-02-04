<?php

namespace App\Models;

use App\Models\Admin\User;
use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'filename',
        'disk',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
