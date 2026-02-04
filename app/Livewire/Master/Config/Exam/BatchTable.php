<?php

namespace App\Livewire\Master\Config\Exam;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;

use App\Models\Master\Config\Exam\Batch;
use App\Models\Master\Config\Academic\College;
use App\Models\Master\Common\Month;
use App\Models\Master\Common\Status;

use Illuminate\Support\Facades\DB;
use App\Services\Exam\BatchEngine;
use Illuminate\Support\Facades\Log;

class BatchTable extends MasterTableBase
{
    use MasterFormBase;

    protected $listeners = ['refresh-batch-table' => '$refresh'];

    protected string $modelClass = Batch::class;

    public string $pageRoute = '';

    public bool $showAllocationModal = false;
    public ?int $editingBatchId = null;

    public int $modalVersion = 0;

    public array $allocation = [];
    public array $originalAllocation = [];

    public int $newCollegeId = 0;
    public int $newCollegeCount = 0;

    public ?int $highlightCentreId = null;

    /** Colleges filtered by stream (for ADD dropdown) */
    public $allocationColleges;

    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();

        $this->sortField = 'centre.mas_college_name';
        $this->sortDirection = 'asc';
    }

    protected function permissionPrefix(): string
    {
        return 'master.config.exam.batch';
    }

    protected function configKey(): string
    {
        return 'master.config.exam.batch';
    }

    protected function formDefaults(): array
    {
        return [
            'mas_batch_month_id'   => null,
            'mas_batch_start_date' => null,
            'mas_batch_status_id'  => 1,
            'mas_batch_revised_scheme_id' => '',
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_batch_month_id'   => 'required',
            'form.mas_batch_start_date' => 'required|date',
            'form.mas_batch_status_id'  => 'required',
            'form.mas_batch_revised_scheme_id' => 'required|exists:mas_revised_scheme,id',
        ];
    }

    public function render()
    {
        $query = $this->baseQuery()
            ->with([
                'stream',
                'year',
                'month',
                'subject',
                'scheme',
                'centre',
                'centreAllocations.college'
            ]);

        $query = $this->applySorting($query);

        $rows = $query->paginate($this->perPage);

        return view('livewire.master.config.exam.batch-table', [
            'batches'  => $rows,
            'months'   => Month::orderBy('id')->get(),
            'statuses' => Status::active()->orderBy('mas_status_name')->get(),

            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'columns'        => $this->columns(),
            'filters'        => $this->filters,
            'sortField'      => $this->sortField,
            'sortDirection'  => $this->sortDirection,
            'statusField'    => $this->statusField(),
        ]);
    }

    // ======================================================
    // EDIT
    // ======================================================
    public function editRow(int $id)
    {
        Log::info('[BatchTable] editRow START', ['batch_id' => $id]);

        $this->reset([
            'allocation',
            'originalAllocation',
            'newCollegeId',
            'newCollegeCount',
            'highlightCentreId',
            'editingBatchId',
            'showAllocationModal',
        ]);

        $this->modalVersion++;

        $batch = Batch::findOrFail($id);
        $this->editingBatchId = $id;

        $this->form = [
            'mas_batch_month_id'   => $batch->mas_batch_month_id,
            'mas_batch_start_date' => $batch->mas_batch_start_date,
            'mas_batch_status_id'  => $batch->mas_batch_status_id,
            'mas_batch_revised_scheme_id' => $batch->mas_batch_revised_scheme_id,
        ];

        // Active attached colleges
        $rows = DB::table('mas_batch_centre_student as bcs')
            ->join('mas_batch_centre as bc', function ($join) {
                $join->on('bc.mas_batch_id', '=', 'bcs.mas_batch_id')
                    ->on('bc.mas_centre_id', '=', 'bcs.mas_centre_id');
            })
            ->where('bcs.mas_batch_id', $id)
            ->where('bc.status_id', 1)
            ->pluck('bcs.student_count', 'bcs.mas_centre_id')
            ->toArray();

        $this->allocation = $rows;
        $this->originalAllocation = $rows;

        // 🔥 FILTER colleges by stream (IMPORTANT)
        $this->allocationColleges = College::active()
            ->where('mas_college_stream_id', $batch->mas_batch_stream_id)
            ->orderBy('mas_college_name')
            ->get()
            ->map(function ($c) {
                $c->college_display_name =
                    "{$c->mas_college_code} - {$c->mas_college_name}";
                return $c;
            });

        Log::info('[BatchTable] editRow LOADED', ['allocation' => $rows]);

        $this->showAllocationModal = true;
    }

    // ======================================================
    // ADD COLLEGE
    // ======================================================
    public function addCollege()
    {
        if (! $this->newCollegeId || $this->newCollegeCount <= 0) {
            return;
        }

        if (isset($this->allocation[$this->newCollegeId])) {
            return;
        }

        $this->allocation[$this->newCollegeId] = (int) $this->newCollegeCount;
        $this->highlightCentreId = $this->newCollegeId;

        $this->newCollegeId = 0;
        $this->newCollegeCount = 0;
    }

    // ======================================================
    // REMOVE COLLEGE  ✅ THIS FIXES YOUR ERROR
    // ======================================================
    public function removeCollege(int $centreId): void
    {
        unset($this->allocation[$centreId]);
    }

    // ======================================================
    // SAVE
    // ======================================================
    public function saveAllocation()
    {
        Log::info('[BatchTable] saveAllocation START', [
            'batch_id' => $this->editingBatchId,
            'allocation' => $this->allocation,
        ]);

        $this->validate();

        DB::transaction(function () {

            Log::info('[BatchTable] saveAllocation TRANSACTION ENTER');

            $batch = Batch::findOrFail($this->editingBatchId);

            $batch->update([
                'mas_batch_month_id'          => $this->form['mas_batch_month_id'],
                'mas_batch_start_date'        => $this->form['mas_batch_start_date'],
                'mas_batch_status_id'         => $this->form['mas_batch_status_id'],
                'mas_batch_revised_scheme_id' => $this->form['mas_batch_revised_scheme_id'],
            ]);

            $existing = DB::table('mas_batch_centre')
                ->where('mas_batch_id', $batch->id)
                ->pluck('mas_centre_id')
                ->toArray();

            $current = array_keys($this->allocation);
            $toDeactivate = array_diff($existing, $current);

            if (! empty($toDeactivate)) {
                DB::table('mas_batch_centre')
                    ->where('mas_batch_id', $batch->id)
                    ->whereIn('mas_centre_id', $toDeactivate)
                    ->update([
                        'status_id'  => 2,
                        'updated_at' => now(),
                    ]);
            }

            foreach ($this->allocation as $centreId => $count) {

                DB::table('mas_batch_centre')->updateOrInsert(
                    [
                        'mas_batch_id'  => $batch->id,
                        'mas_centre_id' => $centreId,
                    ],
                    [
                        'status_id'  => 1,
                        'updated_at' => now(),
                    ]
                );

                DB::table('mas_batch_centre_student')->updateOrInsert(
                    [
                        'mas_batch_id'  => $batch->id,
                        'mas_centre_id' => $centreId,
                    ],
                    [
                        'student_count' => (int) $count,
                        'updated_at'    => now(),
                    ]
                );
            }

            Log::info('[BatchTable] saveAllocation TRANSACTION EXIT');
        });

        Log::info('[BatchTable] saveAllocation BEFORE REGENERATE');
        BatchEngine::regenerate(Batch::findOrFail($this->editingBatchId));
        Log::info('[BatchTable] saveAllocation AFTER REGENERATE');

        $this->reset([
            'allocation',
            'originalAllocation',
            'editingBatchId',
            'showAllocationModal',
            'newCollegeId',
            'newCollegeCount',
            'highlightCentreId',
        ]);

        $this->modalVersion++;

        $this->dispatch('toast', type: 'success', message: 'Batch updated successfully');
        $this->resetPage();
        $this->dispatch('$refresh');
    }

    // ======================================================
    // COMPUTED
    // ======================================================
    public function getComputedTotalProperty()
    {
        return collect($this->allocation)->map(fn ($v) => (int) $v)->sum();
    }

    protected function applySorting($query)
    {
        return match ($this->sortField) {

            // ✅ Exam Centre (College)
            'centre.mas_college_name' => $query
                ->leftJoin(
                    'mas_college as centres',
                    'centres.id',
                    '=',
                    'mas_batch.mas_batch_centre_id'
                )
                ->leftJoin(
                    'mas_subject as subjects',
                    'subjects.id',
                    '=',
                    'mas_batch.mas_batch_subject_id'
                )
                ->orderBy('centres.mas_college_name', $this->sortDirection)
                ->orderBy('subjects.mas_subject_name', 'asc')
                ->orderBy('mas_batch.mas_batch_start_date', 'asc')
                ->select('mas_batch.*'),

            // ✅ Subject
            'subject.mas_subject_name' => $query
                ->leftJoin(
                    'mas_subject as subjects',
                    'subjects.id',
                    '=',
                    'mas_batch.mas_batch_subject_id'
                )
                ->orderBy('subjects.mas_subject_name', $this->sortDirection)
                ->orderBy('mas_batch.mas_batch_start_date', 'asc')
                ->select('mas_batch.*'),

            // ✅ Start Date
            'mas_batch_start_date' => $query
                ->orderBy('mas_batch.mas_batch_start_date', $this->sortDirection),

            // ✅ Fallback
            default => $query
                ->orderBy($this->sortField ?? 'mas_batch.id', $this->sortDirection),
        };
    }

}
