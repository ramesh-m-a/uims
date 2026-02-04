<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    protected $table = 'mas_admin_role';

    protected $fillable = [
        'mas_admin_role_name',
        'mas_admin_role_status_id',
    ];

    /* =========================
  | SCOPES
  ========================= */

    public function scopeActive($query)
    {
        return $query->where('mas_admin_role_status_id', 1);
    }
}
