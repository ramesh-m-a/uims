<?php

namespace App\Http\Controllers\Master\StudentPerBatch;

use App\Http\Controllers\Controller;
use App\Models\Master\StudentPerBatch;
use Illuminate\Http\Request;

class StudentPerBatchController extends Controller
{
    public function index()
    {
        $rows = StudentPerBatch::orderBy('id', 'DESC')->get();
        return view('master.student-per-batch.index', compact('rows'));
    }

    public function create()
    {
        return view('master.student-per-batch.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|integer',
            'degree_id' => 'required|string|max:10',
            'total_number' => 'required|integer',
            'per_day' => 'required|integer',
        ]);

        StudentPerBatch::create([
            'mas_student_per_batch_subject_id' => $request->subject_id,
            'mas_student_per_batch_degree_id' => $request->degree_id,
            'mas_student_per_batch_total_number' => $request->total_number,
            'mas_student_per_batch_per_day' => $request->per_day,
            'created_by' => auth()->user()->name ?? null,
        ]);

        return redirect()
            ->route('master.student-per-batch.index')
            ->with('success', 'Added Successfully');
    }

    public function edit($id)
    {
        $row = StudentPerBatch::findOrFail($id);
        return view('master.student-per-batch.edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'total_number' => 'required|integer',
            'per_day' => 'required|integer',
        ]);

        $row = StudentPerBatch::findOrFail($id);

        $row->update([
            'mas_student_per_batch_total_number' => $request->total_number,
            'mas_student_per_batch_per_day' => $request->per_day,
            'updated_by' => auth()->user()->name ?? null,
        ]);

        return redirect()
            ->route('master.student-per-batch.index')
            ->with('success', 'Updated Successfully');
    }

    public function destroy($id)
    {
        StudentPerBatch::where('id', $id)->delete();

        return redirect()
            ->route('master.student-per-batch.index')
            ->with('success', 'Deleted Successfully');
    }
}
