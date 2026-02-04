<?php

namespace App\Livewire\Master\Role;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RolePermissionMatrix extends Component
{
    public Role $role;

    public array $permissionsMatrix = [];

    /** Column-level select all */
    public array $selectAllColumns = [];

    /** Row-level select all */
    public array $selectAllRows = [];

    public bool $canEdit = false;

    protected array $actions = ['create','edit','delete','view','restore'];

    public function mount(Role $role): void
    {
        $this->role = $role;
        $this->canEdit = auth()->user()?->hasPermission('master.role.edit') ?? false;

        $this->buildMatrix();
        $this->syncSelectAll();
    }

    protected function buildMatrix(): void
    {
        $permissions = Permission::all()->groupBy(
            fn ($p) => explode('.', $p->name)[0]
        );

        $rolePermissions = $this->role
            ->permissions()
            ->pluck('name')
            ->toArray();

        foreach ($permissions as $module => $perms) {
            foreach ($this->actions as $action) {
                $permName = "{$module}.{$action}";

                $this->permissionsMatrix[$module][$action] =
                    in_array($permName, $rolePermissions);
            }
        }
    }

    /* =========================
     | COLUMN SELECT ALL
     ========================= */
    public function updatedSelectAllColumns($value, $action): void
    {
        foreach ($this->permissionsMatrix as $module => $actions) {
            if (array_key_exists($action, $actions)) {
                $this->permissionsMatrix[$module][$action] = $value;
            }
        }

        $this->syncSelectAll();
    }

    /* =========================
     | ROW SELECT ALL
     ========================= */
    public function updatedSelectAllRows($value, $module): void
    {
        foreach ($this->permissionsMatrix[$module] as $action => $enabled) {
            $this->permissionsMatrix[$module][$action] = $value;
        }

        $this->syncSelectAll();
    }

    /* =========================
     | AUTO SYNC
     ========================= */
    public function updatedPermissionsMatrix(): void
    {
        $this->syncSelectAll();
    }

    protected function syncSelectAll(): void
    {
        // Column sync
        foreach ($this->actions as $action) {
            $this->selectAllColumns[$action] = collect($this->permissionsMatrix)
                ->pluck($action)
                ->filter(fn ($v) => $v !== null)
                ->every(fn ($v) => $v === true);
        }

        // Row sync
        foreach ($this->permissionsMatrix as $module => $actions) {
            $this->selectAllRows[$module] =
                collect($actions)->every(fn ($v) => $v === true);
        }
    }

    /* =========================
     | SAVE
     ========================= */
    public function save(): void
    {
        abort_unless($this->canEdit, 403);

        DB::transaction(function () {

            $permissionIds = [];

            foreach ($this->permissionsMatrix as $module => $actions) {
                foreach ($actions as $action => $enabled) {
                    if (!$enabled) continue;

                    $perm = Permission::where(
                        'name', "{$module}.{$action}"
                    )->first();

                    if ($perm) {
                        $permissionIds[] = $perm->id;
                    }
                }
            }

            $this->role->permissions()->sync($permissionIds);
        });

        $this->dispatchBrowserEvent('toast', [
            'type' => 'success',
            'message' => 'Permissions updated successfully',
        ]);
    }

    public function render()
    {
        return view('livewire.master.role.role-permission-matrix', [
            'pageRoute' => 'master.role.permissions',
            'actions'   => $this->actions,
        ]);
    }
}
