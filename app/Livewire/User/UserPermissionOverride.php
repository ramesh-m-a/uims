<?php

namespace App\Livewire\User;

use App\Models\Admin\Permission;
use App\Models\Admin\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserPermissionOverride extends Component
{
    public User $user;

    public array $matrix = [];
    public array $selectedPermissions = [];
    public array $originalPermissions = [];

    public bool $readOnly = true;
    public bool $dirty = false;

    public int $effectiveCount = 0;
    public int $overrideCount = 0;

    public string $search = '';

    /** Column select-all state */
    public array $columnSelectAll = [
        'view'    => false,
        'create'  => false,
        'edit'    => false,
        'delete'  => false,
        'restore' => false,
    ];

    public function mount(User $user): void
    {
        $this->user = $user;

        $this->readOnly = ! Auth::user()->hasPermission('user.edit');

        $this->selectedPermissions = $user
            ->permissions()
            ->pluck('permissions.id')
            ->toArray();

        $this->originalPermissions = $this->selectedPermissions;

        $this->buildMatrix();
        $this->syncColumnState();
    }

    /* =====================================================
     | MATRIX
     ===================================================== */
    private function buildMatrix(): void
    {
        $permissions = Permission::orderBy('module')
            ->orderBy('action')
            ->get();

        $matrix = [];

        // Permissions inherited from roles
        $rolePermissionIds = $this->user->cachedPermissions()
            ->map(fn ($name) =>
            Permission::where('name', $name)->value('id')
            )
            ->filter()
            ->toArray();

        foreach ($permissions->filter(function ($perm) {
            if ($this->search === '') return true;

            return str_contains(
                strtolower($perm->module . '.' . $perm->action),
                strtolower($this->search)
            );
        }) as $perm) {

            [$group, $entity] = str_contains($perm->module, '.')
                ? explode('.', $perm->module, 2)
                : [$perm->module, $perm->module];

            $inherited = in_array($perm->id, $rolePermissionIds);

            $matrix[strtoupper($group)][ucfirst($entity)][$perm->action] = [
                'id'        => $perm->id,
                'checked'   => $inherited || in_array($perm->id, $this->selectedPermissions),
                'inherited' => $inherited,
            ];
        }

        $this->matrix = $matrix;
        $this->dirty  = $this->selectedPermissions !== $this->originalPermissions;

        // Counts (CRITICAL)
        $this->effectiveCount = collect($this->matrix)
            ->flatMap(fn ($e) => $e)
            ->flatMap(fn ($a) => $a)
            ->filter(fn ($p) => $p['checked'])
            ->count();

        $this->overrideCount = count($this->selectedPermissions);
    }

    /* =====================================================
     | TOGGLES
     ===================================================== */
    public function togglePermission(int $permissionId): void
    {
        if ($this->readOnly) return;

        if (in_array($permissionId, $this->selectedPermissions)) {
            $this->selectedPermissions = array_values(
                array_diff($this->selectedPermissions, [$permissionId])
            );
        } else {
            $this->selectedPermissions[] = $permissionId;
        }

        $this->buildMatrix();
        $this->syncColumnState();
    }

    public function toggleColumn(string $action): void
    {
        if ($this->readOnly) return;

        $ids = Permission::where('action', $action)
            ->pluck('id')
            ->toArray();

        $allChecked = empty(array_diff($ids, $this->selectedPermissions));

        $this->selectedPermissions = $allChecked
            ? array_values(array_diff($this->selectedPermissions, $ids))
            : array_values(array_unique(array_merge($this->selectedPermissions, $ids)));

        $this->buildMatrix();
        $this->syncColumnState();
    }

    private function syncColumnState(): void
    {
        foreach ($this->columnSelectAll as $action => $_) {
            $ids = Permission::where('action', $action)->pluck('id')->toArray();
            $this->columnSelectAll[$action] =
                !empty($ids) && empty(array_diff($ids, $this->selectedPermissions));
        }
    }

    /* =====================================================
     | RESET
     ===================================================== */
    public function resetToRole(): void
    {
        abort_unless(! $this->readOnly, 403);

        $this->selectedPermissions = [];
        $this->buildMatrix();
        $this->syncColumnState();
    }

    /* =====================================================
     | SAVE
     ===================================================== */
    public function save(): void
    {
        abort_unless(! $this->readOnly, 403);

        $this->user->permissions()->sync($this->selectedPermissions);
        $this->user->flushPermissionCache();

        $this->originalPermissions = $this->selectedPermissions;
        $this->dirty = false;

        $this->dispatchBrowserEvent('toast', [
            'type' => 'success',
            'message' => 'User permission overrides saved',
        ]);
    }

    public function render()
    {
        return view('livewire.user.user-permission-override');
    }
}
