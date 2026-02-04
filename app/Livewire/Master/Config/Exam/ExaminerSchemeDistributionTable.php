<?php

namespace App\Livewire\Master\Config\Exam;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Config\Exam\ExaminerSchemeDistribution;

class ExaminerSchemeDistributionTable extends MasterTableBase
{
    use MasterFormBase;

    protected string $modelClass = ExaminerSchemeDistribution::class;

    public string $pageRoute = '';

    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    public function mount(): void
    {
       // dd('MOUNT HIT');
        $this->pageRoute = request()->route()->getName();
    }

    protected function permissionPrefix(): string
    {
        return 'master.config.exam.examiner-scheme-distribution';
    }

    protected function configKey(): string
    {
        return 'master.config.exam.examiner-scheme-distribution';
    }

    protected function formDefaults(): array
    {
        return [
            'mas_examiner_scheme_distribution_scheme_id' => null,
            'mas_examiner_scheme_distribution_examiner_type_id' => null,
            'mas_examiner_scheme_distribution_examiner_type_count' => 1,
            'mas_examiner_scheme_distribution_status_id' => 1,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_examiner_scheme_distribution_scheme_id' => 'required|integer',
            'form.mas_examiner_scheme_distribution_examiner_type_id' => 'required|integer',
            'form.mas_examiner_scheme_distribution_examiner_type_count' => 'required|integer|min:1',
            'form.mas_examiner_scheme_distribution_status_id' => 'required|in:1,2',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.mas_examiner_scheme_distribution_scheme_id' => 'Scheme',
            'form.mas_examiner_scheme_distribution_examiner_type_id' => 'Examiner Type',
            'form.mas_examiner_scheme_distribution_examiner_type_count' => 'Count',
            'form.mas_examiner_scheme_distribution_status_id' => 'Status',
        ];
    }

    public function render()
    {
       /* try {

            $rows = $this->baseQuery()
                ->with('streams')
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);

        } catch (\Throwable $e) {

            dd(
                'REAL ERROR FOUND',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }*/
        $rows = $this->baseQuery()
            ->with(['streams','scheme'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
// dd($rows);
        $this->pageIds = $rows->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.config.exam.examiner-scheme-distribution-table', [
            'streams' => $rows,

            // table contract
            'search' => $this->search,
            'perPage' => $this->perPage,
            'columns' => $this->columns(),
            'filters' => $this->filters,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'statusField' => $this->statusField(),

            // UI state
            'pageRoute' => $this->pageRoute,
            'breadcrumbMode' => $this->breadcrumbMode(),
            'showModal' => $this->showModal,
            'editingId' => $this->editingId,
            'showDeleteModal' => $this->showDeleteModal,

            // bulk state
            'hasSelection' => count($this->selected) > 0,
            'selected' => $this->selected,

            // permissions
            'canCreate' => true,
            'canEdit' => true,
            'canDelete' => true,
            'canRestore' => true,
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

       ExaminerSchemeDistribution::findOrFail($this->deleteId)->delete();

        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->resetPage();
    }

    public function deleteSelected(): void
    {
        abort_unless($this->can('delete'), 403);

        if (empty($this->selected)) return;

        ExaminerSchemeDistribution::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->selectAll = false;

        $this->resetPage();
    }

    public function restoreRow(int $id): void
    {
        abort_unless($this->can('restore'), 403);

        ExaminerSchemeDistribution::withTrashed()->findOrFail($id)->restore();

        $this->js(
            "Alpine.store('toast').push('success','Record restored successfully')"
        );

        $this->resetPage();
    }
}
