<?php

namespace App\Livewire\Master\Config\Academic\Degree;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Config\Academic\Degree;
use App\Models\Master\Config\Academic\Stream;
use App\Models\Master\Config\Academic\DegreeLevel;

class DegreeTable extends MasterTableBase
{
    use MasterFormBase;

    /* ==========================
     | MODEL
     ========================== */
    protected string $modelClass = Degree::class;

    /* ==========================
     | PAGE / UI STATE
     ========================== */
    public string $pageRoute = '';

    public bool $showModal = false;
    public ?int $editingId = null;

    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    /* ==========================
     | MOUNT
     ========================== */
    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();
    }

    /* ==========================
     | RBAC
     ========================== */
    protected function permissionPrefix(): string
    {
        return 'master.config.academic.degree';
    }

    protected function configKey(): string
    {
        return 'master.config.academic.degree';
    }

    /* ==========================
     | FORM DEFAULTS
     ========================== */
    protected function formDefaults(): array
    {
        return [
            'streams'                        => [],   // MANY-TO-MANY
            'mas_degree_level_id'            => null,
            'mas_degree_code'                => '',
            'mas_degree_name'                => '',
            'mas_degree_specialisation_mode' => 0,    // 0=None, 1=Optional, 2=Mandatory
            'mas_degree_status_id'           => 1,
        ];
    }

    /* ==========================
     | VALIDATION RULES
     ========================== */
    protected function rules(): array
    {
        return [
            'form.streams'                        => 'required|array|min:1',
            'form.streams.*'                      => 'exists:mas_stream,partials',
            'form.mas_degree_level_id'            => 'required|exists:mas_degree_level,partials',
            'form.mas_degree_code'                => 'required|string|max:20',
            'form.mas_degree_name'                => 'required|string|max:150',
            'form.mas_degree_specialisation_mode' => 'required|in:0,1,2',
            'form.mas_degree_status_id'           => 'required|integer',
        ];
    }

    /* ==========================
     | VALIDATION LABELS
     ========================== */
    protected function validationAttributes(): array
    {
        return [
            'form.streams'                        => 'Streams',
            'form.mas_degree_level_id'            => 'Degree Level',
            'form.mas_degree_code'                => 'Degree Code',
            'form.mas_degree_name'                => 'Degree Name',
            'form.mas_degree_specialisation_mode' => 'Specialisation Mode',
            'form.mas_degree_status_id'           => 'Status',
        ];
    }

    /* ==========================
     | AFTER SAVE (SYNC STREAMS)
     ========================== */
    protected function afterSave(Degree $degree): void
    {
        $degree->streams()->sync($this->form['streams']);
    }

    /* ==========================
     | EDIT PREFILL
     ========================== */
    protected function editFormFill(Degree $degree): void
    {
        $this->form['streams'] = $degree->streams
            ->pluck('partials')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    /* ==========================
     | RENDER
     ========================== */
    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['streams', 'level'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->pageIds   = $rows->pluck('partials')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selected) === count($this->pageIds);

        return view('livewire.master.config.academic.degree.degree-table', [
            // data
            'degrees' => $rows,
            'streams' => Stream::orderBy('mas_stream_name')->get(),
            'levels'  => DegreeLevel::orderBy('mas_degree_level_sort_order')->get(),

            // master table contract
            'columns'        => $this->columns(),
            'search'         => $this->search,
            'perPage'        => $this->perPage,
            'filters'        => $this->filters,
            'sortField'      => $this->sortField,
            'sortDirection'  => $this->sortDirection,

            // permissions
            'canCreate'      => $this->can('create'),
            'canEdit'        => $this->can('edit'),
            'canDelete'      => $this->can('delete'),
            'canRestore'     => $this->can('restore'),

            // UI state
            'pageRoute'      => $this->pageRoute,
            'showModal'      => $this->showModal,
            'editingId'      => $this->editingId,
            'showDeleteModal'=> $this->showDeleteModal,
        ]);
    }

    protected function mapRow($row): array
    {
        return array_merge($row->toArray(), [
            'stream_display' => $row->streams->pluck('mas_stream_short_code')->implode(', '),
            'specialisation_display' => match ((int) $row->mas_degree_specialisation_mode) {
                0 => 'None',
                1 => 'Optional',
                2 => 'Mandatory',
                default => '-',
            },
        ]);
    }
}
