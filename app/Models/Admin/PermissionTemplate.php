<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PermissionTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_template_permission',
            'permission_template_id',
            'permission_id'
        );
    }
}
