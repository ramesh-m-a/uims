<?php

namespace App\Livewire\Master\Common\Taluk;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Common\Taluk;

class TalukTable extends MasterTableBase
{
    use MasterFormBase;

    /* ==========================
     | MODEL
     ========================== */
    protected string $modelClass = Taluk::class;

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
        return 'master.common.taluk';
    }

    protected function configKey(): string
    {
        return 'master.common.taluk';
    }

    /* ==========================
     | FORM CONFIG
     ========================== */
    protected function formDefaults(): array
    {
        return [
            'mas_taluk_name'       => '',
            'mas_taluk_status_id'  => 1,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_taluk_name'       => 'required|string|max:255',
            'form.mas_taluk_status_id'  => 'required|in:1,2',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.mas_taluk_name'       => 'Taluk Name',
            'form.mas_taluk_status_id'  => 'Status',
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

        return view('livewire.master.common.taluk.taluk-table', [
            // data
            'taluks' => $rows,

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

        Taluk::findOrFail($this->deleteId)->delete();

        $this->js(
            "Alpine.store('toast').push('success','Taluk deleted successfully !')"
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

        Taluk::whereIn('id', $this->selected)->delete();

        $this->js(
            "Alpine.store('toast').push('success','Selected Taluk deleted successfully !')"
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

        Taluk::withTrashed()->findOrFail($id)->restore();

        $this->js(
            "Alpine.store('toast').push('success','Taluk restored successfully !')"
        );

        $this->resetPage();
    }
}
