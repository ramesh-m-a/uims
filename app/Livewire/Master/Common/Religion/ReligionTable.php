<?php

namespace App\Livewire\Master\Common\Religion;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Common\Religion;

class ReligionTable extends MasterTableBase
{
    use MasterFormBase;

    /* ==========================
     | MODEL
     ========================== */
    protected string $modelClass = Religion::class;

    /* ==========================
     | PAGE / UI STATE
     ========================== */
    public string $pageRoute = '';

    /* ==========================
     | DELETE STATE
     ========================== */
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    /* ==========================
     | MOUNT
     ========================== */
    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();
    }

    /* ==========================
     | REQUIRED BY MASTER BASE
     ========================== */
    protected function permissionPrefix(): string
    {
        return 'master.common.religion';
    }

    protected function configKey(): string
    {
        return 'master.common.religion';
    }

    /* ==========================
     | FORM CONFIG
     ========================== */
    protected function formDefaults(): array
    {
        return [
            'mas_religion_name'       => '',
            'mas_religion_status_id'  => 1,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_religion_name'       => 'required|string|max:255',
            'form.mas_religion_status_id'  => 'required|in:1,2',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.mas_religion_name'       => 'Religion Name',
            'form.mas_religion_status_id'  => 'Status',
        ];
    }

    /* ==========================
     | RENDER
     ========================== */
    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Selection sync
        $this->pageIds   = $rows->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.common.religion.religion-table', [
            // data
            'religions' => $rows,

            // table contract
            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'columns'        => $this->columns(),
            'filters'        => $this->filters,
            'sortField'     => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'statusField'   => $this->statusField(),

            // UI state
            'pageRoute'      => $this->pageRoute,
            'breadcrumbMode' => $this->breadcrumbMode(),
            'showModal'      => $this->showModal,
            'editingId'      => $this->editingId,
            'showDeleteModal'=> $this->showDeleteModal,

            // bulk state
            'hasSelection'   => count($this->selected) > 0,
            'selected'       => $this->selected,

            // permissions
            'canCreate'  => $this->can('create'),
            'canEdit'    => $this->can('edit'),
            'canDelete'  => $this->can('delete'),
            'canRestore' => $this->can('restore'),
        ]);
    }

    /* ==========================
     | SINGLE DELETE
     ========================== */
    public function confirmDelete(int $id): void
    {
        abort_unless($this->can('delete'), 403);

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        abort_unless($this->can('delete'), 403);

        Religion::findOrFail($this->deleteId)->delete();

        $this->js(
            "Alpine.store('toast').push('success','Religion deleted successfully !')"
        );

        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->resetPage();
    }

    /* ==========================
     | BULK DELETE
     ========================== */
    public function deleteSelected(): void
    {
        abort_unless($this->can('delete'), 403);

        if (empty($this->selected)) {
            return;
        }

        Religion::whereIn('id', $this->selected)->delete();

        $this->js(
            "Alpine.store('toast').push('success','Selected Religion deleted successfully !')"
        );

        $this->selected  = [];
        $this->selectAll = false;

        $this->resetPage();
    }

    /* ==========================
     | RESTORE
     ========================== */
    public function restoreRow(int $id): void
    {
        abort_unless($this->can('restore'), 403);

        Religion::withTrashed()->findOrFail($id)->restore();

        $this->js(
            "Alpine.store('toast').push('success','Religion restored successfully !')"
        );

        $this->resetPage();
    }
}
