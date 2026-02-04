<?php

namespace App\Livewire\Master\Config\Academic\Department;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Config\Academic\Department;
use App\Models\Master\Config\Academic\Stream;

class DepartmentTable extends MasterTableBase
{
    use MasterFormBase;

    protected string $modelClass = Department::class;

    public string $pageRoute = '';

    public bool $showModal = false;
    public ?int $editingId = null;

    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();
    }

    protected function permissionPrefix(): string
    {
        return 'master.config.academic.department';
    }

    protected function configKey(): string
    {
        return 'master.config.academic.department';
    }

    protected function formDefaults(): array
    {
        return [
            'mas_department_name'       => '',
            'mas_department_stream_id'  => null,
            'mas_department_status_id'  => 1,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_department_name'      => 'required|string|max:150',
            'form.mas_department_stream_id' => 'required|exists:mas_stream,id',
            'form.mas_department_status_id' => 'required|integer',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.mas_department_name'      => 'Department',
            'form.mas_department_stream_id' => 'Stream',
            'form.mas_department_status_id' => 'Status',
        ];
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with('streams')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->pageIds   = $rows->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.config.academic.department.department-table', [
            'departments' => $rows,
            'streams'     => Stream::orderBy('mas_stream_name')->get(),

            'columns'        => $this->columns(),
            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'filters'        => $this->filters,
            'sortField'      => $this->sortField,
            'sortDirection'  => $this->sortDirection,

            'canCreate'      => $this->can('create'),
            'canEdit'        => $this->can('edit'),
            'canDelete'      => $this->can('delete'),
            'canRestore'     => $this->can('restore'),

            'pageRoute'      => $this->pageRoute,
            'showModal'      => $this->showModal,
            'editingId'      => $this->editingId,
            'showDeleteModal'=> $this->showDeleteModal,
        ]);
    }
}
