<?php

namespace App\Livewire\Master\Config\Exam;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Common\Status;
use Illuminate\Database\Eloquent\Builder;

class BatchRangeTable extends MasterTableBase
{
    use MasterFormBase;

    protected string $modelClass = \App\Models\Master\Config\Exam\BatchRange::class;

    public string $pageRoute = '';

    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();
        $this->sortField = 'exam_center_name';
        $this->sortDirection = 'asc';
    }

    /* =========================
     | REQUIRED
     ========================= */
    protected function formDefaults(): array { return []; }
    protected function rules(): array { return []; }

    protected function permissionPrefix(): string
    {
        return 'master.config.exam.batch-range';
    }

    protected function configKey(): string
    {
        return 'master.config.exam.batch-range';
    }

    /* =====================================================
     | 🔥 SAFE BASE QUERY (DISABLE PARENT SEARCH)
     ===================================================== */
    protected function safeBaseQuery(): Builder
    {
        $originalSearch = $this->search;
        $this->search = '';               // 🔥 disable global search
        $query = parent::baseQuery();     // safe
        $this->search = $originalSearch; // restore
        return $query;
    }

    /* =====================================================
     | SORTING (SAME AS BATCH)
     ===================================================== */
    protected function applySorting($query)
    {
        return match ($this->sortField) {

            'exam_center_name' => $query
                ->orderBy('ec.mas_college_name', $this->sortDirection)
                ->orderBy('vc.attached_college_name')
                ->orderBy('br.mas_batch_range_from_date'),

            'attached_college_name' => $query
                ->orderBy('vc.attached_college_name', $this->sortDirection)
                ->orderBy('br.mas_batch_range_from_date'),

            'stream_name' => $query
                ->orderBy('s.mas_stream_name', $this->sortDirection),

            default => $query
                ->orderBy(
                    $this->sortField ?? 'br.mas_batch_range_from_date',
                    $this->sortDirection
                ),
        };
    }

    public function render()
    {
        /**
         * ------------------------------------------------------------
         * BUILD QUERY (NO SEARCH YET)
         * ------------------------------------------------------------
         */
        $query = $this->safeBaseQuery()
            ->from('mas_batch_range as br')
            ->join('mas_batch as b', 'b.id', '=', 'br.mas_batch_range_batch_id')
            ->leftJoin('mas_stream as s', 's.id', '=', 'b.mas_batch_stream_id')
            ->leftJoin('mas_college as ec', 'ec.id', '=', 'b.mas_batch_centre_id')
            ->join('vw_batch_attached_colleges as vc', fn ($j) =>
            $j->on('vc.batch_id', '=', 'b.id')
            )
            ->addSelect([
                'br.id',
                'br.mas_batch_range_batch_name',
                'br.mas_batch_range_from_date',
                'br.mas_batch_range_to_date',
                'br.mas_batch_range_status_id',

                's.mas_stream_name as stream_name',
                'ec.mas_college_name as exam_center_name',

                'vc.attached_college_id',
                'vc.attached_college_name',
                'vc.student_count as attached_college_students',
                'vc.status_id as attached_college_status',
            ]);

        /**
         * ------------------------------------------------------------
         * 🔥 GLOBAL SEARCH (ALIAS SAFE)
         * ------------------------------------------------------------
         */
        if ($this->search !== '') {
            $term = "%{$this->search}%";

            $query->where(function ($q) use ($term) {
                $q
                    ->orWhere('ec.mas_college_name', 'like', $term)
                    ->orWhere('vc.attached_college_name', 'like', $term)
                    ->orWhere('s.mas_stream_name', 'like', $term)
                    ->orWhere('br.mas_batch_range_batch_name', 'like', $term);
            });
        }

        // 🔥 ALIAS-SAFE COLUMN FILTERS
        foreach ($this->filters as $field => $value) {

            if ($value === '' || $value === null) {
                continue;
            }

            // Alias → real column
            if (isset($this->columnFilterMap[$field])) {
                $query->where(
                    $this->columnFilterMap[$field],
                    'like',
                    "%{$value}%"
                );
                continue;
            }
        }

        /**
         * ------------------------------------------------------------
         * SORT + PAGINATE
         * ------------------------------------------------------------
         */
        $query = $this->applySorting($query);
        $rows  = $query->paginate($this->perPage);

        return view('livewire.master.config.exam.batch-range-table', [
            'rows'           => $rows,
            'statuses' => Status::active()->orderBy('mas_status_name')->get(),
            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'columns'        => $this->columns(),
            'filters'        => $this->filters,
            'sortField'      => $this->sortField,
            'sortDirection'  => $this->sortDirection,
            'statusField'    => $this->statusField(),
            'pageRoute'      => $this->pageRoute,
            'breadcrumbMode' => $this->breadcrumbMode(),
        ]);
    }

    protected array $columnFilterMap = [
        'exam_center_name'       => 'ec.mas_college_name',
        'attached_college_name' => 'vc.attached_college_name',
        'stream_name'           => 's.mas_stream_name',
    ];

    public function exportQuery()
    {
        $query = $this->safeBaseQuery()
            ->from('mas_batch_range as br')
            ->join('mas_batch as b', 'b.id', '=', 'br.mas_batch_range_batch_id')
            ->leftJoin('mas_stream as s', 's.id', '=', 'b.mas_batch_stream_id')
            ->leftJoin('mas_college as ec', 'ec.id', '=', 'b.mas_batch_centre_id')
            ->join('vw_batch_attached_colleges as vc', fn ($j) =>
            $j->on('vc.batch_id', '=', 'b.id')
            )
            ->addSelect([
                'br.*',
                's.mas_stream_name as stream_name',
                'ec.mas_college_name as exam_center_name',
                'vc.attached_college_name',
                'vc.student_count',
                'vc.status_id as attached_college_status',
            ]);

        // 🔥 reuse SAME sorting logic
        return $this->applySorting($query);
    }

}
