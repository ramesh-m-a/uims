<?php

namespace App\Livewire\Master\Config\Academic\Subject;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Config\Academic\Subject;

class SubjectTable extends MasterTableBase
{
    use MasterFormBase;

    protected string $modelClass = Subject::class;

    public string $pageRoute = '';

    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();
    }

    protected function permissionPrefix(): string
    {
        return 'master.config.academic.subject';
    }

    protected function configKey(): string
    {
        return 'master.config.academic.subject';
    }

    protected function formDefaults(): array
    {
        return [
            'mas_subject_name'       => '',
            'mas_subject_short_code' => '',
            'mas_subject_status_id'  => 1,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_subject_name'       => 'required|string|max:255',
            'form.mas_subject_short_code' => 'required|string|max:50',
            'form.mas_subject_status_id'  => 'required|in:1,2',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.mas_subject_name'       => 'Subject Name',
            'form.mas_subject_short_code' => 'Subject Code',
            'form.mas_subject_status_id'  => 'Status',
        ];
    }

    public function render()
    {
        $query = $this->baseQuery()
            ->with(['stream', 'degree', 'department']);

        if ($this->can('restore')) {
            $query->withTrashed();
        }

        $rows = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->pageIds   = $rows->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.config.academic.subject.subject-table', [
            'subjects'       => $rows,
            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'columns'        => $this->columns(),
            'filters'        => $this->filters,
            'sortField'      => $this->sortField,
            'sortDirection'  => $this->sortDirection,
            'statusField'    => $this->statusField(),
            'pageRoute'      => $this->pageRoute,
            'breadcrumbMode' => $this->breadcrumbMode(),
            'showModal'      => $this->showModal,
            'editingId'      => $this->editingId,
            'showDeleteModal'=> $this->showDeleteModal,
            'hasSelection'   => count($this->selected) > 0,
            'selected'       => $this->selected,
            'canCreate'      => $this->can('create'),
            'canEdit'        => $this->can('edit'),
            'canDelete'      => $this->can('delete'),
            'canRestore'     => $this->can('restore'),
        ]);
    }
}
