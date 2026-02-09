<?php

namespace App\Livewire\Examiner\Allocation;

use App\Services\Exam\College\CollegeExaminerRequestService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Collection;
use App\Repositories\TempAllocationRepository;
use App\Services\ExaminerAllocation\AllocationContext;
use App\Services\RequestStatusService;

class ExaminerAllocationCollege extends Component
{
    /** Filters */
    public $yearId;
    public $monthId;
    public $schemeId;
    public $degreeId;

    public $years = [];
    public $months = [];
    public $schemes = [];

    /** Table Data */
    public Collection $rows;

    /** UI Filters */
    public $search = '';
    public $centre = null;
    public $batch = '';
    public $subject = '';
    public $examiner = '';
    public $status = '';

    public bool $showRequestDrawer = false;
    public array $selectedRow = [];
    public string $pickerSearch = '';

    public array $pendingRequestsMap = [];
    public array $requestStatusMap = [];

    public function mount()
    {
        // 🚨 HARD SECURITY — ONLY COLLEGE CAN ENTER
        if (!auth()->check() || auth()->user()->user_role_id !== 3) {
            abort(403, 'Unauthorized access');
        }

        // ⭐ LOAD DROPDOWNS (CRITICAL)
        $this->years = DB::table('mas_year')->orderByDesc('id')->get();
        $this->months = DB::table('mas_month')->orderBy('id')->get();
        $this->schemes = DB::table('mas_revised_scheme')->orderByDesc('id')->get();

        // ⭐ LOAD FILTERS FROM SESSION (🔥 MISSING PIECE)
        $this->yearId   = session('allocation.yearId');
        $this->monthId  = session('allocation.monthId');
        $this->schemeId = session('allocation.schemeId');
        $this->degreeId = session('allocation.degreeId');

        $this->rows = collect();

        // ⭐ AUTO LOAD DATA IF FILTERS EXIST
        if ($this->yearId && $this->monthId && $this->schemeId && $this->degreeId) {
            $this->loadAllocation();
        }
    }

    public function updated($field)
    {
        if (in_array($field, [
            'yearId',
            'monthId',
            'schemeId',
            'degreeId'
        ])) {
            $this->loadAllocation();
        }
    }

    private function loadAllocationold(): void
    {
        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            $this->rows = collect();
            return;
        }

        /** 🚨 READ ONLY FLOW */
        $repo = app(TempAllocationRepository::class);

        $context = new AllocationContext(
            userId: auth()->id(),
            yearId: (int) $this->yearId,
            monthId: (int) $this->monthId,
            schemeId: (int) $this->schemeId,
            degreeId: (int) $this->degreeId,
            streamId: auth()->user()->user_stream_id ?? 0,
        );

        $this->rows = $repo->fetchForCollegeContext($context);
    }

    private function loadAllocationissue(): void
    {
        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            $this->rows = collect();
            $this->pendingRequestsMap = [];
            return;
        }

        $repo = app(\App\Repositories\TempAllocationRepository::class);
        $statusService = app(RequestStatusService::class);

        $context = new AllocationContext(
            userId: auth()->id(),
            yearId: (int) $this->yearId,
            monthId: (int) $this->monthId,
            schemeId: (int) $this->schemeId,
            degreeId: (int) $this->degreeId,
            streamId: auth()->user()->user_stream_id ?? 0,
        );

        $this->rows = $repo->fetchForCollegeContext($context);

        // ⭐ ENTERPRISE STATUS DRIVEN LOGIC
        $pendingStatusIds = $statusService->getPendingStatusIds();

        $this->pendingRequestsMap = DB::table('college_examiner_request_details')
            ->where('college_examiner_request_details_college_id', auth()->user()->user_college_id)
            ->whereIn('college_examiner_request_details_status_id', $pendingStatusIds)
            ->get([
                'college_examiner_request_details_batch_id',
                'college_examiner_request_details_examiner_id'
            ])
            ->groupBy('college_examiner_request_details_batch_id')
            ->map(function ($items) {
                return $items
                    ->pluck('college_examiner_request_details_examiner_id')
                    ->map(fn($v) => (int)$v)
                    ->values()
                    ->all();
            })
            ->toArray();
    }

    private function loadAllocation(): void
    {
        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            $this->rows = collect();
            $this->requestStatusMap = [];
            return;
        }

        $repo = app(\App\Repositories\TempAllocationRepository::class);

        $context = new AllocationContext(
            userId: auth()->id(),
            yearId: (int) $this->yearId,
            monthId: (int) $this->monthId,
            schemeId: (int) $this->schemeId,
            degreeId: (int) $this->degreeId,
            streamId: auth()->user()->user_stream_id ?? 0,
        );

        $this->rows = $repo->fetchForCollegeContext($context);

        // ⭐ LOAD ALL REQUEST STATUSES (NOT ONLY PENDING)
        $statusMaster = DB::table('request_status_master as rsm')
            ->leftJoin('mas_status as ms', 'ms.id', '=', 'rsm.status_id')
            ->select(
                'rsm.id',
                'rsm.label',
                'rsm.is_pending',
                DB::raw("COALESCE(ms.mas_status_label_colour, 'bg-gray-400') as colour")
            )
            ->get()
            ->keyBy('id');

    //    dd($statusMaster);
        // ⭐ LOAD REQUEST RECORDS
        $requests = DB::table('college_examiner_request_details')
            ->where('college_examiner_request_details_college_id', auth()->user()->user_college_id)
            ->where('college_examiner_request_details_year_id', $this->yearId)
            ->where('college_examiner_request_details_month_id', $this->monthId)
            ->where('college_examiner_request_details_revised_scheme_id', $this->schemeId)
            ->get([
                'college_examiner_request_details_batch_id',
                'college_examiner_request_details_examiner_id',
                'college_examiner_request_details_status_id'
            ]);


        // ⭐ BUILD ENTERPRISE MAP
        $map = [];

        foreach ($requests as $req) {

            $batchId = (int) $req->college_examiner_request_details_batch_id;
            $examinerId = (int) $req->college_examiner_request_details_examiner_id;
            $statusId = (int) $req->college_examiner_request_details_status_id;

            $status = $statusMaster[$statusId] ?? null;

            if (!$status) continue;

            $map[$batchId][$examinerId] = [
                'label' => $status->label,
                'is_pending' => (bool)$status->is_pending,
                'colour' => $status->colour
            ];
        }

        $this->requestStatusMap = $map;
    }

    public function render()
    {
       /* logger([
            'college_id' => auth()->user()->user_college_id,
            'yearId' => $this->yearId,
            'monthId' => $this->monthId,
            'schemeId' => $this->schemeId,
            'degreeId' => $this->degreeId,
        ]);*/

        logger($this->requestStatusMap);

        return view('livewire.examiner.shared.allocation-table-allocation-wrapper', [
            'rows' => $this->filteredRows(),
            'centre' => null,
            'isCollegeUser' => true,
            'pickerResults' => $this->pickerResults,
            'requestStatusMap' => $this->requestStatusMap, // ⭐ CRITICAL LINE
            'isAppointmentModule' => false,
        ]);
    }

    private function filteredRows(): Collection
    {
        return collect($this->rows)
            ->filter(function ($row) {

                if ($this->search) {
                    $search = strtolower($this->search);

                    if (
                        !str_contains(strtolower($row->examiner_name ?? ''), $search) &&
                        !str_contains(strtolower($row->centre_name ?? ''), $search) &&
                        !str_contains(strtolower($row->subject_name ?? ''), $search)
                    ) {
                        return false;
                    }
                }

                if ($this->centre && $row->centre_name !== $this->centre) return false;
                if ($this->batch && !str_contains($row->batch_name ?? '', $this->batch)) return false;
                if ($this->subject && !str_contains($row->subject_name ?? '', $this->subject)) return false;
                if ($this->examiner && !str_contains($row->examiner_name ?? '', $this->examiner)) return false;
                if ($this->status && !str_contains($row->status_label ?? '', $this->status)) return false;

                return true;
            })
            ->values();
    }

    public function requestExaminerChangeold(int $tempRowId, int $newExaminerId)
    {
        $row = collect($this->rows)->firstWhere('id', $tempRowId);

        if (!$row) return;

        // 🔒 SECURITY — Ensure row belongs to this college
        if ((int)$row->centre_id !== (int)auth()->user()->user_college_id) {
            abort(403, 'Invalid centre access');
        }

        // 🔒 DUPLICATE REQUEST CHECK
        $exists = DB::table('college_examiner_request_details')
            ->where('college_examiner_request_details_batch_range_id', $row->batch_range_id)
            ->where('college_examiner_request_details_examiner_id', $row->examiner_id)
            ->where('college_examiner_request_details_college_id', auth()->user()->user_college_id)
            ->exists();

        if ($exists) {
            $this->dispatch('toast', message: 'Request already exists');
            return;
        }

        app(CollegeExaminerRequestService::class)
            ->createExaminerChangeRequest([

                'year_id' => $this->yearId,
                'month_id' => $this->monthId,
                'scheme_id' => $this->schemeId,

                'batch_id' => $row->batch_id,
                'batch_range_id' => $row->batch_range_id,

                'college_id' => auth()->user()->user_college_id,
                'stream_id' => auth()->user()->user_stream_id,

                'current_examiner_id' => $row->examiner_id,
                'new_examiner_id' => $newExaminerId,

                'created_by' => auth()->id(),
                'comments' => 'College Requested Examiner Change',
            ]);

        $this->dispatch('toast', message: 'Request sent to RGUHS');
    }

    public function requestExaminerChange(int $tempRowId, int $newExaminerId)
    {
        $row = collect($this->rows)->firstWhere('id', $tempRowId);

        if (!$row) return;

        // 🔒 SECURITY
        if ((int)$row->centre_id !== (int)auth()->user()->user_college_id) {
            abort(403, 'Invalid centre access');
        }

        // 🔒 DUPLICATE CHECK
        $exists = DB::table('college_examiner_request_details')
            ->where('college_examiner_request_details_batch_range_id', $row->batch_range_id)
            ->where('college_examiner_request_details_examiner_id', $row->examiner_id)
            ->where('college_examiner_request_details_college_id', auth()->user()->user_college_id)
            ->exists();

        if ($exists) {
            $this->dispatch('toast', message: 'Request already exists');
            return;
        }

        // ⭐ CREATE REQUEST
        app(CollegeExaminerRequestService::class)
            ->createExaminerChangeRequest([

                'year_id' => $this->yearId,
                'month_id' => $this->monthId,
                'scheme_id' => $this->schemeId,

                'batch_id' => $row->batch_id,
                'batch_range_id' => $row->batch_range_id,

                'college_id' => auth()->user()->user_college_id,
                'stream_id' => auth()->user()->user_stream_id,

                'current_examiner_id' => $row->examiner_id,
                'new_examiner_id' => $newExaminerId,

                'created_by' => auth()->id(),
                'comments' => 'College Requested Examiner Change',
            ]);

        /** ⭐⭐⭐ CRITICAL FIX — REFRESH UI DATA */
        $this->loadAllocation();

        $this->dispatch('toast', message: 'Request sent to RGUHS');
    }

    public function openRequestDrawer($row)
    {
        $this->selectedRow = $row;
        $this->pickerSearch = '';
        $this->showRequestDrawer = true;
    }

    public function getPickerResultsProperty()
    {
        if (!$this->showRequestDrawer || empty($this->selectedRow)) {
            return collect();
        }

        $type      = $this->selectedRow['examiner_type'] ?? null;
        $subjectId = $this->selectedRow['subject_id'] ?? null;
        $centreId  = (int) ($this->selectedRow['centre_id'] ?? 0);
        $batchId   = (int) ($this->selectedRow['batch_id'] ?? 0);

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
            $q->where('ed.examiner_details_type', 1)
                ->whereIn($effectiveCollege, $attached);
        } elseif ($type === 'External') {
            $q->where('ed.examiner_details_type', 2)
                ->where($effectiveCollege, '!=', $centreId);
        }

        // 🚫 CRITICAL — Exclude currently assigned examiner from picker
        if (!empty($this->selectedRow['examiner_id'])) {
            $q->where('ed.id', '!=', (int)$this->selectedRow['examiner_id']);
        }

        if ($this->pickerSearch) {
            $q->where(function ($sub) {
                $sub->where('u.name', 'like', "%{$this->pickerSearch}%")
                    ->orWhere('u.mobile', 'like', "%{$this->pickerSearch}%");
            });
        }

        $alreadyRequested = DB::table('college_examiner_request_details')
            ->where('college_examiner_request_details_batch_range_id', $this->selectedRow['batch_range_id'])
            ->where('college_examiner_request_details_college_id', auth()->user()->user_college_id)
            ->pluck('college_examiner_request_details_new_examiner_id')
            ->filter()
            ->values()
            ->all();

        if (!empty($alreadyRequested)) {
            $q->whereNotIn('ed.id', $alreadyRequested);
        }

        return $q
            ->select(['ed.id', 'u.name', 'u.mobile'])
            ->orderBy('u.name')
            ->paginate(10);
    }

    public function submitRequest(int $examinerId)
    {
        if (empty($this->selectedRow)) return;

        $row = (object)$this->selectedRow;

        app(CollegeExaminerRequestService::class)
            ->createExaminerChangeRequest([
                'year_id' => $this->yearId,
                'month_id' => $this->monthId,
                'scheme_id' => $this->schemeId,

                'batch_id' => $row->batch_id,
                'batch_range_id' => $row->batch_range_id,

                'college_id' => auth()->user()->user_college_id,
                'stream_id' => auth()->user()->user_stream_id,

                'current_examiner_id' => $row->examiner_id,
                'new_examiner_id' => $examinerId,

                'created_by' => auth()->id(),
                'comments' => 'College Requested Examiner Change',
            ]);

        /** ⭐⭐⭐ CRITICAL — RELOAD MAP + ROWS */
        $this->loadAllocation();

        $this->showRequestDrawer = false;

        $this->dispatch('toast', message: 'Request sent to RGUHS');
    }

}
