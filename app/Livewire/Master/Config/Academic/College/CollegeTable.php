<?php

namespace App\Livewire\Master\Config\Academic\College;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Config\Academic\College;
use App\Models\Master\Config\Academic\Stream;

class CollegeTable extends MasterTableBase
{
    use MasterFormBase;

    /* ==========================
 | MODEL
 ========================== */

    protected string $modelClass = College::class;

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
        return 'master.config.academic.college';
    }

    protected function configKey(): string
    {
        return 'master.config.academic.college';
    }

    /* ==========================
   | FORM CONFIG
   ========================== */
    protected function formDefaults(): array
    {
        return [
            'mas_college_stream_id'   => null,
            'mas_college_code'        => '',
            'mas_college_name'        => '',
            'mas_college_exam_centre' => null,
            'mas_college_is_internal' => null,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_college_stream_id'   => 'required|exists:mas_stream,id',
            'form.mas_college_code'        => 'required|string|max:10',
            'form.mas_college_name'        => 'required|string|max:255',
            'form.mas_college_exam_centre' => 'nullable|boolean',
            'form.mas_college_is_internal' => 'nullable|boolean',
        ];
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->pageIds = $rows->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.config.academic.college.college-table', [
            // data
            'colleges' => $rows,
            'streams' => Stream::orderBy('mas_stream_name')->get(),

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

    protected function validationAttributes(): array
    {
        return [
            'form.mas_college_stream_id'   => 'Stream',
            'form.mas_college_name'       => 'College Name',
            'form.mas_college_code' => 'College Code',
            'form.mas_college_status_id'  => 'Status',
        ];
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

        College::findOrFail($this->deleteId)->delete();

        $this->js(
            "Alpine.store('toast').push('success','Stream deleted successfully')"
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

        College::whereIn('id', $this->selected)->delete();

        $this->js(
            "Alpine.store('toast').push('success','Selected records deleted successfully')"
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

        College::withTrashed()->findOrFail($id)->restore();

        $this->js(
            "Alpine.store('toast').push('success','Record restored successfully')"
        );

        $this->resetPage();
    }
}
