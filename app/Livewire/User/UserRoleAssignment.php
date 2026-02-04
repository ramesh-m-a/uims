<?php

namespace App\Livewire\User;

use App\Models\Admin\Role;
use App\Models\Admin\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UserRoleAssignment extends Component
{
    public User $user;

    public array $selectedRoles = [];

    public function mount(User $user): void
    {
        $this->user = $user;

        $this->selectedRoles = $this->user
            ->roles()
            ->pluck('id')
            ->toArray();
    }

    public function save()
    {
        abort_unless(auth()->user()->hasPermission('user.edit'), 403);

        /**
         * 🔒 SYSTEM / ROOT USERS
         * These users do not participate in RBAC
         */
        if (is_null($this->user->user_role_id)) {
            session()->flash(
                'success',
                'System users do not require role assignment.'
            );

            return redirect()->route('user.index');
        }

        // 🔍 Capture OLD roles
        $oldRoles = $this->user->roles()->pluck('id')->toArray();

        // ✅ FORCE primary role (cannot be removed)
        if (!in_array($this->user->user_role_id, $this->selectedRoles)) {
            $this->selectedRoles[] = $this->user->user_role_id;
        }

        // 🔄 Sync roles
        $this->user->roles()->sync(array_unique($this->selectedRoles));

        // 🧾 AUDIT LOG
        DB::table('audit_logs')->insert([
            'user_id'    => auth()->id(),
            'table_name' => 'users',
            'record_id'  => $this->user->id,
            'action'     => 'update',
            'old_values' => json_encode(['roles' => $oldRoles]),
            'new_values' => json_encode(['roles' => $this->selectedRoles]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 🔥 Clear permission cache
        $this->user->flushPermissionCache();

        session()->flash('success', 'Roles updated successfully.');

        return redirect()->route('user.index');
    }

    public function render()
    {
        return view('livewire.user.user-role-assignment', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function selectAllRoles(): void
    {
        $this->selectedRoles = Role::pluck('id')->toArray();
    }

    public function clearRoles(): void
    {
        // keep primary role always
        $this->selectedRoles = [$this->user->user_role_id];
    }
}
