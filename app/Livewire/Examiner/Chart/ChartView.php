<?php

namespace App\Livewire\Examiner\Chart;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Support\Examiner\ExaminerUniversalSort;
use App\Services\ExaminerAllocation\AllocationContext;

class ChartView extends Component
{
    use ExaminerUniversalSort;

    public $yearId;
    public $monthId;
    public $schemeId;
    public $degreeId;
    public ?string $centre = null;

    public array $requestStatusMap = [];

    public function mount()
    {
        if (!auth()->check()) abort(403);

        $this->yearId   = session('allocation.yearId');
        $this->monthId  = session('allocation.monthId');
        $this->schemeId = session('allocation.schemeId');
        $this->degreeId = session('allocation.degreeId');
    }

    /**
     * ⭐ Source Of Truth (Temp Table Finalized Records)
     */
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
            DB::table('temp_examiner_assigned_details')
                ->where('status', 26) // finalized
                ->get()
        )->map(fn($r) => (object)$r);
    }

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

    public function getAllRowsProperty()
    {
        return $this->sortExaminerRows(
            $this->baseQuery()->values()
        );
    }

    public function render()
    {
        return view('livewire.examiner.shared.allocation-table-chart-wrapper', [
            'rows' => $this->rows,
            'allRows' => $this->allRows,
            'centre' => $this->centre,
            'requestStatusMap' => [], // Chart doesn’t need request flow
            'isAppointmentModule' => false, // IMPORTANT
        ]);
    }
}
