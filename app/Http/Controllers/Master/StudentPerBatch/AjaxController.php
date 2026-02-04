<?php

namespace App\Http\Controllers\Master\StudentPerBatch;


use App\Http\Controllers\Controller;
use App\Models\Master\StudentPerBatch;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function index(Request $request)
    {
        // columns mapping for ordering (DataTables column index -> column name)
        $columns = [0 => 'id', 1 => 'mas_student_per_batch_subject_id', 2 => 'mas_student_per_batch_degree_id', 3 => 'mas_student_per_batch_total_number', 4 => 'mas_student_per_batch_per_day', 5 => 'mas_student_per_batch_status_id',];

        $query = StudentPerBatch::with(['subject', 'degree']);

        // global search (skip first and last columns on UI - handled in JS by disabling searchable)
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")->orWhereHas('subject', function ($s) use ($search) {
                        $s->where('name', 'like', "%{$search}%");
                    })->orWhere('mas_student_per_batch_degree_id', 'like', "%{$search}%")->orWhere('mas_student_per_batch_total_number', 'like', "%{$search}%")->orWhere('mas_student_per_batch_per_day', 'like', "%{$search}%")->orWhere('mas_student_per_batch_status_id', 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        // ordering
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $query->orderBy($orderColumn, $orderDir);

        // paging
        $start = intval($request->input('start', 0));
        $length = intval($request->input('length', 10));

        $rows = $query->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $row) {
            $data[] = [$row->id, $row->subject?->name ?? $row->mas_student_per_batch_subject_id, $row->degree?->name ?? $row->mas_student_per_batch_degree_id, $row->mas_student_per_batch_total_number, $row->mas_student_per_batch_per_day, $row->mas_student_per_batch_status_id, // actions (edit/delete)
                view('master.student-per-batch.partials.actions', ['row' => $row])->render()];
        }

        return response()->json(["draw" => intval($request->input('draw')), "recordsTotal" => StudentPerBatch::count(), "recordsFiltered" => $totalFiltered, "data" => $data,]);
    }
}
