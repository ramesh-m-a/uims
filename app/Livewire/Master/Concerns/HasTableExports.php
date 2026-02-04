<?php

namespace App\Livewire\Master\Concerns;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

trait HasTableExports
{
    protected function exportQuery()
    {
        $query = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection);

        if (!empty($this->selected)) {
            $query->whereIn('id', $this->selected);
        }

        return $query;
    }

    protected function exportRows()
    {
        $columns = $this->columns();

        return $this->exportQuery()->get()->map(function ($row) use ($columns) {
            $exportRow = [];

            foreach ($columns as $column) {
                $value = data_get($row, $column['field']);

                if (($column['type'] ?? '') === 'enum') {
                    $value = $column['options'][$value] ?? '—';
                }

                $exportRow[$column['label']] = $value;
            }

            return $exportRow;
        });
    }

    protected function exportMeta(): array
    {
        return [
            'org'   => 'RAJIV GANDHI UNIVERSITY OF HEALTH SCIENCES',
            'title' => strtoupper(Str::slug(class_basename($this->modelClass), ' ')) . ' REPORT',
            'date'  => now()->format('d-m-Y H:i'),
            'logo'  => public_path('images/RGUHS-logo.png'),
        ];
    }

    /* ==========================
     | PDF (DOMPDF – FINAL)
     ========================== */
    public function exportPdf()
    {
        $rows = $this->exportRows();
        $meta = $this->exportMeta();

        // ✅ AUTO ORIENTATION
        $orientation = count($rows->first()) > 6 ? 'landscape' : 'portrait';

        $filename = Str::slug(class_basename($this->modelClass)) . '.pdf';

        $pdf = Pdf::loadView('exports.master-table-pdf', [
            'rows' => $rows,
            'meta' => $meta,
        ])->setPaper('a4', $orientation);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }
}
