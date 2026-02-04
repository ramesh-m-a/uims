<?php

namespace App\Livewire\Profile\Tabs;

use Livewire\Component;
use App\Models\UserProfileDraft;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkTab extends Component
{
    public UserProfileDraft $draft;

    public array $work = [];
    public bool $showForm = false;
    public ?int $editingIndex = null;

    public array $designations = [];
    public array $designationMap = [];

    public array $form = [
        'designation_id'       => null,
        'institution'          => null,
        'date_of_appointment'  => null,
        'date_of_joining'      => null,
        'from_date'            => null,
        'to_date'              => null,
        'is_current'           => false,
    ];

    public function mount(UserProfileDraft $draft): void
    {
        $this->draft = $draft;
        $this->work  = $draft->data['work'] ?? [];

        $this->designations = DB::table('mas_designation')
            ->where('mas_designation_status_id', 1)
            ->orderBy('mas_designation_name')
            ->get(['id', 'mas_designation_name'])
            ->map(fn ($d) => [
                'id'   => $d->id,
                'name' => strtoupper($d->mas_designation_name),
            ])
            ->toArray();

        $this->designationMap = collect($this->designations)
            ->pluck('name', 'id')
            ->toArray();

        $this->hydrateWorkNames();
    }

    public function add(): void
    {
        $this->reset('form', 'editingIndex');
        $this->form['is_current'] = false;
        $this->showForm = true;
    }

    public function edit(int $index): void
    {
        $row = $this->work[$index];

        $this->form = [
            'designation_id'      => $row['designation_id'] ?? null,
            'institution'         => $row['institution'] ?? null,

            // IMPORTANT: keep dates in Y-m-d for input[type=date]
            'date_of_appointment' => $row['date_of_appointment'] ?? null,
            'date_of_joining'     => $row['date_of_joining'] ?? null,
            'from_date'           => $row['from_date'] ?? null,
            'to_date'             => $row['to_date'] ?? null,

            'is_current'          => (bool) ($row['is_current'] ?? false),
        ];

        $this->editingIndex = $index;
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->reset('form', 'editingIndex');
        $this->showForm = false;
    }

    public function delete(int $index): void
    {
        unset($this->work[$index]);
        $this->work = array_values($this->work);
        $this->persist();
    }

    public function saveForm(): void
    {
        $this->validate([
            'form.designation_id' => 'required|integer',
            'form.institution'    => 'required|string|max:255',

            'form.date_of_appointment' => 'nullable|date',
            'form.date_of_joining'     => 'nullable|date|after_or_equal:form.date_of_appointment',
            'form.from_date'           => 'required|date',
            'form.to_date'             => 'nullable|date|after_or_equal:form.from_date',
        ], $this->messages());

        if ($this->form['is_current']) {
            $this->form['to_date'] = null;
        }

        if ($this->editingIndex !== null) {
            $this->work[$this->editingIndex] = $this->form;
        } else {
            $this->work[] = $this->form;
        }

        $this->hydrateWorkNames();
        $this->persist();

        $this->reset('form', 'editingIndex');
        $this->showForm = false;
    }

    private function persist(): void
    {
        $data = $this->draft->data;
        $data['work'] = $this->work;

        $completed = $this->draft->completed_tabs ?? [];
        if (! in_array('work', $completed, true)) {
            $completed[] = 'work';
        }

        $this->draft->update([
            'data'           => $data,
            'completed_tabs' => $completed,
        ]);
    }

    private function hydrateWorkNames(): void
    {
        $this->work = collect($this->work)
            ->map(function ($row) {
                return array_merge($row, [
                    'designation_name' =>
                        $this->designationMap[$row['designation_id']] ?? '-',
                ]);
            })
            ->values()
            ->toArray();
    }

    public function experience(array $row): string
    {
        if (empty($row['from_date'])) {
            return '-';
        }

        try {
            $from = Carbon::parse($row['from_date'])->startOfDay();

            // If current job → today
            if (!empty($row['is_current'])) {
                $to = Carbon::today()->startOfDay();
            }
            // Else use to_date if exists
            elseif (!empty($row['to_date'])) {
                $to = Carbon::parse($row['to_date'])->startOfDay();
            }
            else {
                return '-';
            }

            // Prevent negative ranges
            if ($to->lessThan($from)) {
                return '-';
            }

            // Exact diff
            $diff = $from->diff($to);

            $parts = [];

            if ($diff->y > 0) $parts[] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
            if ($diff->m > 0) $parts[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
            if ($diff->d > 0 || empty($parts)) {
                $parts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            }

            return implode(', ', $parts);

        } catch (\Throwable $e) {
            return '-';
        }
    }

    public function formatDate(?string $date): string
    {
        if (!$date) return '-';

        try {
            return Carbon::parse($date)->format('d-m-Y');
        } catch (\Throwable) {
            return '-';
        }
    }

    public function save(): void
    {
        if (count($this->work) === 0) {
            $this->addError('work', 'At least one work experience is required.');
            return;
        }

        $this->dispatch('switch-tab', tab: 'bank');
    }

    protected function messages(): array
    {
        return [
            'form.designation_id.required' => 'Designation is required.',
            'form.institution.required'    => 'Institution is required.',
            'form.from_date.required'      => 'From Date is required.',
        ];
    }

    public function render()
    {
        return view('livewire.profile.tabs.work-tab');
    }

    public function totalExperience(): string
    {
        if (empty($this->work)) {
            return '-';
        }

        try {
            $totalDays = 0;

            foreach ($this->work as $row) {
                if (empty($row['from_date'])) continue;

                $from = Carbon::parse($row['from_date'])->startOfDay();

                if (!empty($row['is_current'])) {
                    $to = Carbon::today()->startOfDay();
                } elseif (!empty($row['to_date'])) {
                    $to = Carbon::parse($row['to_date'])->startOfDay();
                } else {
                    continue;
                }

                if ($to->lessThan($from)) continue;

                $totalDays += $from->diffInDays($to);
            }

            if ($totalDays === 0) {
                return '-';
            }

            // Convert total days → Y M D
            $start = Carbon::createFromDate(2000, 1, 1);
            $end   = $start->copy()->addDays($totalDays);

            $diff = $start->diff($end);

            $parts = [];
            if ($diff->y > 0) $parts[] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
            if ($diff->m > 0) $parts[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
            if ($diff->d > 0 || empty($parts)) {
                $parts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            }

            return implode(', ', $parts);

        } catch (\Throwable $e) {
            return '-';
        }
    }

}
