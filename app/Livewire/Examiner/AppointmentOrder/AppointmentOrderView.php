<?php

namespace App\Livewire\Examiner\AppointmentOrder;

use App\Support\Examiner\ExaminerUniversalSort;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Repositories\AppointmentOrderRepository;
use App\Services\ExaminerAllocation\AllocationContext;

class AppointmentOrderView extends Component
{
    use ExaminerUniversalSort;

    public $yearId;
    public $monthId;
    public $schemeId;
    public $degreeId;
    public ?string $centre = null;

    public array $requestStatusMap = [];

    /* =========================================
       MOUNT
    ========================================= */
    public function mount()
    {
        if (!auth()->check()) abort(403);

        $this->yearId   = session('allocation.yearId');
        $this->monthId  = session('allocation.monthId');
        $this->schemeId = session('allocation.schemeId');
        $this->degreeId = session('allocation.degreeId');

        $this->buildRequestStatusMap();
    }

    /* =========================================
       ⭐ STATELESS SOURCE OF TRUTH
    ========================================= */
    protected function baseQuery()
    {
        $context = new AllocationContext(
            userId: auth()->id(),
            yearId: (int) $this->yearId,
            monthId: (int) $this->monthId,
            schemeId: (int) $this->schemeId,
            degreeId: (int) $this->degreeId,
            streamId: auth()->user()->user_stream_id ?? 6,
        );

        return collect(
            app(AppointmentOrderRepository::class)
                ->fetchForCollegeContext($context)
        )->map(fn($r) => is_array($r) ? (object)$r : $r);
    }

    /* =========================================
       RIGHT PANEL DATA
    ========================================= */
    public function getRowsProperty()
    {
        return $this->sortExaminerRows(
            $this->baseQuery()
                ->when($this->centre, fn($c) =>
                $c->where('centre_name', $this->centre)
                )
                ->values()
        );
    }

    /* =========================================
       LEFT PANEL DATA (FULL DATASET)
    ========================================= */
    public function getAllRowsProperty()
    {
        return $this->sortExaminerRows(
            $this->baseQuery()->values()
        );
    }

    /* =========================================
       STATUS MAP (Same Concept as Allocation)
    ========================================= */
    private function buildRequestStatusMap(): void
    {
        if (!$this->yearId || !$this->monthId || !$this->schemeId) {
            $this->requestStatusMap = [];
            return;
        }

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

    /* =========================================
       RENDER
    ========================================= */
    public function render()
    {
        return view('livewire.examiner.shared.allocation-table-appointment-wrapper', [
            'rows' => $this->rows,
            'allRows' => $this->allRows,
            'centre' => $this->centre,
            'requestStatusMap' => $this->requestStatusMap,
            'isAppointmentModule' => true,
        ]);
    }

    public function generateAppointmentOrder($allocationId)
    {
        $alloc = DB::table('temp_examiner_assigned_details')
            ->where('id', $allocationId)
            ->first();

        if (!$alloc) return;

        app(\App\Services\Examiner\AppointmentOrderService::class)
            ->generateIfMissing($alloc);

        $this->dispatch('$refresh');
    }

    public function retryAppointmentPdf($allocationId)
    {
        $order = DB::table('appointment_orders')
            ->where('allocation_id', $allocationId)
            ->where('is_latest', 1)
            ->first();

        if (!$order) return;

        \App\Jobs\GenerateAppointmentOrderPdfJob::dispatch($order->id);

        $this->dispatch('$refresh');
    }

}
