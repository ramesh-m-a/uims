<?php

namespace App\Livewire\Master\Config\Academic\Designation;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Config\Academic\Designation;
use App\Models\Master\Config\Academic\Stream;

class DesignationTable extends MasterTableBase
{
    use MasterFormBase;

    protected string $modelClass = Designation::class;

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
        return 'master.config.academic.designation';
    }

    protected function configKey(): string
    {
        return 'master.config.academic.designation';
    }

    protected function formDefaults(): array
    {
        return [
            'mas_designation_name'       => '',
            'mas_designation_status_id' => 1,
            'streams' => [],
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_designation_name'       => 'required|string|max:150',
            'form.mas_designation_status_id' => 'required|integer',
            'form.streams'                   => 'required|array|min:1',
            'form.streams.*' => 'exists:mas_stream,id',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.mas_designation_name'       => 'Designation',
            'form.mas_designation_status_id' => 'Status',
            'form.streams'                   => 'Streams',
        ];
    }

    protected function afterSave(Designation $designation): void
    {
        $designation->streams()->sync($this->form['streams'] ?? []);
    }

    protected function editFormFill(Designation $designation): void
    {
        $this->form['streams'] = $designation->streams
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['streams'])   // THIS IS THE FIX (singular, matches config)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

       // dd($rows);

        $this->pageIds   = $rows->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.config.academic.designation.designation-table', [
            'designations' => $rows,
            'streams'      => Stream::orderBy('mas_stream_name')->get(),

            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'columns'        => $this->columns(),
            'filters'        => $this->filters,
            'sortField'      => $this->sortField,
            'sortDirection'  => $this->sortDirection,
            'statusField'    => $this->statusField(),

            'pageRoute'       => $this->pageRoute,
            'breadcrumbMode'  => $this->breadcrumbMode(),
            'showModal'       => $this->showModal,
            'editingId'       => $this->editingId,
            'showDeleteModal' => $this->showDeleteModal,

            'hasSelection' => count($this->selected) > 0,
            'selected'     => $this->selected,

            'canCreate'  => $this->can('create'),
            'canEdit'    => $this->can('edit'),
            'canDelete'  => $this->can('delete'),
            'canRestore' => $this->can('restore'),
        ]);
    }

    public function confirmDelete(int $id): void
    {
        abort_unless($this->can('delete'), 403);

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        abort_unless($this->can('delete'), 403);

        Designation::findOrFail($this->deleteId)->delete();

        $this->js("Alpine.store('toast').push('success','Designation deleted successfully')");

        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->resetPage();
    }

    public function deleteSelected(): void
    {
        abort_unless($this->can('delete'), 403);

        if (empty($this->selected)) return;

        Designation::whereIn('id', $this->selected)->delete();

        $this->js("Alpine.store('toast').push('success','Selected records deleted successfully')");

        $this->selected  = [];
        $this->selectAll = false;

        $this->resetPage();
    }

    public function restoreRow(int $id): void
    {
        abort_unless($this->can('restore'), 403);

        Designation::withTrashed()->findOrFail($id)->restore();

        $this->js("Alpine.store('toast').push('success','Record restored successfully')");

        $this->resetPage();
    }
}
