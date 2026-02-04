<?php

namespace App\Livewire\Profile;

use App\Models\Teacher\BasicDetails;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher\SubjectDetails;
use App\Models\Master\Config\Academic\Subject;
use App\Models\Master\Common\Year;
use Livewire\WithPagination;

class SubjectDetailsTable extends Component
{
    use WithPagination;
    public array $subjects = [];
    public array $years = [];

    public array $rows = [];

    public bool $showForm = false;
    public ?int $editingId = null;

    public array $form = [
        'mas_subject_id' => null,
        'mas_year_id'    => null,
    ];

    public function mount(): void
    {
        $user = Auth::user();

       /* if (! $user->basic_details_id || ! $user->user_stream_id) {
            abort(403, 'Profile incomplete.');
        }*/

        $this->subjects = Subject::query()
            ->where('mas_subject_status_id', 1)
            ->where('mas_subject_stream_id', $user->user_stream_id)
            ->orderBy('mas_subject_name')
            ->get(['id', 'mas_subject_name'])
            ->toArray();

        $this->years = Year::query()
            ->where('mas_year_status_id', 1)
            ->orderByDesc('mas_year_year')
            ->get(['id', 'mas_year_year'])
            ->toArray();

        $this->loadRows();
    }

    private function loadRows(): void
    {
        $user = Auth::user();

        $basicDetailsId = BasicDetails::where('basic_details_user_id', $user->id)
            ->value('id');

        $this->rows = SubjectDetails::query()
            ->where('basic_details_id', $basicDetailsId)
            ->with(['subject:id,mas_subject_name', 'year:id,mas_year_year'])
            ->latest()
            ->get()
            ->map(fn ($r) => [
                'id'      => $r->id,
                'subject' => $r->subject->mas_subject_name ?? '-',
                'year'    => $r->year->mas_year_year ?? '-',
                'mas_subject_id' => $r->mas_subject_id,
                'mas_year_id'    => $r->mas_year_id,
            ])
            ->toArray();
    }

    public function add(): void
    {
        $this->reset('form', 'editingId');
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $user = Auth::user();

        $basicDetailsId = BasicDetails::where('basic_details_user_id', $user->id)
            ->value('id');

        $row = SubjectDetails::where('id', $id)
            ->where('basic_details_id', $basicDetailsId)
            ->firstOrFail();

        $this->editingId = $id;
        $this->form = [
            'mas_subject_id' => $row->mas_subject_id,
            'mas_year_id'    => $row->mas_year_id,
        ];

        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->reset('form', 'editingId');
        $this->showForm = false;
    }

    public function saveForm(): void
    {
        $this->validate([
            'form.mas_subject_id' => 'required|integer',
            'form.mas_year_id'    => 'required|integer',
        ]);

        $user = Auth::user();

        $basicDetailsId = BasicDetails::where('basic_details_user_id', $user->id)
            ->value('id');

        // Enforce uniqueness
        $exists = SubjectDetails::query()
            ->where('basic_details_id', $basicDetailsId)
            ->where('mas_subject_id', $this->form['mas_subject_id'])
            ->where('mas_year_id', $this->form['mas_year_id'])
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            $this->addError('duplicate', 'Subject already exists for this year.');
            return;
        }

        SubjectDetails::updateOrCreate(
            ['id' => $this->editingId],
            [
                'basic_details_id' => $basicDetailsId,
                'mas_subject_id'   => $this->form['mas_subject_id'],
                'mas_year_id'      => $this->form['mas_year_id'],
            ]
        );

        $this->cancel();
        $this->loadRows();
    }

    public function delete(int $id): void
    {
        SubjectDetails::where('id', $id)
            ->where('basic_details_id', Auth::user()->basic_details_id)
            ->delete();

        $this->loadRows();
    }

    public function render()
    {
        return view('livewire.profile.subject-details-table');
    }
}
