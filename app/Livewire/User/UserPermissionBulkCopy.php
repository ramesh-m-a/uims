<?php

namespace App\Livewire\User;

use App\Models\Admin\User;
use App\Models\Admin\Permission;
use App\Models\Admin\PermissionChangeAudit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserPermissionBulkCopy extends Component
{
    public User $targetUser;
    public ?int $sourceUserId = null;

    public array $preview = [
        'add' => [],
        'remove' => [],
    ];

    public bool $confirmed = false;

    public function mount(User $user): void
    {
        abort_unless(Auth::user()->hasPermission('user.edit'), 403);
        $this->targetUser = $user;
    }

    public function updatedSourceUserId(): void
    {
        $this->buildPreview();
    }

    private function buildPreview(): void
    {
        if (! $this->sourceUserId) {
            $this->preview = ['add' => [], 'remove' => []];
            return;
        }

        $source = User::findOrFail($this->sourceUserId);

        $sourcePerms = $source->permissions()->pluck('permissions.id')->toArray();
        $targetPerms = $this->targetUser->permissions()->pluck('permissions.id')->toArray();

        $this->preview['add'] = Permission::whereIn(
            'id',
            array_diff($sourcePerms, $targetPerms)
        )->pluck('name')->toArray();

        $this->preview['remove'] = Permission::whereIn(
            'id',
            array_diff($targetPerms, $sourcePerms)
        )->pluck('name')->toArray();
    }

    public function apply()
    {
        abort_unless($this->confirmed, 422);

        $before = $this->targetUser->permissions()
            ->pluck('name')->toArray();

        $sourcePermIds = User::findOrFail($this->sourceUserId)
            ->permissions()->pluck('permissions.id')->toArray();

        $this->targetUser->permissions()->sync($sourcePermIds);
        $this->targetUser->flushPermissionCache();

        $after = Permission::whereIn('id', $sourcePermIds)
            ->pluck('name')->toArray();

        PermissionChangeAudit::create([
            'actor_id'       => auth()->id(),
            'target_user_id' => $this->targetUser->id,
            'source_user_id' => $this->sourceUserId,
            'before'         => $before,
            'after'          => $after,
        ]);

        $this->dispatchBrowserEvent('toast', [
            'type' => 'success',
            'message' => 'Permissions copied successfully',
        ]);

        return redirect()->route('user.permissions', $this->targetUser);
    }

    public function render()
    {
        return view('livewire.user.user-permission-bulk-copy', [
            'users' => User::where('id', '!=', $this->targetUser->id)->get(),
        ]);
    }
}
