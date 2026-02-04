<?php

namespace App\Livewire\Master\Role;

use App\Models\Admin\PermissionTemplate;
use App\Models\Admin\PermissionChangeAudit;
use App\Models\Admin\Role;
use Livewire\Component;

class ApplyTemplateToRole extends Component
{
    public Role $role;

    public ?int $templateId = null;
    public int $previewCount = 0;

    public function updatedTemplateId(): void
    {
        $this->previewCount = PermissionTemplate::find($this->templateId)
            ?->permissions()
            ->count() ?? 0;
    }

    public function apply(): void
    {
        abort_unless(
            auth()->user()->hasPermission('master.role.edit'),
            403
        );

        $template = PermissionTemplate::findOrFail($this->templateId);

        $before = $this->role->permissions()
            ->pluck('name')
            ->sort()
            ->values()
            ->toArray();

        $this->role->permissions()->sync(
            $template->permissions()->pluck('id')->toArray()
        );

        $after = $this->role->permissions()
            ->pluck('name')
            ->sort()
            ->values()
            ->toArray();

        PermissionChangeAudit::create([
            'actor_id'       => auth()->id(),
            'target_user_id' => 0, // role-level
            'source_user_id' => null,
            'before'         => $before,
            'after'          => $after,
        ]);

        foreach ($this->role->users as $user) {
            $user->flushPermissionCache();
        }

        $this->dispatchBrowserEvent('toast', [
            'type' => 'success',
            'message' => 'Template applied to role',
        ]);
    }

    public function render()
    {
        return view('livewire.master.role.apply-template-to-role', [
            'templates' => PermissionTemplate::orderBy('name')->get(),
        ]);
    }
}
