<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'roles_status_id',
        'parent_role_id',
    ];

    /* =========================
     | RELATIONS
     ========================= */

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permission',
            'role_id',
            'permission_id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_role',
            'role_id',
            'user_id'
        );
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_role_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_role_id');
    }

    /* =========================
     | PERMISSION INHERITANCE
     ========================= */

    public function allPermissions()
    {
        $permissions = collect($this->permissions);

        if ($this->parent) {
            $permissions = $permissions->merge(
                $this->parent->allPermissions()
            );
        }

        return $permissions->unique('id')->values();
    }

    /* =========================
     | SAFETY
     ========================= */

    public function isAncestorOf(Role $role): bool
    {
        $parent = $role->parent;

        while ($parent) {
            if ($parent->id === $this->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }
}
