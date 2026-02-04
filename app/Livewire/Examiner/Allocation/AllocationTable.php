<?php

namespace App\Livewire\Examiner\Allocation;

use App\Livewire\Concerns\NormalizesDates;
use App\Services\ExaminerAllocation\AllocationContext;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class AllocationTable extends Component
{
    use WithPagination;
    use NormalizesDates;

    protected string $paginationTheme = 'bootstrap';

    public ?int $yearId = null;
    public ?int $monthId = null;
    public ?int $schemeId = null;
    public ?int $degreeId = null;
    public int $userId;

    public ?string $centre = null;
    public ?string $batch = null;
    public ?string $subject = null;
    public ?string $examiner = null;
    public ?string $status = null;
    public ?string $date = null;
    public string $search = '';

    public bool $showDrawer = false;
    public array $selectedRow = [];
    public string $pickerSearch = '';

    public bool $showAddModal = false;
    public string $newName = '';
    public string $newMobile = '';
    public string $newEmail = '';

    public bool $showDateDrawer = false;

    public string $current_from_date = '';
    public string $current_to_date = '';
    public string $new_from_date = '';
    public string $new_to_date = '';

    public bool $showAddDrawer = false;

    public ?int $add_examiner_id = null;
    public ?int $add_session_id = null;

    public array $centreExaminers = [];

    public bool $isAddingAdditional = false;
    public bool $isContactRGUHS = false;

    protected $queryString = [
        'yearId'   => ['except' => ''],
        'monthId'  => ['except' => ''],
        'schemeId' => ['except' => ''],
        'degreeId' => ['except' => ''],
        'streamId' => ['except' => ''],
    ];

    public function mountrefreshissue(
        \App\Services\ExaminerAllocation\AllocationEngine $engine,
        \App\Repositories\TempAllocationRepository $tempRepo
    ): void
    {
        $this->userId = auth()->id() ?? 1;

        $this->yearId   = session('allocation.yearId');
        $this->monthId  = session('allocation.monthId');
        $this->schemeId = session('allocation.schemeId');
        $this->degreeId = session('allocation.degreeId');

        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            abort(403, 'Invalid allocation session');
        }

        $context = new AllocationContext(
            yearId: $this->yearId,
            monthId: $this->monthId,
            schemeId: $this->schemeId,
            degreeId: $this->degreeId,
            userId: $this->userId,
            streamId: 6,
        );

        if (!$tempRepo->existsForContext($context)) {
            $result = $engine->build($context);

            if ($result->rows->isNotEmpty()) {
                $tempRepo->store($context, $result->rows);
            }
        }
    }

    public function mountduplicates(
        \App\Services\ExaminerAllocation\AllocationEngine $engine,
        \App\Repositories\TempAllocationRepository $tempRepo,
        \App\Repositories\BatchRepository $batchRepo   // 🔥 NEW
    ): void
    {
        $this->userId = auth()->id() ?? 1;

        $this->yearId   = session('allocation.yearId');
        $this->monthId  = session('allocation.monthId');
        $this->schemeId = session('allocation.schemeId');
        $this->degreeId = session('allocation.degreeId');

        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            abort(403, 'Invalid allocation session');
        }

        $context = new AllocationContext(
            yearId: $this->yearId,
            monthId: $this->monthId,
            schemeId: $this->schemeId,
            degreeId: $this->degreeId,
            userId: $this->userId,
            streamId: 6,
        );

        /**
         * 🔥 CRITICAL REGRESSION FIX
         *
         * Rebuild temp if:
         * 1️⃣ No temp rows
         * 2️⃣ OR batch date changed in source
         */

        $mustRebuild = false;

        if (!$tempRepo->existsForContext($context)) {
            $mustRebuild = true;
        } else {

            // Compare source batch dates vs temp dates
            $sourceDates = collect(
                $batchRepo->rangesForScope(
                    $this->yearId,
                    $this->monthId,
                    $this->schemeId,
                    $this->degreeId,
                    6
                )
            )->pluck('from_date')->unique()->sort()->values();

            $tempDates = DB::table('temp_examiner_assigned_details')
                ->where('user_id', $this->userId)
                ->where('year_id', $this->yearId)
                ->where('month_id', $this->monthId)
                ->where('scheme_id', $this->schemeId)
                ->where('degree_id', $this->degreeId)
                ->pluck('from_date')
                ->unique()
                ->sort()
                ->values();

            if ($sourceDates != $tempDates) {
                $mustRebuild = true;
            }
        }

        if ($mustRebuild) {

            Log::info('Allocation Temp Rebuild Triggered');

            DB::table('temp_examiner_assigned_details')
                ->where('user_id', $this->userId)
                ->where('year_id', $this->yearId)
                ->where('month_id', $this->monthId)
                ->where('scheme_id', $this->schemeId)
                ->where('degree_id', $this->degreeId)
                ->delete();

            $result = $engine->build($context);

            if ($result->rows->isNotEmpty()) {
                $tempRepo->store($context, $result->rows);
            }
        }
    }

    public function mount(
        \App\Services\ExaminerAllocation\AllocationEngine $engine,
        \App\Repositories\TempAllocationRepository $tempRepo
    ): void
    {
        $this->userId = auth()->id() ?? 1;

        $this->yearId   = session('allocation.yearId');
        $this->monthId  = session('allocation.monthId');
        $this->schemeId = session('allocation.schemeId');
        $this->degreeId = session('allocation.degreeId');

        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            abort(403, 'Invalid allocation session');
        }

        $context = new AllocationContext(
            yearId: $this->yearId,
            monthId: $this->monthId,
            schemeId: $this->schemeId,
            degreeId: $this->degreeId,
            userId: $this->userId,
            streamId: 6,
        );

        /**
         * ✅ IF TEMP NOT EXISTS → BUILD (OLD BEHAVIOUR)
         */
        if (!$tempRepo->existsForContext($context)) {

            $result = $engine->build($context);

            if ($result->rows->isNotEmpty()) {
                $tempRepo->store($context, $result->rows);
            }

        } else {
            // 🔥 NEW: Always sync dates from batch master
            $tempRepo->syncDatesFromBatchRange($context);
        }

        /**
         * 🔥 REGRESSION FIX (FINAL)
         * SYNC DATES FROM mas_batch_range → TEMP TABLE
         * NO REBUILD
         * NO INSERT
         * ONLY UPDATE EXISTING ROWS
         */

        DB::statement("
        UPDATE temp_examiner_assigned_details t
        JOIN mas_batch_range br
            ON br.id = t.batch_range_id
        SET
            t.from_date = br.mas_batch_range_from_date,
            t.to_date   = br.mas_batch_range_from_date,
            t.updated_at = NOW()
        WHERE
            t.user_id = ?
            AND t.year_id = ?
            AND t.month_id = ?
            AND t.scheme_id = ?
            AND t.degree_id = ?
    ", [
            $this->userId,
            $this->yearId,
            $this->monthId,
            $this->schemeId,
            $this->degreeId
        ]);
    }

    protected function query()
    {
        return DB::table('temp_examiner_assigned_details')
            ->where('user_id', $this->userId)
            ->where('year_id', $this->yearId)
            ->where('month_id', $this->monthId)
            ->where('scheme_id', $this->schemeId)
            ->where('degree_id', $this->degreeId);
    }

    /**
     * Paginated rows → used by RIGHT panel table
     */
    public function getRowsProperty()
    {
        $q = $this->query();

        if ($this->centre) $q->where('centre_name', 'like', "%{$this->centre}%");
        if ($this->batch) $q->where('batch_name', 'like', "%{$this->batch}%");
        if ($this->subject) $q->where('subject_name', 'like', "%{$this->subject}%");
        if ($this->examiner) $q->where('examiner_name', 'like', "%{$this->examiner}%");
        if ($this->status) $q->where('status_label', $this->status);
        if ($this->date) $q->where('from_date', $this->date);

        if ($this->search) {
            $q->where(function ($sub) {
                $sub->where('centre_name', 'like', "%{$this->search}%")
                    ->orWhere('batch_name', 'like', "%{$this->search}%")
                    ->orWhere('subject_name', 'like', "%{$this->search}%")
                    ->orWhere('examiner_name', 'like', "%{$this->search}%");
            });
        }

        return $q
            ->orderBy('centre_name')
            ->orderBy('batch_name')
            ->orderBy('from_date')
            ->orderByRaw("FIELD(examiner_type,'Internal-C','Internal-A','External-O','External')")
            ->get();
    }


    /**
     * FULL dataset → used ONLY for LEFT centre list
     * This is the critical fix for missing centres
     */
    public function getAllRowsProperty()
    {
        return $this->query()
            ->orderBy('centre_name')
            ->orderBy('batch_name')
            ->orderBy('from_date')
            ->orderByRaw("FIELD(examiner_type,'Internal-C','Internal-A','External-O','External')")
            ->get();
    }

    /* =========================
     | DRAWER
     ========================= */
    public function openDrawer($row)
    {
        $this->selectedRow = $row;
        $this->pickerSearch = '';
        $this->showDrawer = true;
        $this->isContactRGUHS = false;
    }

    public function openContactRGUHSDrawer($row)
    {
        $this->selectedRow = $row;
        $this->pickerSearch = '';
        $this->showDrawer = true;
        $this->isContactRGUHS = true;
    }

    public function closeDrawer()
    {
        $this->showDrawer = false;
        $this->selectedRow = [];
    }

    /* =========================
     | PICKER
     ========================= */
    public function getPickerResultsProperty()
    {
        if (!$this->showDrawer || empty($this->selectedRow)) {
            return collect();
        }

        $type      = $this->selectedRow['examiner_type'] ?? null;
        $subjectId = $this->selectedRow['subject_id'] ?? null;
        $centreId  = (int) ($this->selectedRow['centre_id'] ?? 0);
        $batchId   = (int) ($this->selectedRow['batch_id'] ?? 0);
        $batchRangeId = (int) ($this->selectedRow['batch_range_id'] ?? 0);

        if (!$type || !$subjectId || !$centreId || !$batchId) {
            return collect();
        }

        $departmentId = DB::table('mas_subject')
            ->where('id', $subjectId)
            ->value('mas_subject_department_id');

        $attached = DB::table('mas_batch_centre')
            ->where('mas_batch_id', $batchId)
            ->where('is_attached', 1)
            ->pluck('mas_centre_id')
            ->map(fn($v) => (int) $v)
            ->values()
            ->all();

        if (empty($attached)) {
            $attached = [$centreId];
        }

        $q = DB::table('examiner_details as ed')
            ->join('basic_details as bd', 'bd.id', '=', 'ed.examiner_details_basic_details_id')
            ->join('users as u', 'u.id', '=', 'bd.basic_details_user_id')
            ->leftJoin('mas_college as uc', 'uc.id', '=', 'u.user_college_id')
            ->leftJoin('mas_college as ucc', 'ucc.id', '=', 'uc.mas_college_canonical_college_id')
            ->where('bd.basic_details_department_id', $departmentId);

        $effectiveCollege = DB::raw('COALESCE(ucc.id, uc.id)');

        if ($type === 'Internal-C') {
            $q->where('ed.examiner_details_type', 1)
                ->where($effectiveCollege, '=', $centreId);
        } elseif ($type === 'Internal-A') {
            $realAttached = array_values(array_diff($attached, [$centreId]));
            $allAllowed = array_unique(array_merge([$centreId], $realAttached));

            $q->where('ed.examiner_details_type', 1)
                ->whereIn($effectiveCollege, $allAllowed);
        } elseif ($type === 'External') {
            $q->where('ed.examiner_details_type', 2)
                ->where($effectiveCollege, '!=', $centreId);
        } elseif ($type === 'External-O') {
            $q->where('ed.examiner_details_type', 1)
                ->whereNotIn($effectiveCollege, array_unique(array_merge([$centreId], $attached)));
        }

        if ($this->pickerSearch) {
            $q->where(function ($sub) {
                $sub->where('u.name', 'like', "%{$this->pickerSearch}%")
                    ->orWhere('u.mobile', 'like', "%{$this->pickerSearch}%");
            });
        }

        // 🔒 HARD FIX: prevent same examiner appearing again in same batch_range
        if ($batchRangeId > 0) {
            $alreadyUsed = DB::table('temp_examiner_assigned_details')
                ->where('user_id', $this->userId)
                ->where('year_id', $this->yearId)
                ->where('month_id', $this->monthId)
                ->where('scheme_id', $this->schemeId)
                ->where('degree_id', $this->degreeId)
                ->where('batch_range_id', $batchRangeId)
                ->pluck('examiner_id')
                ->filter()
                ->values()
                ->all();

            if (!empty($alreadyUsed)) {
                $q->whereNotIn('ed.id', $alreadyUsed);
            }
        }

        return $q
            ->select(['ed.id', 'u.name', 'u.mobile'])
            ->orderBy('u.name')
            ->paginate(10);
    }

    public function assignExaminer(int $examinerId)
    {
        if (empty($this->selectedRow)) return;

        $examiner = DB::table('examiner_details as ed')
            ->join('basic_details as bd', 'bd.id', '=', 'ed.examiner_details_basic_details_id')
            ->join('users as u', 'u.id', '=', 'bd.basic_details_user_id')
            ->where('ed.id', $examinerId)
            ->select(['ed.id', 'u.name', 'u.mobile'])
            ->first();

        if (!$examiner) return;

        DB::table('temp_examiner_assigned_details')
            ->where('user_id', $this->userId)
            ->where('year_id', $this->yearId)
            ->where('month_id', $this->monthId)
            ->where('scheme_id', $this->schemeId)
            ->where('degree_id', $this->degreeId)
            ->where('centre_name', $this->selectedRow['centre_name'])
            ->where('subject_name', $this->selectedRow['subject_name'])
            ->where('examiner_type', $this->selectedRow['examiner_type'])
            ->update([
                'examiner_id'   => $examiner->id,
                'examiner_name' => $examiner->name,
                'mobile'        => $examiner->mobile,
                'status'       => $this->isContactRGUHS ? 38 : DB::raw('status'),
                'status_label' => $this->isContactRGUHS ? 'Contact RGUHS' : DB::raw('status_label'),
                'updated_at'    => now(),
            ]);

        $this->closeDrawer();
        $this->isAddingAdditional = false;
        $this->isContactRGUHS = false;

        $this->dispatch('toast', message: 'Examiner Swapped successfully');
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.examiner.allocation.allocation-table', [
            'rows'         => $this->rows,
            'allRows'      => $this->allRows,
            'pickerResults'=> $this->pickerResults,
        ]);
    }

    public function openChangeDateDrawer(array $row): void
    {
        $this->selectedRow = $row;

        // MUST be Y-m-d for HTML date input
        $this->current_from_date = $row['from_date'] ?? null;
        $this->current_to_date   = $row['to_date'] ?? null;

        $this->new_from_date = $this->current_from_date;
        $this->new_to_date   = $this->current_to_date;

        $this->showDateDrawer = true;

      //  dd('openChangeDateDrawer', $this->selectedRow);
    }

    public function saveDateChange(): void
    {
        if (empty($this->selectedRow)) {
            return;
        }

       /* dd([
            'saveDateChange', $this->selectedRow,
            'selectedRow_from_date' => $this->selectedRow['from_date'] ?? null,
            'new_from_date' => $this->new_from_date,
      'new_to_date' => $this->new_to_date,
        ]);*/

        // 🔥 ALWAYS SYNC TO DATE WITH FROM DATE
        $this->new_to_date = $this->new_from_date;
        // 1. Validation
        $this->validate([
            'new_from_date' => 'required|date|after_or_equal:today',
            'new_to_date'   => 'required|date|after_or_equal:new_from_date',
        ]);
        Log::info('Temp Examiner Duplicate Check', [
            'user_id'     => $this->userId,
            'examiner_id' => $this->selectedRow['examiner_id'] ?? null,
            'from_date'   => $this->new_from_date,
            'exclude_id'  => $this->selectedRow['id'] ?? null,
        ]);
        // 2. Conflict check: same examiner already scheduled on same date elsewhere
       /* $exists = DB::table('temp_examiner_assigned_details')
            ->where('user_id', $this->userId)
            ->where('examiner_id', $this->selectedRow['examiner_id'])
            ->where('from_date', $this->new_from_date)
            ->where('id', '!=', $this->selectedRow['id']) // exclude current row
            ->exists();*/

        $query = DB::table('temp_examiner_assigned_details')
            ->where('user_id', $this->userId)
            ->where('examiner_id', $this->selectedRow['examiner_id'])
            ->where('from_date', $this->new_from_date)
            ->where('id', '!=', $this->selectedRow['id']);


        Log::info('Temp Examiner SQL', [
            'sql'      => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        $exists = $query->exists();

        Log::info('Temp Examiner Exists', [
            'exists' => $exists,
        ]);

        if ($exists) {
            $this->addError(
                'new_from_date',
                'This examiner is already scheduled elsewhere on this date.'
            );
            return;
        }

        // 3. Update the single row safely (by primary key)
        $updated = DB::table('temp_examiner_assigned_details')
            ->where('id', $this->selectedRow['id'])
            ->update([
                'original_from_date' => DB::raw("COALESCE(original_from_date, '{$this->current_from_date}')"),
                'from_date'          => $this->new_from_date,
                'to_date'            => $this->new_from_date,
                'is_rescheduled'     => 1,
                'rescheduled_at'     => now(),
                'rescheduled_by'     => $this->userId,
                'updated_at'         => now(),
            ]);

        // 4. Safety: if nothing updated
        if ($updated === 0) {
            $this->addError(
                'new_from_date',
                'No record updated. Please refresh and try again.'
            );
            return;
        }

        // 5. Close drawer and refresh UI
        $this->showDateDrawer = false;
        $this->selectedRow = [];

        // ✅ Success toast
        $this->dispatch('toast', message: 'Date updated successfully');

        $this->dispatch('$refresh');
    }

    public function undoDateChange(int $rowId): void
    {
        $row = DB::table('temp_examiner_assigned_details')
            ->where('id', $rowId)
            ->first();

        if (! $row || ! $row->is_rescheduled || ! $row->original_from_date) {
            return;
        }

        DB::table('temp_examiner_assigned_details')
            ->where('id', $rowId)
            ->update([
                'from_date'          => $row->original_from_date,
                'to_date'            => $row->original_from_date,
                'is_rescheduled'     => 0,
                'original_from_date' => null,
                'rescheduled_at'     => null,
                'rescheduled_by'     => null,
                'updated_at'         => now(),
            ]);

        // ✅ Fire toast event
        $this->dispatch('toast', message: 'Date reverted successfully');

        $this->dispatch('$refresh');
    }

    public function openAddDrawer(int $rowId)
    {
        $row = DB::table('temp_examiner_assigned_details')
            ->where('id', $rowId)
            ->first();

        if (! $row) {
            return;
        }

        $this->isAddingAdditional = true;

        // Reuse the SAME drawer used by Change Examiner
        $this->openDrawer((array) $row);
    }

    public function addAdditionalExaminer(int $examinerDetailsId)
    {
        if (empty($this->selectedRow)) return;

        // Fetch examiner from SAME SOURCE as assignExaminer
        $examiner = DB::table('examiner_details as ed')
            ->join('basic_details as bd', 'bd.id', '=', 'ed.examiner_details_basic_details_id')
            ->join('users as u', 'u.id', '=', 'bd.basic_details_user_id')
            ->where('ed.id', $examinerDetailsId)
            ->select([
                'ed.id',
                'u.name',
                'u.mobile',
                'ed.examiner_details_type'
            ])
            ->first();

        if (! $examiner) return;

        // Base row = truth
        $base = DB::table('temp_examiner_assigned_details')
            ->where('id', $this->selectedRow['id'])
            ->first();

        if (! $base) return;

        // Prevent duplicate
        $exists = DB::table('temp_examiner_assigned_details')
            ->where('batch_range_id', $base->batch_range_id)
            ->where('examiner_id', $examiner->id)
            ->exists();

        if ($exists) {
            $this->dispatch('toast', message: 'Examiner already added');
            return;
        }

        DB::table('temp_examiner_assigned_details')->insert([
            'user_id'           => $base->user_id,
            'year_id'           => $base->year_id,
            'month_id'          => $base->month_id,
            'scheme_id'         => $base->scheme_id,
            'degree_id'         => $base->degree_id,
            'batch_id'          => $base->batch_id,
            'batch_range_id'    => $base->batch_range_id,

            'examiner_id'       => $examiner->id,
            'examiner_name'     => $examiner->name,
            'mobile'            => $examiner->mobile,

            // SAME TYPE as SLOT
            'examiner_type'     => $base->examiner_type,
            'examiner_type_id'  => $base->examiner_type_id,

            'centre_id'         => $base->centre_id,
            'centre_name'       => $base->centre_name,
            'attached_id'       => $base->attached_id,
            'user_college_id'   => $base->user_college_id,

            'subject_id'        => $base->subject_id,
            'subject_name'      => $base->subject_name,
            'user_stream_id'    => $base->user_stream_id,

            'status'            => $base->status,
            'status_label'      => $base->status_label,
            'batch_name'        => $base->batch_name,

            'from_date'         => $base->from_date,
            'to_date'           => $base->to_date,
            'original_from_date'=> $base->from_date,

            'created_at'        => now(),
            'updated_at'        => now(),

            'is_additional' => 1,
        ]);

        $this->isAddingAdditional = false;

        $this->showAddDrawer = false;

        $this->dispatch('toast', message: 'Additional examiner added');

        $this->dispatch('$refresh');

    }

    public function saveAdditionalExaminer()
    {
        $this->validate([
            'add_examiner_id' => 'required',
            'add_session_id'  => 'required|exists:temp_examiner_assigned_details,id',
        ]);

        // Base row (truth source)
        $base = DB::table('temp_examiner_assigned_details')
            ->where('id', $this->add_session_id)
            ->first();

        if (! $base) {
            throw new \Exception('Invalid base examiner row');
        }

        // Examiner from drawer dataset (contains type + id)
        $examiner = collect($this->centreExaminers)
            ->firstWhere('id', $this->add_examiner_id);

        if (! $examiner) {
            throw new \Exception('Invalid examiner selection');
        }

        // Duplicate protection (same examiner, same batch_range)
        $exists = DB::table('temp_examiner_assigned_details')
            ->where('batch_range_id', $base->batch_range_id)
            ->where('examiner_id', $examiner['id'])
            ->exists();

        if ($exists) {
            $this->dispatch('toast', type: 'warning', message: 'Examiner already added');
            return;
        }

        DB::table('temp_examiner_assigned_details')->insert([
            // Session / context clone
            'user_id'           => $base->user_id,
            'year_id'           => $base->year_id,
            'month_id'          => $base->month_id,
            'scheme_id'         => $base->scheme_id,
            'degree_id'         => $base->degree_id,
            'batch_id'          => $base->batch_id,
            'batch_range_id'    => $base->batch_range_id,

            // Examiner (REAL data)
            'examiner_id'       => $examiner['id'],
            'examiner_name'     => $examiner['name'],
            'mobile'            => $examiner['mobile'],
            'examiner_type'     => $examiner['examiner_type'],
            'examiner_type_id'  => $examiner['examiner_type_id'],

            // Structural consistency
            'centre_id'         => $base->centre_id,
            'centre_name'       => $base->centre_name,
            'attached_id'       => $base->attached_id,
            'user_college_id'   => $base->user_college_id,

            'subject_id'        => $base->subject_id,
            'subject_name'      => $base->subject_name,
            'user_stream_id'    => $base->user_stream_id,

            'status'            => $base->status,
            'status_label'      => $base->status_label,

            'batch_name'        => $base->batch_name,

            'from_date'         => $base->from_date,
            'to_date'           => $base->to_date,
            'original_from_date'=> $base->from_date,

            'is_rescheduled'    => 0,

            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $this->dispatch('toast', type: 'success', message: 'Additional examiner added');

        $this->dispatch('$refresh');

        $this->showAddDrawer = false;
    }

    public function undoAdditionalExaminer(int $rowId): void
    {
        $row = DB::table('temp_examiner_assigned_details')
            ->where('id', $rowId)
            ->where('user_id', $this->userId)
            ->where('is_additional', 1)
            ->first();

        if (! $row) {
            return;
        }

        DB::table('temp_examiner_assigned_details')
            ->where('id', $rowId)
            ->delete();

        $this->dispatch('toast', message: 'Additional examiner removed');

        $this->dispatch('$refresh');
    }


}
