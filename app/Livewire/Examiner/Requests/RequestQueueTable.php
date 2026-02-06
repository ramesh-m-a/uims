<?php

namespace App\Livewire\Examiner\Requests;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\ExaminerRequestApprovalService;
use App\Services\ExaminerRequestRejectService;

class RequestQueueTable extends Component
{
    public ?string $centre = null;

    public int $userId;
    public ?int $yearId = null;
    public ?int $monthId = null;
    public ?int $schemeId = null;
    public ?int $degreeId = null;

    public int $statusFilter = 26; // Pending default

    protected $listeners = ['$refresh'];

    public function mount()
    {
        $this->userId   = auth()->id();
        $this->yearId   = session('allocation.yearId');
        $this->monthId  = session('allocation.monthId');
        $this->schemeId = session('allocation.schemeId');
        $this->degreeId = session('allocation.degreeId');

        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            abort(403, 'Invalid allocation session');
        }
    }

    /**
     * ======================================================
     * BASE QUERY
     * ======================================================
     */
    private function baseQueryold()
    {
        return DB::table('college_examiner_request_details as cer')

            /**
             * ==========================================
             * TEMP CONTEXT (Allocation Context)
             * ==========================================
             */
            ->join('temp_examiner_assigned_details as temp', function ($join) {
                $join->on('temp.batch_id', '=', 'cer.college_examiner_request_details_batch_id')
                    ->on('temp.examiner_id', '=', 'cer.college_examiner_request_details_examiner_id')

                    ->where('temp.user_id', $this->userId)
                    ->where('temp.year_id', $this->yearId)
                    ->where('temp.month_id', $this->monthId)
                    ->where('temp.scheme_id', $this->schemeId)
                    ->where('temp.degree_id', $this->degreeId);
            })

            /**
             * ==========================================
             * STATUS MASTER
             * ==========================================
             */
            ->join('request_status_master as rsm',
                'rsm.id',
                '=',
                'cer.college_examiner_request_details_status_id'
            )

            ->leftJoin('mas_status as ms',
                'ms.id',
                '=',
                'rsm.status_id'
            )

            /**
             * ==========================================
             * FROM EXAMINER (OLD)
             * examiner_details → basic_details → users
             * ==========================================
             */
            ->leftJoin('examiner_details as from_ed',
                'from_ed.id',
                '=',
                'cer.college_examiner_request_details_examiner_id'
            )

            ->leftJoin('basic_details as from_bd',
                'from_bd.id',
                '=',
                'from_ed.examiner_details_basic_details_id'
            )

            ->leftJoin('users as from_user',
                'from_user.id',
                '=',
                'from_bd.basic_details_user_id'
            )

            /**
             * ==========================================
             * TO EXAMINER (NEW)
             * examiner_details → basic_details → users
             * ==========================================
             */
            ->leftJoin('examiner_details as to_ed',
                'to_ed.id',
                '=',
                'cer.college_examiner_request_details_new_examiner_id'
            )

            ->leftJoin('basic_details as to_bd',
                'to_bd.id',
                '=',
                'to_ed.examiner_details_basic_details_id'
            )

            ->leftJoin('users as to_user',
                'to_user.id',
                '=',
                'to_bd.basic_details_user_id'
            );
    }

    private function baseQuery()
    {
        return DB::table('college_examiner_request_details as cer')

            /**
             * =================================
             * TEMP CONTEXT
             * =================================
             */
            ->join('temp_examiner_assigned_details as temp', function ($join) {
                $join->on('temp.batch_id', '=', 'cer.college_examiner_request_details_batch_id')
                    ->on('temp.examiner_id', '=', 'cer.college_examiner_request_details_examiner_id')

                    ->where('temp.user_id', $this->userId)
                    ->where('temp.year_id', $this->yearId)
                    ->where('temp.month_id', $this->monthId)
                    ->where('temp.scheme_id', $this->schemeId)
                    ->where('temp.degree_id', $this->degreeId);
            })

            /**
             * =================================
             * ATTACHED COLLEGE
             * =================================
             */
            ->leftJoin('mas_college as attached_college',
                'attached_college.id',
                '=',
                'temp.attached_id'
            )

            /**
             * =================================
             * STATUS
             * =================================
             */
            ->join('request_status_master as rsm',
                'rsm.id',
                '=',
                'cer.college_examiner_request_details_status_id'
            )

            ->leftJoin('mas_status as ms',
                'ms.id',
                '=',
                'rsm.status_id'
            )

            /**
             * =================================
             * NEW EXAMINER (CORRECT CHAIN)
             * =================================
             */
            ->leftJoin('examiner_details as to_ed',
                'to_ed.id',
                '=',
                'cer.college_examiner_request_details_new_examiner_id'
            )

            ->leftJoin('basic_details as to_bd',
                'to_bd.id',
                '=',
                'to_ed.examiner_details_basic_details_id'
            )

            ->leftJoin('users as to_user',
                'to_user.id',
                '=',
                'to_bd.basic_details_user_id'
            );
    }

    /**
     * ======================================================
     * ROWS (TABLE DATA)
     * ======================================================
     */
    public function getRowsPropertyold()
    {
        $q = $this->baseQuery()
            ->where(
                'cer.college_examiner_request_details_status_id',
                $this->statusFilter
            );

        if ($this->centre) {
            $q->where('temp.centre_name', $this->centre);
        }

        return $q
            ->select([
                'cer.id as request_id',

                // Context
                'temp.centre_name',
                'temp.batch_name',
                'temp.from_date',
                'temp.examiner_type',

                // FROM Examiner
                'from_user.name as old_examiner_name',
                'from_user.mobile as old_examiner_mobile',

                // TO Examiner
                'to_user.name as new_examiner_name',
                'to_user.mobile as new_examiner_mobile',

                // Reason
                'cer.college_examiner_request_details_comments as reason',

                // Status
                'rsm.label as status_label',

                DB::raw("
                    COALESCE(ms.mas_status_label_colour, 'bg-gray-400')
                    as status_colour
                "),
            ])
            ->orderBy('temp.centre_name')
            ->orderBy('temp.batch_name')
            ->orderBy('temp.from_date')
            ->get();
    }

    public function getRowsProperty()
    {
        $q = $this->baseQuery()
            ->where(
                'cer.college_examiner_request_details_status_id',
                $this->statusFilter
            );

        if ($this->centre) {
            $q->where('temp.centre_name', $this->centre);
        }

        return $q
            ->select([
                'cer.id as request_id',

                'temp.centre_name',
                'temp.batch_name',
                'temp.from_date',
                'temp.to_date',
                'temp.examiner_type',

                'temp.examiner_name as old_examiner_name',
                'temp.mobile as old_examiner_mobile',

                'to_user.name as new_examiner_name',
                'to_user.mobile as new_examiner_mobile',

                'attached_college.mas_college_name as attached_college_name',

                'cer.college_examiner_request_details_comments as reason',

                'rsm.label as status_label',

                DB::raw("
        COALESCE(ms.mas_status_label_colour, 'bg-gray-400')
        as status_colour
    "),
            ])

            ->orderBy('temp.centre_name')
            ->orderBy('temp.batch_name')
            ->orderBy('temp.from_date')
            ->get();
    }

    /**
     * ======================================================
     * CENTRES (LEFT PANEL)
     * ======================================================
     */
    public function getCentresProperty()
    {
        return $this->baseQuery()
            ->where(
                'cer.college_examiner_request_details_status_id',
                $this->statusFilter
            )
            ->select('temp.centre_name')
            ->distinct()
            ->orderBy('temp.centre_name')
            ->pluck('centre_name');
    }

    /**
     * ======================================================
     * APPROVE
     * ======================================================
     */
    public function approveRequest(
        int $requestId,
        ExaminerRequestApprovalService $service
    )
    {
        $service->approve($requestId, auth()->id());

        $this->dispatch('toast', message: 'Request Approved');
        $this->dispatch('$refresh');
    }

    /**
     * ======================================================
     * REJECT
     * ======================================================
     */
    public function rejectRequest(
        int $requestId,
        ExaminerRequestRejectService $service
    )
    {
        $service->reject($requestId, auth()->id(), 'Rejected by Admin');

        $this->dispatch('toast', message: 'Request Rejected');
        $this->dispatch('$refresh');
    }

    /**
     * ======================================================
     * RENDER
     * ======================================================
     */
    public function render()
    {
        return view('livewire.examiner.requests.request-queue-table', [
            'rows' => $this->rows,
            'centres' => $this->centres,
        ]);
    }
}
