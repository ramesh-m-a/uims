<?php

namespace App\Livewire\Admin\PermissionTemplate;

use App\Models\Admin\Permission;
use App\Models\Admin\PermissionTemplate;
use Livewire\Component;

class TemplateEditor extends Component
{
    public ?PermissionTemplate $template = null;

    public string $name = '';
    public string $description = '';

    public array $matrix = [];
    public array $selectedPermissions = [];

    public function mount(?PermissionTemplate $template = null): void
    {
        $this->template = $template;

        if ($template) {
            $this->name = $template->name;
            $this->description = $template->description ?? '';
            $this->selectedPermissions = $template
                ->permissions()
                ->pluck('permissions.id')
                ->toArray();
        }

        $this->buildMatrix();
    }

    private function buildMatrix(): void
    {
        $permissions = Permission::orderBy('module')->orderBy('action')->get();

        $matrix = [];

        foreach ($permissions as $perm) {

            [$group, $entity] = str_contains($perm->module, '.')
                ? explode('.', $perm->module, 2)
                : [$perm->module, $perm->module];

            $matrix[strtoupper($group)][ucfirst($entity)][$perm->action] = [
                'id'      => $perm->id,
                'checked' => in_array($perm->id, $this->selectedPermissions),
            ];
        }

        $this->matrix = $matrix;
    }

    public function toggle(int $permissionId): void
    {
        if (in_array($permissionId, $this->selectedPermissions)) {
            $this->selectedPermissions = array_diff(
                $this->selectedPermissions,
                [$permissionId]
            );
        } else {
            $this->selectedPermissions[] = $permissionId;
        }

        $this->buildMatrix();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:permission_templates,name,' .
                ($this->template?->id),
        ]);

        $template = PermissionTemplate::updateOrCreate(
            ['id' => $this->template?->id],
            [
                'name' => $this->name,
                'description' => $this->description,
            ]
        );

        $template->permissions()->sync($this->selectedPermissions);

        return redirect()
            ->route('permission-templates.index')
            ->with('success', 'Template saved');
    }

    public function render()
    {
        return view('livewire.admin.permission-template.template-editor');
    }
}
