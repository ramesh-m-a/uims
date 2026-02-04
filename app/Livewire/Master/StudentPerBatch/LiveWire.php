<?php

namespace App\Livewire\Master\StudentPerBatch;

use App\Models\Master\Common\Degree;
use App\Models\Master\StudentPerBatch;
use App\Models\Master\Subject;
use Livewire\Component;
use Livewire\WithPagination;

// if listing many

class LiveWire extends Component
{
    use WithPagination;

    public $subject_id;
    public $degree_id;
    public $total_number;
    public $per_day;
    public $editingId = null;
    public $search = '';

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'subject_id' => 'required|integer|exists:mas_subjects,id',
            'degree_id' => 'required|string|max:50|exists:mas_degrees,mas_degree_id',
            'total_number' => 'required|integer|min:1',
            'per_day' => 'required|integer|min:1',
        ];
    }

    protected $messages = [
        'subject_id.required' => 'Please select a subject.',
        'subject_id.exists' => 'Selected subject does not exist.',
        'degree_id.required' => 'Please select a degree.',
        'degree_id.exists' => 'Selected degree does not exist.',
        'total_number.required' => 'Total students is required.',
        'per_day.required' => 'Per day count is required.',
        'total_number.integer' => 'Total students must be a number.',
        'per_day.integer' => 'Per day must be a number.',
    ];

    public function mount()
    {
        $this->resetInput();
    }

    public function render()
    {
        $query = StudentPerBatch::with(['subject', 'degree'])
            ->when($this->search, fn($q) => $q->whereHas('subject', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"))
                ->orWhere('mas_student_per_batch_degree_id', 'like', "%{$this->search}%")
            )
            ->orderBy('id', 'desc');

        $rows = $query->paginate(10);

        return view('livewire.master.student-per-batch.live-wire', [
            'rows' => $rows,
            'subjects' => Subject::orderBy('name')->get(),
            'degrees' => Degree::orderBy('name')->get(),
        ]);
    }

    public function resetInput()
    {
        $this->subject_id = null;
        $this->degree_id = null;
        $this->total_number = null;
        $this->per_day = null;
        $this->editingId = null;
    }

    public function store()
    {
        $this->validate();

        StudentPerBatch::create([
            'mas_student_per_batch_subject_id' => $this->subject_id,
            'mas_student_per_batch_degree_id' => $this->degree_id,
            'mas_student_per_batch_total_number' => $this->total_number,
            'mas_student_per_batch_per_day' => $this->per_day,
            'created_by' => auth()->user()->name ?? null,
        ]);

        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Added Successfully']);

        $this->resetInput();
        $this->resetPage();
    }

    public function edit($id)
    {
        $row = StudentPerBatch::findOrFail($id);
        $this->editingId = $id;
        $this->subject_id = $row->mas_student_per_batch_subject_id;
        $this->degree_id = $row->mas_student_per_batch_degree_id;
        $this->total_number = $row->mas_student_per_batch_total_number;
        $this->per_day = $row->mas_student_per_batch_per_day;

        $this->dispatchBrowserEvent('openModal'); // client can listen to open modal
    }

    public function update()
    {
        $this->validate();

        if (!$this->editingId) return;

        $row = StudentPerBatch::findOrFail($this->editingId);
        $row->update([
            'mas_student_per_batch_subject_id' => $this->subject_id,
            'mas_student_per_batch_degree_id' => $this->degree_id,
            'mas_student_per_batch_total_number' => $this->total_number,
            'mas_student_per_batch_per_day' => $this->per_day,
            'updated_by' => auth()->user()->name ?? null,
        ]);

        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Updated Successfully']);
        $this->resetInput();
    }

    public function delete($id)
    {
        StudentPerBatch::where('id', $id)->delete();
        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Deleted Successfully']);
        $this->resetPage();
    }
}
