<?php

namespace App\Livewire\Master\Common\Ifsc;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Common\Bank;
use App\Models\Master\Common\BankBranch;
use App\Models\Master\Common\Ifsc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IfscTable extends MasterTableBase
{
    use MasterFormBase;

    /* ==========================
     | MODEL
     ========================== */
    protected string $modelClass = Ifsc::class;

    /* ==========================
     | PAGE / UI STATE
     ========================== */
    public string $pageRoute = '';

    protected array $branches = [];

    /* ==========================
     | DELETE STATE (MATCH GENDER)
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
        return 'master.common.ifsc';
    }

    protected function configKey(): string
    {
        return 'master.common.ifsc';
    }

    /* ==========================
     | FORM DEFAULTS
     ========================== */
    protected function formDefaults(): array
    {
        return [
            'mas_ifsccode_number'     => '',
            'mas_ifsccode_bank_id'   => '',
            'branch_id'              => '',
            'mas_ifsccode_status_id' => 1,
        ];
    }

    /* ==========================
     | VALIDATION
     ========================== */
    protected function rules(): array
    {
        return [
            'form.mas_ifsccode_number' =>
                'required|string|max:20|unique:mas_ifsccode,mas_ifsccode_number,' . $this->editingId,

            'form.mas_ifsccode_bank_id' =>
                'required|exists:mas_bank,id',

            'form.branch_id' =>
                'required|exists:mas_bank_branch,id',

            'form.mas_ifsccode_status_id' =>
                'required|in:1,2',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.mas_ifsccode_number'     => 'IFSC Code',
            'form.mas_ifsccode_bank_id'   => 'Bank',
            'form.branch_id'              => 'Branch',
            'form.mas_ifsccode_status_id' => 'Status',
        ];
    }

    /* ==========================
     | RENDER (MATCH GENDER)
     ========================== */
    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['bank', 'branch'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Selection sync (same as Gender)
        $this->pageIds   = $rows->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.common.ifsc.ifsc-table', [
            // data
            'rows'     => $rows,
            'banks'    => Bank::orderBy('mas_bank_name')->get(),
            'branches' => $this->branches,

            // table contract
            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'columns'        => $this->columns(),
            'filters'        => $this->filters,
            'sortField'      => $this->sortField,
            'sortDirection'  => $this->sortDirection,
            'statusField'    => $this->statusField(),

            // UI state (🔥 THIS FIXES LIFECYCLE)
            'pageRoute'       => $this->pageRoute,
            'breadcrumbMode'  => $this->breadcrumbMode(),
            'showModal'       => $this->showModal,
            'editingId'       => $this->editingId,
            'showDeleteModal' => $this->showDeleteModal,

            // bulk state
            'hasSelection' => count($this->selected) > 0,
            'selected'     => $this->selected,

            // permissions (🔥 THIS FIXES ADD BUTTON)
            'canCreate'  => $this->can('create'),
            'canEdit'    => $this->can('edit'),
            'canDelete'  => $this->can('delete'),
            'canRestore' => $this->can('restore'),
            'branchesExist'    => $this->branchesExist(),
        ]);
    }

    /* =====================================================
     | CASCADE: BANK → BRANCH
     ===================================================== */
    public function updatedFormMasIfsccodeBankId($bankId): void
    {
        $this->form['branch_id'] = null;
        $this->loadBranchesForBank($bankId);
    }

    protected function loadBranchesForBank(?int $bankId): void
    {
        $this->branches = [];

        if (!$bankId) {
            return;
        }

        if (!Schema::hasTable('mas_bank_branch')) {
            return;
        }

        $this->branches = BankBranch::query()
            ->where('bank_id', $bankId)
            ->orderBy('branch_name')
            ->get(['id', 'branch_name'])
            ->toArray();
    }

    /* =====================================================
     | CREATE / EDIT HOOKS
     ===================================================== */
    protected function afterCreate(): void
    {
        $this->loadBranchesForBank(
            $this->form['mas_ifsccode_bank_id'] ?? null
        );
    }

    protected function editFormFill($model): void
    {
        parent::editFormFill($model);

        $this->loadBranchesForBank(
            $this->form['mas_ifsccode_bank_id'] ?? null
        );
    }

    /* ==========================
     | SINGLE DELETE (MATCH GENDER)
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

        IfscCode::findOrFail($this->deleteId)->delete();

        $this->js(
            "Alpine.store('toast').push('success','IFSC deleted successfully !')"
        );

        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->resetPage();
    }

    protected function branchesExist(): bool
    {
        // Fast existence check (no heavy query)
        return DB::table('mas_bank_branch')->exists();
    }
}
