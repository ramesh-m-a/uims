<?php

namespace App\Models\Admin;

use App\Models\Master\Common\Status;
use App\Models\Master\Config\Academic\College;
use App\Models\Master\Config\Academic\Department;
use App\Models\Master\Config\Academic\Designation;
use App\Models\Master\Config\Academic\Stream;
use App\Traits\HasStatus;
use App\Traits\HasStatusWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\UserProfileDraft;

/* === RELATED MODELS === */

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasStatus, HasStatusWorkflow;

    /**
     * Always eager-load these for UI tables
     */
    protected $with = ['stream', 'college', 'designation', 'status'];

    /* =========================
     | MASS ASSIGNMENT
     ========================= */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'photo_path',
        'force_password_change',
        'user_stream_id',
        'user_college_id',
        'user_designation_id',
        'user_role_id',
        'user_status_id',
        'user_tin',
    ];

    /* =========================
     | HIDDEN ATTRIBUTES
     ========================= */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /* =========================
     | CASTS
     ========================= */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /* =====================================================
     | MASTER RELATIONS (CRITICAL)
     ===================================================== */

    /** Stream */
    public function stream()
    {
        return $this->belongsTo(Stream::class, 'user_stream_id');
    }

    /** College */
    public function college()
    {
        return $this->belongsTo(College::class, 'user_college_id');
    }

    /** Designation */
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'user_designation_id');
    }

    /**
     * Primary Role (DISPLAY ONLY)
     * ⚠️ NOT RBAC
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'user_role_id');
    }

    /** Status (mas_status) */
    public function status()
    {
        return $this->belongsTo(Status::class, 'user_status_id');
    }

    /* =====================================================
     | PROFILE DRAFT (🔥 REQUIRED)
     ===================================================== */

    /**
     * Draft profile for my-details workflow
     * ✔ Used by My Details table
     * ✔ Used by Edit lock after submit
     * ✔ Used by approval flows
     */
    public function profileDraft()
    {
        return $this->hasOne(
            \App\Models\UserProfileDraft::class,
            'user_id'
        );
    }

    /* =====================================================
     | RBAC (MANY-TO-MANY)
     ===================================================== */

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_role',
            'user_id',
            'role_id'
        );
    }

    /* =====================================================
     | USER-LEVEL PERMISSION OVERRIDES
     ===================================================== */

    public function permissions()
    {
        return $this->belongsToMany(
            \App\Models\Admin\Permission::class,
            'user_permission',
            'user_id',
            'permission_id'
        );
    }

    public function cachedPermissions()
    {
        return Cache::remember(
            "user_permissions_{$this->id}",
            now()->addMinutes(30),
            function () {

                $rolePermissions = $this->roles
                    ->flatMap(fn ($role) => $role->allPermissions())
                    ->pluck('name');

                $userOverrides = $this->permissions()->pluck('name');

                return $rolePermissions
                    ->merge($userOverrides)
                    ->unique()
                    ->values();
            }
        );
    }

    public function hasPermission(string $permission): bool
    {
        // 🔥 SUPER-ADMIN BYPASS
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return $this->cachedPermissions()->contains($permission);
    }

    public function flushPermissionCache(): void
    {
        Cache::forget("user_permissions_{$this->id}");
    }

    /* =====================================================
     | ROLE HELPERS
     ===================================================== */

    public function hasRole(string $role): bool
    {
        // 🔥 SUPER-ADMIN BYPASS
        if ($this->roles()->where('name', 'super-admin')->exists()) {
            return true;
        }

        return $this->roles()->where('name', $role)->exists();
    }

    /* =====================================================
     | UI HELPERS
     ===================================================== */

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($w) => Str::substr($w, 0, 1))
            ->implode('');
    }

    /**
     * Avatar URL (profile photo or fallback)
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->photo_path) {
            return null;
        }

        // Remove extension if exists
        $base = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '', $this->photo_path);

        foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
            $relative = "storage/{$base}.{$ext}";
            $fullPath = public_path($relative);

            if (file_exists($fullPath)) {
                return asset($relative) . '?v=' . filemtime($fullPath);
            }
        }

        return null;
    }

    /* =====================================================
     | TEACHER BASIC DETAILS (OPTIONAL)
     ===================================================== */

    public function basicDetails()
    {
        return $this->hasOne(
            \App\Models\Teacher\BasicDetails::class,
            'basic_details_user_id'
        );
    }

    public function department()
    {
        return $this->hasOneThrough(
            Department::class,
            \App\Models\Teacher\BasicDetails::class,
            'basic_details_user_id',        // FK on basic_details table
            'id',                           // PK on mas_department
            'id',                           // PK on users
            'basic_details_department_id'   // FK on basic_details pointing to department
        );
    }
}
