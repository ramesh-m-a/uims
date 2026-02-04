<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\User;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'table_name',
        'record_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Actor who performed the action
     * ✔ user
     * ✔ admin
     * ✔ principal
     * ✔ rguhs
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Display-safe actor name
     */
    public function actorName(): string
    {
        if ($this->actor) {
            return strtoupper($this->actor->name);
        }

        return 'SYSTEM';
    }
}
