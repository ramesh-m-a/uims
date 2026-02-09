<?php

namespace App\Livewire\Examiner\AppointmentOrder;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Repositories\AppointmentOrderRepository;
use App\Services\ExaminerAllocation\AllocationContext;

class AppointmentOrderView extends Component
{
    public $yearId;
    public $monthId;
    public $schemeId;
    public $degreeId;

    public $years = [];
    public $months = [];
    public $schemes = [];
    public ?string $centre = null;

    public $rows = [];
    public $requestStatusMap = [];

    public function mount()
    {
        /** 🔐 SECURITY */
        if (!auth()->check()) {
            abort(403);
        }

        /** ⭐ LOAD FILTER DROPDOWNS */
        $this->years   = DB::table('mas_year')->orderByDesc('id')->get();
        $this->months  = DB::table('mas_month')->orderBy('id')->get();
        $this->schemes = DB::table('mas_revised_scheme')->orderByDesc('id')->get();

        /** ⭐ RESTORE SESSION FILTERS */
        $this->yearId   = session('allocation.yearId');
        $this->monthId  = session('allocation.monthId');
        $this->schemeId = session('allocation.schemeId');
        $this->degreeId = session('allocation.degreeId');

        if ($this->yearId && $this->monthId && $this->schemeId && $this->degreeId) {
            $this->loadData();
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
            $this->loadData();
        }
    }

    private function loadData(): void
    {
        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            $this->rows = collect();
            return;
        }

        $context = new AllocationContext(
            userId: auth()->id(),
            yearId: (int) $this->yearId,
            monthId: (int) $this->monthId,
            schemeId: (int) $this->schemeId,
            degreeId: (int) $this->degreeId,
            streamId: auth()->user()->user_stream_id ?? 0,
        );

        /** ⭐ LOAD ALLOCATION + APPOINTMENT MERGED DATA */
        $this->rows = collect(
            app(AppointmentOrderRepository::class)
                ->fetchForCollegeContext($context)
        );

        /** ⭐ LOAD STATUS MAP (SAME AS ALLOCATION MODULE) */
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

    public function render()
    {
        return view('livewire.examiner.shared.allocation-table-appointment-wrapper', [
            'rows' => $this->rows,
            'centre' => $this->centre ?? null,
            'requestStatusMap' => $this->requestStatusMap,
            'isAppointmentModule' => true,

            // ⭐ CRITICAL
          //  'actionsView' => 'livewire.examiner.appointment-order.partials.actions-appointment',
        ]);
    }

    private function sortRows($collection)
    {
        return collect($collection)
            ->sortBy([
                ['centre_name', 'asc'],
                ['batch_name', 'asc'],
                ['from_date', 'asc'],
            ])
            ->values();
    }

    public function getRowsProperty()
    {
        return $this->sortRows($this->rows);
    }

    public function getAllRowsProperty()
    {
        return $this->sortRows($this->rows);
    }

}
