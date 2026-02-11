<?php

namespace App\Livewire\Examiner\AppointmentOrder;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Repositories\AppointmentOrderRepository;
use App\Services\ExaminerAllocation\AllocationContext;
use App\Support\Examiner\ExaminerUniversalSort;

class AppointmentOrderView extends Component
{
    use ExaminerUniversalSort;

    /* FILTERS */
    public $yearId;
    public $monthId;
    public $schemeId;
    public $degreeId;

    public $years = [];
    public $months = [];
    public $schemes = [];

    public ?string $centre = null;

    public Collection $rows;

    public array $requestStatusMap = [];

    /* =========================================
       MOUNT (⭐ SESSION DRIVEN — SAME AS COLLEGE)
    ========================================= */
    public function mount()
    {
        abort_unless(auth()->check(), 403);

        $this->years   = DB::table('mas_year')->orderByDesc('id')->get();
        $this->months  = DB::table('mas_month')->orderBy('id')->get();
        $this->schemes = DB::table('mas_revised_scheme')->orderByDesc('id')->get();

        // ⭐ SESSION SOURCE OF TRUTH
        $this->yearId   = session('allocation.yearId');
        $this->monthId  = session('allocation.monthId');
        $this->schemeId = session('allocation.schemeId');
        $this->degreeId = session('allocation.degreeId');

        $this->rows = collect();

        if ($this->yearId && $this->monthId && $this->schemeId && $this->degreeId) {
            $this->loadOrders();
        }
    }

    /* =========================================
       UPDATED HOOK (⭐ SAME AS COLLEGE)
    ========================================= */
    public function updated($field)
    {
        if (!in_array($field, [
            'yearId',
            'monthId',
            'schemeId',
            'degreeId'
        ])) {
            return;
        }

        // ⭐ STORE SESSION (SHARED CONTEXT)
        session([
            'allocation.yearId'   => $this->yearId,
            'allocation.monthId'  => $this->monthId,
            'allocation.schemeId' => $this->schemeId,
            'allocation.degreeId' => $this->degreeId,
        ]);
    }

    /* =========================================
       LOAD DATA
    ========================================= */
    private function loadOrders(): void
    {
        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            $this->rows = collect();
            $this->requestStatusMap = [];
            return;
        }

        $context = new AllocationContext(
            userId: auth()->id(),
            yearId: (int)$this->yearId,
            monthId: (int)$this->monthId,
            schemeId: (int)$this->schemeId,
            degreeId: (int)$this->degreeId,
            streamId: auth()->user()->user_stream_id ?? 6,
        );

        $this->rows = collect(
            app(AppointmentOrderRepository::class)
                ->fetchForCollegeContext($context)
        )->map(fn($r) => is_array($r) ? (object)$r : $r);

        $this->buildRequestStatusMap();
    }

    /* =========================================
       STATUS MAP
    ========================================= */
    private function buildRequestStatusMap(): void
    {
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

        $requests = DB::table('college_examiner_request_details')
            ->where('college_examiner_request_details_year_id', $this->yearId)
            ->where('college_examiner_request_details_month_id', $this->monthId)
            ->where('college_examiner_request_details_revised_scheme_id', $this->schemeId)
            ->get();

        $map = [];

        foreach ($requests as $req) {

            $batchId = (int)$req->college_examiner_request_details_batch_id;
            $examinerId = (int)$req->college_examiner_request_details_examiner_id;
            $statusId = (int)$req->college_examiner_request_details_status_id;

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

    /* =========================================
       ACTIONS
    ========================================= */

    public function generateAppointmentOrder($allocationId)
    {
        $alloc = DB::table('temp_examiner_assigned_details')
            ->where('id', $allocationId)
            ->first();

        if (!$alloc) return;

        app(\App\Services\Examiner\AppointmentOrderService::class)
            ->generateIfMissing($alloc);

        $this->loadOrders();
    }

    public function retryAppointmentPdf($allocationId)
    {
        $order = DB::table('appointment_orders')
            ->where('allocation_id', $allocationId)
            ->where('is_latest', 1)
            ->first();

        if (!$order) return;

        \App\Jobs\GenerateAppointmentOrderPdfJob::dispatch($order->id);

        $this->loadOrders();
    }

    /* =========================================
       RENDER
    ========================================= */
    public function render()
    {
        return view('livewire.examiner.shared.allocation-table-appointment-wrapper', [
            'rows' => $this->rows,
            'centre' => $this->centre,
            'requestStatusMap' => $this->requestStatusMap,
            'isAppointmentModule' => true,
        ]);
    }
}
