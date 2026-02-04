<?php

namespace App\Livewire\User;

use App\Models\Admin\PermissionTemplate;
use App\Models\Admin\PermissionChangeAudit;
use App\Models\Admin\User;
use Livewire\Component;

class ApplyTemplateToUser extends Component
{
    public User $user;
    public ?int $templateId = null;

    public function apply(): void
    {
        abort_unless(
            auth()->user()->hasPermission('user.edit'),
            403
        );

        $template = PermissionTemplate::findOrFail($this->templateId);

        $before = $this->user->permissions()
            ->pluck('name')
            ->toArray();

        $this->user->permissions()->sync(
            $template->permissions()->pluck('id')->toArray()
        );

        $after = $this->user->permissions()
            ->pluck('name')
            ->toArray();

        PermissionChangeAudit::create([
            'actor_id'       => auth()->id(),
            'target_user_id' => $this->user->id,
            'source_user_id' => null,
            'before'         => $before,
            'after'          => $after,
        ]);

        $this->user->flushPermissionCache();

        $this->dispatchBrowserEvent('toast', [
            'type' => 'success',
            'message' => 'Template applied to user',
        ]);
    }

    public function render()
    {
        return view('livewire.user.apply-template-to-user', [
            'templates' => PermissionTemplate::orderBy('name')->get(),
        ]);
    }
}
