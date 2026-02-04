<?php

namespace App\Livewire\Master\StudentPerBatch;

use App\Models\Master\StudentPerBatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // public properties (form fields)
    public ?int $mas_student_per_batch_id = null;
    public ?int $mas_student_per_batch_subject_id = null;
    public ?string $mas_student_per_batch_degree_id = null; // VARCHAR(10)
    public ?int $mas_student_per_batch_total_number = null;
    public ?int $mas_student_per_batch_per_day = null;
    public ?int $mas_student_per_batch_status_id = 50; // default 50 (per DDL)

    // UI state
    public string $search = '';
    public int $perPage = 10;
    public bool $showModal = false;
    public $confirmingDeleteId = null;

    // always expose items so blade doesn't crash
    public $items = null;

    protected $queryString = ['search'];

    protected $listeners = [
        'refreshList' => '$refresh',
    ];

    public function mount()
    {
        // make sure defaults align with DDL
        $this->mas_student_per_batch_status_id = $this->mas_student_per_batch_status_id ?? 50;
    }

    protected function rules()
    {
        return [
            'mas_student_per_batch_subject_id' => ['required','integer'],
            'mas_student_per_batch_degree_id'  => ['required','string','max:10'],
            'mas_student_per_batch_total_number'=> ['required','integer','min:0'],
            'mas_student_per_batch_per_day'    => ['required','integer','min:0'],
            'mas_student_per_batch_status_id'  => ['required','integer'],
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->mas_student_per_batch_id = null;
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $record = StudentPerBatch::findOrFail($id);

        $this->mas_student_per_batch_id = $record->id;
        $this->mas_student_per_batch_subject_id = (int)$record->mas_student_per_batch_subject_id;
        $this->mas_student_per_batch_degree_id = (string)$record->mas_student_per_batch_degree_id;
        $this->mas_student_per_batch_total_number = (int)$record->mas_student_per_batch_total_number;
        $this->mas_student_per_batch_per_day = (int)$record->mas_student_per_batch_per_day;
        $this->mas_student_per_batch_status_id = (int)$record->mas_student_per_batch_status_id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // check unique composite (subject + degree) for create & update
        $sid = $this->mas_student_per_batch_subject_id;
        $did = $this->mas_student_per_batch_degree_id;
        $existing = StudentPerBatch::where('mas_student_per_batch_subject_id', $sid)
            ->where('mas_student_per_batch_degree_id', $did);

        if ($this->mas_student_per_batch_id) {
            $existing->where('id', '!=', $this->mas_student_per_batch_id);
        }

        if ($existing->exists()) {
            $this->addError('mas_student_per_batch_subject_id', 'A record with this Subject + Degree already exists.');
            return;
        }

        $data = [
            'mas_student_per_batch_subject_id' => $sid,
            'mas_student_per_batch_degree_id' => $did,
            'mas_student_per_batch_total_number' => $this->mas_student_per_batch_total_number,
            'mas_student_per_batch_per_day' => $this->mas_student_per_batch_per_day,
            'mas_student_per_batch_status_id' => $this->mas_student_per_batch_status_id,
            'updated_by' => (string) Auth::id(),
        ];

        if ($this->mas_student_per_batch_id) {
            $row = StudentPerBatch::findOrFail($this->mas_student_per_batch_id);
            $row->update($data);
            $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Updated successfully.']);
        } else {
            $data['created_by'] = (string) Auth::id();
            StudentPerBatch::create($data);
            $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Created successfully.']);
        }

        $this->showModal = false;
        $this->resetForm();
        $this->emit('refreshList');
    }

    public function confirmDelete(int $id)
    {
        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('show-delete-confirm');
    }

    public function delete()
    {
        if (!$this->confirmingDeleteId) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'No record selected.']);
            return;
        }

        StudentPerBatch::where('id', $this->confirmingDeleteId)->delete();
        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Deleted successfully.']);
        $this->emit('refreshList');
    }

    public function resetForm()
    {
        $this->mas_student_per_batch_subject_id = null;
        $this->mas_student_per_batch_degree_id = null;
        $this->mas_student_per_batch_total_number = null;
        $this->mas_student_per_batch_per_day = null;
        $this->mas_student_per_batch_status_id = 50;
        $this->resetValidation();
    }

    public function render()
    {
        $query = StudentPerBatch::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('mas_student_per_batch_total_number', 'like', '%' . $this->search . '%')
                    ->orWhere('mas_student_per_batch_per_day', 'like', '%' . $this->search . '%')
                    ->orWhere('mas_student_per_batch_degree_id', 'like', '%' . $this->search . '%');
            });
        }

        $perPage = intval($this->perPage) > 0 ? intval($this->perPage) : 10;
        $items = $query->orderByDesc('id')->paginate($perPage);
        $this->items = $items;

        return view('livewire.master.student-per-batch.index');
    }
}
