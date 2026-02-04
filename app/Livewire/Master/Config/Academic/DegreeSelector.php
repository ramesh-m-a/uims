<?php

namespace App\Livewire\Master\Config\Academic;

use Livewire\Component;
use App\Models\Master\Config\Academic\DegreeLevel;
use App\Models\Master\Config\Academic\Degree;
use App\Models\Master\Config\Academic\Specialisation;

class DegreeSelector extends Component
{
    /* =========================
     | DATA SOURCES
     ========================= */
    public $degreeLevels = [];
    public $degrees = [];
    public $specialisations = [];

    /* =========================
     | SELECTED VALUES
     ========================= */
    public $degreeLevelId = null;
    public $degreeId = null;
    public $specialisationId = null;

    /* =========================
     | FLAGS
     ========================= */
    public bool $requiresSpecialisation = false;

    /* =========================
     | MOUNT
     ========================= */
    public function mount(): void
    {
        $this->degreeLevels = DegreeLevel::active()
            ->orderBy('sort_order')
            ->get();
    }

    /* =========================
     | UPDATED HOOKS
     ========================= */

    public function updatedDegreeLevelId($value): void
    {
        $this->reset(['degreeId', 'specialisationId', 'degrees', 'specialisations']);

        if (! $value) {
            return;
        }

        $this->degrees = Degree::query()
            ->where('degree_level_id', $value)
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function updatedDegreeId($value): void
    {
        $this->reset(['specialisationId', 'specialisations']);
        $this->requiresSpecialisation = false;

        if (! $value) {
            return;
        }

        $degree = Degree::with('specialisations')->find($value);

        if (! $degree) {
            return;
        }

        $this->requiresSpecialisation = $degree->requires_specialisation;

        if ($this->requiresSpecialisation) {
            $this->specialisations = $degree->specialisations()
                ->active()
                ->orderBy('name')
                ->get();
        }
    }

    /* =========================
     | RENDER
     ========================= */
    public function render()
    {
        return view('livewire.academic.degree-selector');
    }

    protected function rules(): array
    {
        return [
            'degreeLevelId' => ['required', 'exists:degree_levels,id'],
            'degreeId'      => ['required', 'exists:degrees,id'],
            'specialisationId' => [
                function ($attribute, $value, $fail) {

                    if (! $this->degreeId) {
                        return;
                    }

                    $degree = \App\Models\Academic\Degree::find($this->degreeId);

                    if (! $degree) {
                        return;
                    }

                    if ($degree->requires_specialisation && empty($value)) {
                        $fail('Specialisation is mandatory for the selected degree.');
                    }

                    if (! $degree->requires_specialisation && ! empty($value)) {
                        $fail('Specialisation is not applicable for the selected degree.');
                    }
                }
            ],
        ];
    }

    public function validateSelection(): void
    {
        $this->validate();
    }

    public function updated($property): void
    {
        if (in_array($property, [
            'degreeLevelId',
            'degreeId',
            'specialisationId'
        ])) {
            $this->validateOnly($property);
        }
    }

}
