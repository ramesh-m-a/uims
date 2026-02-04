<?php

namespace App\Livewire\Examiner;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExaminerExport;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /* =========================
     | SORTING
     ========================= */
    public string $sortField = 'mc.mas_college_name';
    public string $sortDirection = 'asc';

    /* =========================
     | GLOBAL SEARCH
     ========================= */
    public string $search = '';

    /* =========================
     | COLUMN FILTERS (LIKE MASTERS)
     ========================= */
    public array $columnFilters = [
        'stream'      => '',
        'college'     => '',
        'department'  => '',
        'name'        => '',
        'designation' => '',
        'mobile'      => '',
        'type'        => '',
    ];

    /* =========================
     | EDIT MODAL
     ========================= */
    public bool $showEditModal = false;
    public array $editForm = [];

    /* =========================
     | BASE QUERY
     ========================= */
    protected function baseQuery()
    {
        $query = DB::table('examiner_details as ed')
            ->leftJoin('basic_details as bd', 'ed.examiner_details_basic_details_id', '=', 'bd.id')
            ->leftJoin('user as usr', 'bd.basic_details_user_id', '=', 'usr.id')
            ->leftJoin('mas_stream as ms', 'usr.user_stream_id', '=', 'ms.id')
            ->leftJoin('mas_college as mc', 'usr.user_college_id', '=', 'mc.id')
            ->leftJoin('mas_designation as md', 'usr.user_designation_id', '=', 'md.id')
            ->leftJoin('mas_department as mdt', 'bd.basic_details_department_id', '=', 'mdt.id')
            ->select(
                'ed.id as examinerId',
                'ed.examiner_details_rank',
                'ed.examiner_details_type',
                'usr.user_photo_path',
                'usr.mobile',
                'ms.mas_stream_name',
                'mc.mas_college_name',
                'md.mas_designation_name',
                'mdt.mas_department_name',
                DB::raw("CONCAT(
                    COALESCE(usr.fname,''),' ',
                    COALESCE(usr.mname,''),' ',
                    COALESCE(usr.lname,'')
                ) as fname")
            );

        /* =========================
         | GLOBAL SEARCH
         ========================= */
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('usr.user_mobile_number', 'like', "%{$this->search}%")
                    ->orWhere(DB::raw("CONCAT(
                        COALESCE(usr.fname,''),' ',
                        COALESCE(usr.mname,''),' ',
                        COALESCE(usr.lname,'')
                  )"), 'like', "%{$this->search}%");
            });
        }

        /* =========================
         | COLUMN FILTERS
         ========================= */
        if ($this->columnFilters['stream'] !== '') {
            $query->where('ms.mas_stream_name', 'like', '%' . $this->columnFilters['stream'] . '%');
        }

        if ($this->columnFilters['college'] !== '') {
            $query->where('mc.mas_college_name', 'like', '%' . $this->columnFilters['college'] . '%');
        }

        if ($this->columnFilters['department'] !== '') {
            $query->where('mdt.mas_department_name', 'like', '%' . $this->columnFilters['department'] . '%');
        }

        if ($this->columnFilters['name'] !== '') {
            $query->where(DB::raw("CONCAT(
                COALESCE(usr.fname,''),' ',
                COALESCE(usr.mname,''),' ',
                COALESCE(usr.lname,'')
            )"), 'like', '%' . $this->columnFilters['name'] . '%');
        }

        if ($this->columnFilters['designation'] !== '') {
            $query->where('md.mas_designation_name', 'like', '%' . $this->columnFilters['designation'] . '%');
        }

        if ($this->columnFilters['mobile'] !== '') {
            $query->where('usr.user_mobile_number', 'like', '%' . $this->columnFilters['mobile'] . '%');
        }

        if ($this->columnFilters['type'] !== '') {
            $query->where('ed.examiner_details_type', $this->columnFilters['type']);
        }

        return $query;
    }

    /* =========================
     | SORTING
     ========================= */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatedColumnFilters(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /* =========================
     | EXPORT
     ========================= */
    public function exportExcel()
    {
        return Excel::download(
            new ExaminerExport($this->baseQuery()->get()),
            'exam.xlsx'
        );
    }

    /* =========================
     | EDIT
     ========================= */
    public function edit(int $id): void
    {
        $row = DB::table('examiner_details')->where('id', $id)->first();

        $this->editForm = [
            'id'   => $row->id,
            'rank' => $row->examiner_details_rank,
            'type' => $row->examiner_details_type,
        ];

        $this->showEditModal = true;
    }

    public function updateExaminer(): void
    {
        DB::table('examiner_details')
            ->where('id', $this->editForm['id'])
            ->update([
                'examiner_details_rank' => $this->editForm['rank'],
                'examiner_details_type' => $this->editForm['type'],
            ]);

        $this->showEditModal = false;
    }

    /* =========================
     | RENDER
     ========================= */
    public function render()
    {
        $allowedSorts = [
            'ms.mas_stream_name'      => 'ms.mas_stream_name',
            'mc.mas_college_name'     => 'mc.mas_college_name',
            'mdt.mas_department_name' => 'mdt.mas_department_name',
        ];

        $sortColumn = $allowedSorts[$this->sortField] ?? 'mc.mas_college_name';

        return view('livewire.exam.index', [
            'examiners' => $this->baseQuery()
                ->orderBy($sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}
