<?php

namespace App\Livewire\Master\StudentPerBatch;

use App\Models\Master\Common\Degree;
use App\Models\Master\StudentPerBatch;
use App\Models\Master\Subject;
use Livewire\Component;
use Livewire\WithPagination;

class Datatable extends Component
{
    use WithPagination;

    // table controls
    public string $search = '';
    public string $sortField = 'id';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    // form fields
    public $editingId = null;
    public $subject_id = null;
    public $degree_id = null;
    public $total_number = null;
    public $per_day = null;

    // confirm delete
    public $confirmingDeleteId = null;

    protected $queryString = ['search', 'sortField', 'sortDirection', 'perPage'];

    protected function rules(): array
    {
        return [
            'subject_id' => 'required|integer|exists:mas_subjects,id',
            'degree_id'  => 'required|string|max:50|exists:mas_degrees,mas_degree_id',
            'total_number' => 'required|integer|min:1',
            'per_day' => 'required|integer|min:1',
        ];
    }

    protected $messages = [
        'subject_id.required' => 'Please select a subject.',
        'subject_id.exists' => 'Selected subject is invalid.',
        'degree_id.required' => 'Please select a degree.',
        'degree_id.exists' => 'Selected degree is invalid.',
        'total_number.required' => 'Total students is required.',
        'per_day.required' => 'Per day is required.',
    ];

    // reset pagination when searching or changing perPage
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // sort toggler
    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function mount()
    {
        $this->perPage = 10;
    }

    public function render()
    {
        // Base query with relations
        $query = StudentPerBatch::with(['subject', 'degree']);

        // Search:
        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('subject', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('degree', fn($q3) => $q3->where('name', 'like', "%{$s}%"))
                    ->orWhere('mas_student_per_batch_total_number', 'like', "%{$s}%")
                    ->orWhere('mas_student_per_batch_per_day', 'like', "%{$s}%")
                    ->orWhere('mas_student_per_batch_status_id', 'like', "%{$s}%");
            });
        }

        // Sorting - ensure only allowed columns
        $allowedSorts = [
            'id',
            'mas_student_per_batch_total_number',
            'mas_student_per_batch_per_day',
            'mas_student_per_batch_status_id'
        ];

        if ($this->sortField === 'subject') {
            $query->join('mas_subject as s', 's.id', '=', 'mas_student_per_batch.mas_student_per_batch_subject_id')
                ->orderBy('s.mas_subject_name', $this->sortDirection)
                ->select('mas_student_per_batch.*');
        } elseif ($this->sortField === 'degree') {
            $query->join('mas_degree as d', 'd.mas_degree_id', '=', 'mas_student_per_batch.mas_student_per_batch_degree_id')
                ->orderBy('d.mas_degree_name', $this->sortDirection)
                ->select('mas_student_per_batch.*');
        } elseif (in_array($this->sortField, $allowedSorts)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $rows = $query->paginate($this->perPage);

        return view('livewire.master.student-per-batch.data-table', [
            'rows' => $rows,
            'subjects' => Subject::orderBy('mas_subject_name')->get(),
            'degrees' => Degree::orderBy('mas_degree_name')->get(),
        ]);
    }

    // create new record
    public function createShowModal()
    {
        $this->resetValidation();
        $this->resetForm();
        // use Livewire facade emit to be robust
        Livewire::emit('openModal', 'formModal');
    }

    public function editShowModal($id)
    {
        $this->resetValidation();
        $row = StudentPerBatch::findOrFail($id);
        $this->editingId = $id;
        $this->subject_id = $row->mas_student_per_batch_subject_id;
        $this->degree_id = $row->mas_student_per_batch_degree_id;
        $this->total_number = $row->mas_student_per_batch_total_number;
        $this->per_day = $row->mas_student_per_batch_per_day;

        \Livewire\Livewire::emit('openModal', 'formModal');
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $row = StudentPerBatch::findOrFail($this->editingId);
            $row->update([
                'mas_student_per_batch_subject_id' => $this->subject_id,
                'mas_student_per_batch_degree_id' => $this->degree_id,
                'mas_student_per_batch_total_number' => $this->total_number,
                'mas_student_per_batch_per_day' => $this->per_day,
                'updated_by' => auth()->user()->name ?? null,
            ]);

            \Livewire\Livewire::emit('notify', ['type' => 'success', 'message' => 'Updated Successfully']);
        } else {
            StudentPerBatch::create([
                'mas_student_per_batch_subject_id' => $this->subject_id,
                'mas_student_per_batch_degree_id' => $this->degree_id,
                'mas_student_per_batch_total_number' => $this->total_number,
                'mas_student_per_batch_per_day' => $this->per_day,
                'created_by' => auth()->user()->name ?? null,
            ]);

            \Livewire\Livewire::emit('notify', ['type' => 'success', 'message' => 'Added Successfully']);
        }

        $this->resetForm();
        \Livewire\Livewire::emit('closeModal', 'formModal');
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        \Livewire\Livewire::emit('openModal', 'confirmDeleteModal');
    }

    public function delete()
    {
        if (!$this->confirmingDeleteId) return;

        StudentPerBatch::where('id', $this->confirmingDeleteId)->delete();

        \Livewire\Livewire::emit('notify', ['type' => 'success', 'message' => 'Deleted Successfully']);
        $this->confirmingDeleteId = null;
        \Livewire\Livewire::emit('closeModal', 'confirmDeleteModal');
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->subject_id = null;
        $this->degree_id = null;
        $this->total_number = null;
        $this->per_day = null;
    }
}
