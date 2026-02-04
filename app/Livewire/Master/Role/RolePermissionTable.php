<?php

namespace App\Livewire\Master\Role;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use App\Models\PermissionAudit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RolePermissionTable extends Component
{
    public Role $role;

    /** Permission matrix */
    public array $matrix = [];

    /** Selected (DIRECT) permission IDs */
    public array $selectedPermissions = [];

    /** Column select-all state */
    public array $columnSelectAll = [
        'create'  => false,
        'edit'    => false,
        'delete'  => false,
        'view'    => false,
        'restore' => false,
    ];

    /** 🔒 View-only mode */
    public bool $readOnly = true;

    /** UX state */
    public bool $dirty = false;
    public array $originalPermissions = [];

    /* =====================================================
     | MOUNT
     ===================================================== */
    public function mount(Role $role): void
    {
        $this->role = $role;

        $this->readOnly = ! Auth::user()->hasPermission('master.role.edit');

        $this->selectedPermissions = $role
            ->permissions()
            ->pluck('permissions.id')
            ->toArray();

        // 🔥 Track original state
        $this->originalPermissions = $this->selectedPermissions;

        $this->buildMatrix();
        $this->syncColumnStates();
    }


    /* =====================================================
     | BUILD PERMISSION MATRIX
     ===================================================== */
    private function buildMatrix(): void
    {
        $permissions = Permission::orderBy('module')
            ->orderBy('action')
            ->get();

        $matrix = [];
        $inheritedIds = $this->inheritedPermissionIds();

        foreach ($permissions as $perm) {

            if (str_contains($perm->module, '.')) {
                [$group, $entity] = explode('.', $perm->module, 2);
            } else {
                $group  = $perm->module;
                $entity = $perm->module;
            }

            $isInherited = in_array($perm->id, $inheritedIds);

            $matrix[strtoupper($group)][ucfirst($entity)][$perm->action] = [
                'id'         => $perm->id,
                'checked'    => $isInherited || in_array($perm->id, $this->selectedPermissions),
                'inherited'  => $isInherited,
            ];
        }

        $this->matrix = $matrix;
    }

    /* =====================================================
     | TOGGLE SINGLE PERMISSION
     ===================================================== */
    public function togglePermission(int $permissionId): void
    {
        if ($this->readOnly) return;

        // ❌ BLOCK inherited permissions
        if (in_array($permissionId, $this->inheritedPermissionIds())) {
            return;
        }

        if (in_array($permissionId, $this->selectedPermissions)) {
            $this->selectedPermissions = array_values(
                array_diff($this->selectedPermissions, [$permissionId])
            );
        } else {
            $this->selectedPermissions[] = $permissionId;
        }

        $this->buildMatrix();
        $this->syncColumnStates();
    }

    /* =====================================================
     | TOGGLE ENTITY (ROW)
     ===================================================== */
    public function toggleEntity(string $group, string $entity): void
    {
        if ($this->readOnly) return;

        if (!isset($this->matrix[$group][$entity])) return;

        $actions = $this->matrix[$group][$entity];

        $ids = collect($actions)
            ->reject(fn ($a) => $a['inherited']) // ❌ skip inherited
            ->pluck('id')
            ->values()
            ->toArray();

        if (empty($ids)) return;

        $allChecked = empty(array_diff($ids, $this->selectedPermissions));

        $this->selectedPermissions = $allChecked
            ? array_values(array_diff($this->selectedPermissions, $ids))
            : array_values(array_unique(array_merge($this->selectedPermissions, $ids)));

        $this->buildMatrix();
        $this->syncColumnStates();
    }

    /* =====================================================
     | TOGGLE COLUMN
     ===================================================== */
    public function toggleColumn(string $action): void
    {
        if ($this->readOnly) return;

        $ids = Permission::where('action', $action)
            ->pluck('id')
            ->diff($this->inheritedPermissionIds())
            ->toArray();

        if (empty($ids)) return;

        $allChecked = empty(array_diff($ids, $this->selectedPermissions));

        $this->selectedPermissions = $allChecked
            ? array_values(array_diff($this->selectedPermissions, $ids))
            : array_values(array_unique(array_merge($this->selectedPermissions, $ids)));

        $this->buildMatrix();
        $this->syncColumnStates();
    }

    /* =====================================================
     | SYNC COLUMN STATES
     ===================================================== */
    private function syncColumnStates(): void
    {
        foreach (array_keys($this->columnSelectAll) as $action) {

            $ids = Permission::where('action', $action)
                ->pluck('id')
                ->diff($this->inheritedPermissionIds())
                ->toArray();

            $this->columnSelectAll[$action] =
                !empty($ids) && empty(array_diff($ids, $this->selectedPermissions));
        }
    }

    /* =====================================================
     | SAVE (DIRECT PERMISSIONS ONLY)
     ===================================================== */
    public function save(): void
    {
        abort_unless(
            Auth::user()->hasPermission('master.role.edit'),
            403,
            'You do not have permission to modify role permissions.'
        );

        $before = $this->role
            ->permissions()
            ->pluck('name')
            ->sort()
            ->values()
            ->toArray();

        // 🔥 REMOVE INHERITED PERMISSIONS BEFORE SAVE
        $directPermissions = array_diff(
            $this->selectedPermissions,
            $this->inheritedPermissionIds()
        );

        $this->role->permissions()->sync($directPermissions);

        $after = $this->role
            ->permissions()
            ->pluck('name')
            ->sort()
            ->values()
            ->toArray();

        PermissionAudit::create([
            'user_id' => auth()->id(),
            'role_id' => $this->role->id,
            'before'  => $before,
            'after'   => $after,
        ]);

        // 🔥 Flush cache for all users of this role
        foreach ($this->role->users as $user) {
            $user->flushPermissionCache();
        }

        $this->originalPermissions = $this->selectedPermissions;
        $this->dirty = false;

        $this->dispatchBrowserEvent('toast', [
            'type'    => 'success',
            'message' => 'Permissions updated successfully',
        ]);
    }

    /* =====================================================
     | INHERITED PERMISSIONS
     ===================================================== */
    private function inheritedPermissionIds(): array
    {
        if (!$this->role->parent) {
            return [];
        }

        return $this->role->parent
            ->allPermissions()
            ->pluck('id')
            ->toArray();
    }

    /* =====================================================
     | RENDER
     ===================================================== */
    public function render()
    {
        return view('livewire.master.role.role-permission-table');
    }

    public function updatedSelectedPermissions(): void
    {
        $this->dirty = $this->selectedPermissions !== $this->originalPermissions;
    }

    public function permissionSummary(): array
    {
        return collect($this->matrix)
            ->map(function ($entities) {
                return collect($entities)->flatMap(function ($actions) {
                    return collect($actions)
                        ->filter(fn ($a) => $a['checked'])
                        ->keys();
                })->unique()->values();
            })
            ->toArray();
    }

}
