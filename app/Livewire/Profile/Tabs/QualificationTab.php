<?php

namespace App\Livewire\Profile\Tabs;

use Livewire\Component;
use App\Models\UserProfileDraft;
use App\Models\Master\Config\Academic\Degree;
use App\Models\Master\Config\Academic\Specialisation;
use Illuminate\Support\Facades\Auth;

class QualificationTab extends Component
{
    public UserProfileDraft $draft;

    public array $qualification = [];
    public bool $showForm = false;
    public ?int $editingIndex = null;
    public array $degreeMap = [];
    public array $specialisationMap = [];
    public array $allDegrees = [];

    public array $form = [
        'degree_id' => null,
        'specialisation_id' => null,
        'institution' => null,
        'year_of_exam' => null,
        'year_of_award' => null,
        'registration_no' => null,
    ];

    public array $degrees = [];
    public array $specialisations = [];

    /**
     * 0 = NONE
     * 1 = OPTIONAL
     * 2 = REQUIRED
     */
    public int $specialisationMode = 0;

    public function mount(UserProfileDraft $draft): void
    {
        $this->draft = $draft;

        $user = Auth::user();

        if (! $user || ! $user->user_stream_id) {
            abort(403, 'User stream not defined - QualificationTab.');
        }

        $this->degreeMap = Degree::pluck('mas_degree_code', 'id')->toArray();

        $this->specialisationMap = Specialisation::pluck(
            'mas_specialisation_name',
            'id'
        )->toArray();

        $this->allDegrees = Degree::query()
            ->where('mas_degree_status_id', 1)
            ->whereHas('streams', function ($q) use ($user) {
                $q->where('mas_stream.id', $user->user_stream_id);
            })
            ->orderBy('mas_degree_name')
            ->get(['id', 'mas_degree_code'])
            ->map(fn ($d) => [
                'id'   => $d->id,
                'name' => $d->mas_degree_code,
            ])
            ->toArray();

        $this->qualification = $draft->data['qualification'] ?? [];

        $this->refreshAvailableDegrees();
        $this->hydrateQualificationNames();
    }

    public function updatedFormDegreeId($degreeId): void
    {
        if (! $degreeId) {
            $this->resetSpecialisation();
            return;
        }

        $degree = Degree::with('specialisations')->find($degreeId);

        if (! $degree) {
            $this->resetSpecialisation();
            return;
        }

        $this->specialisationMode = (int) $degree->mas_degree_specialisation_mode;

        if ($this->specialisationMode === 0) {
            $this->resetSpecialisation();
            return;
        }

        $this->specialisations = $degree->specialisations()
            ->where('mas_degree_specialisation_status_id', 1)
            ->orderBy('mas_specialisation_name')
            ->get()
            ->toArray();
    }

    private function resetSpecialisation(): void
    {
        $this->specialisations = [];
        $this->specialisationMode = 0;
        $this->form['specialisation_id'] = null;
    }

    public function add(): void
    {
        $this->reset('form', 'editingIndex', 'specialisations');
        $this->specialisationMode = 0;
        $this->refreshAvailableDegrees();
        $this->showForm = true;
    }

    public function edit(int $index): void
    {
        $this->editingIndex = $index;

        $this->form = [
            'degree_id'         => (int) $this->qualification[$index]['degree_id'],
            'specialisation_id' => $this->qualification[$index]['specialisation_id'] ?? null,
            'institution'       => $this->qualification[$index]['institution'] ?? null,
            'year_of_exam'      => $this->qualification[$index]['year_of_exam'] ?? null,
            'year_of_award'     => $this->qualification[$index]['year_of_award'] ?? null,
            'registration_no'   => $this->qualification[$index]['registration_no'] ?? null,
        ];

        $this->refreshAvailableDegrees();

        if ($this->form['degree_id']) {
            $this->updatedFormDegreeId($this->form['degree_id']);
        }

        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->reset('form', 'editingIndex', 'specialisations');
        $this->specialisationMode = 0;
        $this->showForm = false;
    }

    public function saveForm(): void
    {
        $this->validate([
            'form.degree_id' => 'required|integer',

            'form.specialisation_id' => $this->specialisationMode === 2
                ? 'required|integer'
                : 'nullable',

            'form.institution' => [
                'required',
                'regex:/^[A-Za-z0-9,\s]+$/',
            ],

            'form.year_of_exam' => [
                'required',
                'digits:4',
                'integer',
                'min:1900',
                'max:' . date('Y'),
            ],

            'form.year_of_award' => [
                'required',
                'digits:4',
                'integer',
                'gte:form.year_of_exam',
                'max:' . date('Y'),
            ],

            'form.registration_no' => [
                'nullable',
                'regex:/^[A-Za-z0-9,\s]+$/',
            ],
        ], $this->messages());

        if ($this->specialisationMode === 0) {
            $this->form['specialisation_id'] = null;
        }

        if ($this->editingIndex !== null) {
            $this->qualification[$this->editingIndex] = $this->form;
        } else {
            $this->qualification[] = $this->form;
        }

        $this->refreshAvailableDegrees();
        $this->hydrateQualificationNames();

        $data = $this->draft->data;
        $data['qualification'] = $this->qualification;

        $completed = $this->draft->completed_tabs ?? [];
        if (! in_array('qualification', $completed, true)) {
            $completed[] = 'qualification';
        }

        $this->draft->update([
            'data'           => $data,
            'completed_tabs' => $completed,
        ]);

        $this->reset('form', 'editingIndex', 'specialisations');
        $this->specialisationMode = 0;
        $this->showForm = false;
    }

    public function save(): void
    {
        if (count($this->qualification) === 0) {
            $this->addError('qualification', 'At least one qualification is required.');
            return;
        }

        $this->dispatch('switch-tab', tab: 'work');
    }

    public function render()
    {
        return view('livewire.profile.tabs.qualification-tab');
    }

    protected function messages(): array
    {
        return [
            'form.degree_id.required' => 'The Degree is required.',
            'form.specialisation_id.required' => 'The Specialisation is required.',
            'form.institution.required' => 'The Name of the Institution is required.',
            'form.year_of_exam.required' => 'The Year of Exam is required.',
            'form.year_of_award.required' => 'The Year of Award is required.',
        ];
    }

    private function hydrateQualificationNames(): void
    {
        $this->qualification = collect($this->qualification)
            ->map(function ($row) {
                $degree = Degree::find($row['degree_id']);

                if (! $degree) {
                    $specialisation = '-';
                } else {
                    $mode = (int) $degree->mas_degree_specialisation_mode;

                    if ($mode === 0) {
                        $specialisation = 'NONE';
                    } else {
                        $specialisation = $this->specialisationMap[$row['specialisation_id']] ?? '-';
                    }
                }

                return array_merge($row, [
                    'degree_code'         => $this->degreeMap[$row['degree_id']] ?? '-',
                    'specialisation_name' => $specialisation,
                ]);
            })
            ->values()
            ->toArray();
    }

    private function refreshAvailableDegrees(): void
    {
        $usedDegreeIds = collect($this->qualification)
            ->pluck('degree_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();

        if ($this->editingIndex !== null) {
            $currentDegree = (int) ($this->qualification[$this->editingIndex]['degree_id'] ?? 0);
            $usedDegreeIds = array_diff($usedDegreeIds, [$currentDegree]);
        }

        $this->degrees = collect($this->allDegrees)
            ->reject(fn ($d) => in_array((int) $d['id'], $usedDegreeIds, true))
            ->values()
            ->toArray();
    }

    public function delete(int $index): void
    {
        unset($this->qualification[$index]);
        $this->qualification = array_values($this->qualification);

        $this->refreshAvailableDegrees();
        $this->hydrateQualificationNames();

        $data = $this->draft->data;
        $data['qualification'] = $this->qualification;
        $this->draft->update(['data' => $data]);
    }
}
