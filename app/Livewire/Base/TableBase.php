<?php

namespace App\Livewire\Base;

use App\Livewire\Master\Concerns\HasTableExports;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Livewire\Component;
use Livewire\WithPagination;

abstract class TableBase extends Component
{
    use HasTableExports;
    use WithPagination;

    protected string $modelClass;

    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'id';
    public string $sortDirection = 'asc';

    public bool $showTrashed = false;

    public array $filters = [];
    public string $statusFilter = '';

    public array $selected = [];
    public bool $selectAll = false;
    public array $pageIds = [];

    /* =====================================================
     | BASE QUERY (SINGLE SOURCE OF TRUTH)
     ===================================================== */
    protected function baseQuery(): Builder
    {
        $query = ($this->modelClass)::query();

        /* ==========================
         | SOFT DELETE SUPPORT (SAFE)
         ========================== */
        if ($this->supportsSoftDeletes()) {
            if ($this->can('restore')) {
                $query->withTrashed();
            }

            if ($this->showTrashed) {
                $query->onlyTrashed();
            }
        }

        /* ==========================
         | GLOBAL SEARCH
         ========================== */
        if ($this->search !== '') {
            $query->where(function ($q) {
                foreach ($this->columns() as $column) {

                    // Default type safety (prevents future bugs)
                    $type = $column['type'] ?? 'text';
                    if ($type !== 'text') {
                        continue;
                    }

                    $field = $column['field'] ?? null;
                    if (!$field) continue;

                    if (str_contains($field, '.')) {
                        [$relation, $relField] = explode('.', $field, 2);

                        if (method_exists($this->modelClass, $relation)) {
                            $q->orWhereHas($relation, function ($rq) use ($relField) {
                                $rq->where($relField, 'like', "%{$this->search}%");
                            });
                        }
                    } else {
                        $q->orWhere($field, 'like', "%{$this->search}%");
                    }
                }
            });
        }

        /* ==========================
         | STATUS FILTER
         ========================== */
        if ($this->statusFilter !== '' && $this->statusField()) {
            $query->where($this->statusField(), $this->statusFilter);
        }

        /* ==========================
         | COLUMN FILTERS
         ========================== */
        foreach ($this->filters as $field => $value) {

            if (is_array($value)) {
                foreach ($value as $subField => $subValue) {
                    if ($subValue === '' || $subValue === null) continue;

                    if (method_exists($this->modelClass, $field)) {
                        $query->whereHas($field, function ($q) use ($subField, $subValue) {
                            $q->where($subField, 'like', "%{$subValue}%");
                        });
                    }
                }
                continue;
            }

            if ($value === '' || $value === null) continue;

            if (str_contains($field, '.')) {
                [$relation, $column] = explode('.', $field, 2);

                if (method_exists($this->modelClass, $relation)) {
                    $query->whereHas($relation, function ($q) use ($column, $value) {
                        $q->where($column, 'like', "%{$value}%");
                    });
                    continue;
                }
            }

            $query->where($field, 'like', "%{$value}%");
        }

        return $query;
    }

    /* =====================================================
     | HELPERS
     ===================================================== */
    protected function supportsSoftDeletes(): bool
    {
        return in_array(
            SoftDeletes::class,
            class_uses_recursive($this->modelClass)
        );
    }

    public function sortBy(string $field): void
    {
        $this->sortDirection =
            $this->sortField === $field && $this->sortDirection === 'asc'
                ? 'desc'
                : 'asc';

        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatedSearch()       { $this->resetPage(); }
    public function updatedPerPage()      { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }

    public function updatedSelectAll(bool $checked): void
    {
        $this->selected = $checked ? $this->pageIds : [];
    }

    public function updatedSelected(): void
    {
        $this->selectAll = count($this->selected) === count($this->pageIds);
    }

    /* =====================================================
     | COLUMN CONTRACT (SAFE NORMALIZATION)
     ===================================================== */
    protected function columns(): array
    {
        $raw = $this->masterConfig()['columns'] ?? [];

        return collect($raw)
            ->map(fn ($cfg, $field) => array_merge([
                'field'      => $field,
                'label'      => $field,
                'visible'    => true,
                'sortable'   => false,
                'filterable' => false,
                'type'       => 'text',   // 🔥 default safety
                'order'      => 999,
            ], $cfg))
            ->filter(fn ($c) => $c['visible'])
            ->sortBy('order')
            ->values()
            ->toArray();
    }

    protected function statusField(): ?string
    {
        return $this->masterConfig()['status_field'] ?? null;
    }

    protected function masterConfig(): array
    {
        return method_exists($this, 'configKey')
            ? config($this->configKey(), [])
            : [];
    }

    /* =====================================================
     | PERMISSIONS
     ===================================================== */
    protected function can(string $action): bool
    {
        return auth()->user()?->hasPermission(
            $this->permissionPrefix() . '.' . $action
        ) ?? false;
    }

    abstract protected function permissionPrefix(): string;
}
