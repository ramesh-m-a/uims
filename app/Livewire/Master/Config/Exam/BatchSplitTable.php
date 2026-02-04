<?php

namespace App\Livewire\Master\Config\Exam;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use Illuminate\Support\Facades\DB;

class BatchSplitTable extends MasterTableBase
{
    use MasterFormBase;

    protected string $modelClass = \App\Models\Master\Config\Exam\BatchSplit::class;

    public string $pageRoute = '';

    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();
    }

    protected function formDefaults(): array
    {
        return [];
    }

    protected function rules(): array
    {
        return [];
    }

    protected function permissionPrefix(): string
    {
        return 'master.config.exam.batch-split';
    }

    protected function configKey(): string
    {
        return 'master.config.exam.batch-split';
    }

    public function render()
    {
        $query = $this->baseQuery()
            ->from('mas_batch_split as bs')

            ->leftJoin('mas_batch as b', 'b.id', '=', 'bs.mas_batch_split_batch_id')
            ->leftJoin('mas_stream as s', 's.id', '=', 'b.mas_batch_stream_id')

            // Exam centre (main)
            ->leftJoin('mas_college as ec', 'ec.id', '=', 'b.mas_batch_centre_id')

            // Attached centre for this split
            ->leftJoin('mas_college as ac', 'ac.id', '=', 'bs.mas_batch_split_centre_id')

            // Name from range
            ->leftJoin('mas_batch_range as br', 'br.mas_batch_range_batch_split_id', '=', 'bs.id')

            ->addSelect([
                'bs.*',

                // expose real columns so search/filter engine works
                'br.mas_batch_range_batch_name',
                's.mas_stream_name',
                'ec.mas_college_name',
                'ac.mas_college_name',

                // aliases used by config/blade
                'br.mas_batch_range_batch_name as mas_batch_split_batch_name',
                's.mas_stream_name as stream_name',
                'ec.mas_college_name as exam_center_name',
                DB::raw("COALESCE(ac.mas_college_name, '—') as attached_colleges"),
            ]);

        // -------------------------
        // Default + safe sorting
        // -------------------------
        $sortField = $this->sortField ?? 'exam_center_name';
        $sortDirection = $this->sortDirection ?? 'asc';

        if ($sortField === 'exam_center_name') {
            $query->orderBy('ec.mas_college_name', $sortDirection);
        } elseif ($sortField === 'attached_colleges') {
            $query->orderBy('ac.mas_college_name', $sortDirection);
        } elseif ($sortField === 'stream_name') {
            $query->orderBy('s.mas_stream_name', $sortDirection);
        } elseif ($sortField === 'mas_batch_split_batch_name') {
            $query->orderBy('br.mas_batch_range_batch_name', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $rows = $query->paginate($this->perPage);

        return view('livewire.master.config.exam.batch-split-table', [
            'rows'          => $rows,
            'search'        => $this->search,
            'perPage'       => $this->perPage,
            'columns'       => $this->columns(),
            'filters'       => $this->filters,
            'sortField'     => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'statusField'   => $this->statusField(),
            'pageRoute'     => $this->pageRoute,
            'breadcrumbMode'=> $this->breadcrumbMode(),
        ]);
    }

    public function renderold()
    {
        $rows = DB::table('mas_batch_split as bs')
            ->leftJoin('mas_batch as b', 'b.id', '=', 'bs.mas_batch_split_batch_id')
            ->leftJoin('mas_stream as s', 's.id', '=', 'b.mas_batch_stream_id')

            // Exam centre (main)
            ->leftJoin('mas_college as ec', 'ec.id', '=', 'b.mas_batch_centre_id')

            // Attached centre for this split
            ->leftJoin('mas_college as ac', 'ac.id', '=', 'bs.mas_batch_split_centre_id')

            // Name from range
            ->leftJoin('mas_batch_range as br', 'br.mas_batch_range_batch_split_id', '=', 'bs.id')

            ->select([
                'bs.*',
                'br.mas_batch_range_batch_name as mas_batch_split_batch_name',
                's.mas_stream_name as stream_name',
                'ec.mas_college_name as exam_center_name',

                // ✅ Only college name, no count
                DB::raw("COALESCE(ac.mas_college_name, '—') as attached_colleges"),
            ])

            ->orderBy($this->sortField ?? 'bs.id', $this->sortDirection ?? 'desc')
            ->paginate($this->perPage);

        return view('livewire.master.config.exam.batch-split-table', [
            'rows'          => $rows,
            'search'        => $this->search,
            'perPage'       => $this->perPage,
            'columns'       => $this->columns(),
            'filters'       => $this->filters,
            'sortField'     => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'statusField'   => $this->statusField(),
            'pageRoute'     => $this->pageRoute,
            'breadcrumbMode'=> $this->breadcrumbMode(),
        ]);
    }
}
