<?php

namespace App\Livewire\Master\Common\BankBranch;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Common\Bank;
use App\Models\Master\Common\BankBranch;
use Illuminate\Support\Facades\DB;

class BankBranchTable extends MasterTableBase
{
    use MasterFormBase;

    /* ==========================
     | MODEL
     ========================== */
    protected string $modelClass = BankBranch::class;

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
     | RBAC
     ========================== */
    protected function permissionPrefix(): string
    {
        return 'master.common.bank-branch';
    }

    protected function configKey(): string
    {
        return 'master.common.bank-branch';
    }

    /* ==========================
     | FORM DEFAULTS
     ========================== */
    protected function formDefaults(): array
    {
        return [
            'bank_id'        => '',
            'branch_name'    => '',
            'branch_city'    => '',
            'branch_state'   => '',
            'status_id'      => 1,
        ];
    }

    /* ==========================
     | VALIDATION
     ========================== */
    protected function rules(): array
    {
        return [
            'form.bank_id'     => 'required|exists:mas_bank,id',
            'form.branch_name' => 'required|string|max:150',
            'form.branch_city' => 'nullable|string|max:100',
            'form.branch_state'=> 'nullable|string|max:100',
            'form.status_id'   => 'required|in:1,2',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.bank_id'     => 'Bank',
            'form.branch_name' => 'Branch Name',
            'form.branch_city' => 'City',
            'form.branch_state'=> 'State',
            'form.status_id'   => 'Status',
        ];
    }

    /* ==========================
     | BANK-EXISTS UX GUARD
     ========================== */
    protected function banksExist(): bool
    {
        return DB::table('mas_bank')->exists();
    }

    /* ==========================
     | RENDER (GENDER-CLONE)
     ========================== */
    public function render()
    {
        $rows = $this->baseQuery()
            ->with('bank')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->pageIds   = $rows->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.common.bank-branch.bank-branch-table', [
            // data
            'rows'  => $rows,
            'banks' => Bank::orderBy('mas_bank_name')->get(),

            // table contract
            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'columns'        => $this->columns(),
            'filters'        => $this->filters,
            'sortField'      => $this->sortField,
            'sortDirection'  => $this->sortDirection,
            'statusField'    => $this->statusField(),

            // UI state
            'pageRoute'       => $this->pageRoute,
            'breadcrumbMode'  => $this->breadcrumbMode(),
            'showModal'       => $this->showModal,
            'editingId'       => $this->editingId,
            'showDeleteModal' => $this->showDeleteModal,

            // bulk
            'hasSelection' => count($this->selected) > 0,
            'selected'     => $this->selected,

            // permissions + UX guard
            'canCreate'     => $this->can('create') && $this->banksExist(),
            'canEdit'       => $this->can('edit'),
            'canDelete'     => $this->can('delete'),
            'canRestore'    => $this->can('restore'),
            'banksExist'    => $this->banksExist(),
        ]);
    }

    /* ==========================
     | DELETE
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

        BankBranch::findOrFail($this->deleteId)->delete();

        $this->js(
            "Alpine.store('toast').push('success','Bank branch deleted successfully!')"
        );

        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->resetPage();
    }
}
