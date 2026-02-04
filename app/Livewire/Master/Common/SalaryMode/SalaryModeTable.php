<?php

namespace App\Livewire\Master\Common\SalaryMode;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;

class SalaryModeTable extends MasterTableBase
{
    use MasterFormBase;

    protected string $modelClass = Bank::class;

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
        return 'master.common.salary-mode.salary-mode';
    }

    protected function configKey(): string
    {
        return 'master.common.salary-mode.salary-mode';
    }

    protected function formDefaults(): array
    {
        return [
            'mas_bank_name'       => '',
            'mas_bank_status_id'  => 1,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_bank_name'      => 'required|string|max:150|unique:mas_bank,mas_bank_name,' . $this->editingId,
            'form.mas_bank_status_id'=> 'required|integer',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.mas_bank_name'      => 'Bank Name',
            'form.mas_bank_status_id'=> 'Status',
        ];
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->pageIds   = $rows->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.common.salary-mode.salary-mode-table', [
            'banks'          => $rows,
            'columns'        => $this->columns(),
            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'filters'        => $this->filters,
            'sortField'      => $this->sortField,
            'sortDirection'  => $this->sortDirection,
            'canCreate'      => $this->can('create'),
            'canEdit'        => $this->can('edit'),
            'canDelete'      => $this->can('delete'),
            'pageRoute'      => $this->pageRoute,
            'showModal'      => $this->showModal,
            'editingId'      => $this->editingId,
            'showDeleteModal'=> $this->showDeleteModal,
            'canRestore' => $this->can('restore'),
        ]);
    }
}
