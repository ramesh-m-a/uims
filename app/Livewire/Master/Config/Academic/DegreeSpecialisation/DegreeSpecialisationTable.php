<?php

namespace App\Livewire\Master\Config\Academic\DegreeSpecialisation;

use App\Livewire\Master\Concerns\MasterFormBase;
use App\Livewire\Master\MasterTableBase;
use App\Models\Master\Config\Academic\DegreeStream;
use App\Models\Master\Config\Academic\Degree;
use App\Models\Master\Config\Academic\Stream;
use Illuminate\Database\Eloquent\Builder;

class DegreeSpecialisationTable extends MasterTableBase
{
    use MasterFormBase;

    /* ==========================
     | MODEL
     ========================== */
    protected string $modelClass = DegreeSpecialisationTable::class;

    /* ==========================
     | PAGE / UI STATE
     ========================== */
    public string $pageRoute = '';

    /* ==========================
     | DELETE STATE
     ========================== */
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    /* ==========================
     | DROPDOWNS
     ========================== */
    public array $degrees = [];
    public array $streams = [];

    /* ==========================
     | MOUNT
     ========================== */
    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();

        $this->degrees = Degree::orderBy('mas_degree_name')
            ->pluck('mas_degree_name', 'id')
            ->toArray();

    }

    /* ==========================
     | REQUIRED BY MASTER BASE
     ========================== */
    protected function permissionPrefix(): string
    {
        return 'master.config.academic.degree-specialisation';
    }

    protected function configKey(): string
    {
        return 'master.config.academic.degree-specialisation';
    }

    /* ==========================
     | FORM CONFIG
     ========================== */
    protected function formDefaults(): array
    {
        return [
            'mas_degree_id' => '',
            'mas_stream_id' => '',
        ];
    }

    protected function rules(): array
    {
        return [
            'form.mas_degree_id' => 'required|exists:mas_degree,id',
            'form.mas_stream_id' => 'required|exists:mas_stream,id',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.mas_degree_id' => 'Degree',
            'form.mas_stream_id' => 'Stream',
        ];
    }

    /* ==========================
     | TABLE QUERY (Eloquent Builder)
     ========================== */
    protected function baseQuery(): Builder
    {
        return DegreeStream::query()
            ->join('mas_degree as d', 'd.id', '=', 'mas_degree_stream.mas_degree_id')
            ->join('mas_stream as s', 's.id', '=', 'mas_degree_stream.mas_stream_id')
            ->select([
                'mas_degree_stream.id',
                'd.mas_degree_name as degree',
                's.mas_stream_name as stream',
            ]);
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Degree', 'field' => 'degree', 'sortable' => true],
            ['label' => 'Stream', 'field' => 'stream', 'sortable' => true],
        ];
    }

    /* ==========================
     | DUPLICATE PROTECTION
     ========================== */
    protected function beforeSave(): void
    {
        $exists = DegreeStream::query()
            ->where('mas_degree_id', $this->form['mas_degree_id'])
            ->where('mas_stream_id', $this->form['mas_stream_id'])
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            $this->addError('form.mas_stream_id', 'This mapping already exists.');
            throw new \RuntimeException('Duplicate mapping');
        }
    }

    /* ==========================
     | SINGLE DELETE
     ========================== */
    public function confirmDelete(int $id): void
    {
        abort_unless($this->can('delete'), 403);

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        abort_unless($this->can('delete'), 403);

        DegreeStream::findOrFail($this->deleteId)->delete();

        $this->js(
            "Alpine.store('toast').push('success','Mapping deleted successfully')"
        );

        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->resetPage();
    }
}
