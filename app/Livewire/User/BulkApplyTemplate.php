<?php

namespace App\Livewire\User;

use App\Models\Admin\User;
use App\Models\Admin\PermissionTemplate;
use App\Models\Admin\PermissionChangeAudit;
use Livewire\Component;

class BulkApplyTemplate extends Component
{
    public array $userIds = [];
    public ?int $templateId = null;

    public bool $preview = false;

    public function apply(): void
    {
        abort_unless(auth()->user()->hasPermission('user.edit'), 403);

        $template = PermissionTemplate::findOrFail($this->templateId);

        foreach (User::whereIn('id', $this->userIds)->get() as $user) {

            $before = $user->permissions()->pluck('name')->toArray();

            $user->permissions()->sync(
                $template->permissions()->pluck('id')->toArray()
            );

            $after = $user->permissions()->pluck('name')->toArray();

            PermissionChangeAudit::create([
                'actor_id'       => auth()->id(),
                'target_user_id' => $user->id,
                'source_user_id' => null,
                'before'         => $before,
                'after'          => $after,
            ]);

            $user->flushPermissionCache();
        }

        $this->dispatchBrowserEvent('toast', [
            'type' => 'success',
            'message' => 'Template applied to selected users',
        ]);

        $this->reset();
    }

    public function render()
    {
        return view('livewire.user.bulk-apply-template', [
            'users'     => User::orderBy('name')->get(),
            'templates' => PermissionTemplate::orderBy('name')->get(),
        ]);
    }
}
