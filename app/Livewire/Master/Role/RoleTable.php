<?php

namespace App\Livewire\Master\Role;

use App\Livewire\Master\MasterTableBase;
use App\Models\Admin\Role;

class RoleTable extends MasterTableBase
{
    protected string $modelClass = Role::class;

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $pageRoute = '';

    /** 🔥 Lock parent after create */
    public bool $lockParent = false;

    /** 🔥 Parent role dropdown */
    public $parentRoles;

    public array $form = [
        'name'            => '',
        'description'     => '',
        'parent_role_id'  => null,
        'roles_status_id' => 1,
    ];

    protected function permissionPrefix(): string
    {
        return 'master.role';
    }

    protected function configKey(): string
    {
        return 'master.role';
    }

    protected function rules(): array
    {
        return [
            'form.name' => [
                'required',
                'string',
                'max:100',
                'unique:roles,name,' . $this->editingId,
            ],
            'form.description'     => ['nullable', 'string', 'max:255'],
            'form.parent_role_id'  => ['nullable', 'exists:roles,id'],
            'form.roles_status_id' => ['required', 'exists:mas_status,id'],
        ];
    }

    public function mount(): void
    {
        $this->pageRoute   = request()->route()->getName();
        $this->parentRoles = Role::orderBy('name')->get();
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.master.role.role-table', [
            'rows' => $rows,
        ]);
    }

    public function create(): void
    {
        abort_unless($this->can('create'), 403);

        $this->editingId  = null;
        $this->lockParent = false;

        $this->form = [
            'name'            => '',
            'description'     => '',
            'parent_role_id'  => null,
            'roles_status_id' => 1,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function editRow(int $id): void
    {
        abort_unless($this->can('edit'), 403);

        $role = Role::findOrFail($id);

        $this->editingId  = $id;
        $this->lockParent = true;

        $this->form = $role->only([
            'name',
            'description',
            'parent_role_id',
            'roles_status_id',
        ]);

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        Role::updateOrCreate(
            ['id' => $this->editingId],
            $this->form
        );

        $this->js(
            "Alpine.store('toast').push('success', 'Role saved successfully')"
        );

        $this->cancelEdit();
    }

    public function cancelEdit(): void
    {
        $this->showModal  = false;
        $this->editingId  = null;
        $this->lockParent = false;
        $this->resetValidation();
    }
}
