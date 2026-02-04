<?php

namespace App\Livewire\Examiner\Allocation;

use Livewire\Component;
use App\Repositories\AllocationRepository;

class ChangeExaminerDateDrawer extends Component
{
    public bool $open = false;

    public int $examinerId;
    public int $batchId;
    public int $rangeId;

    public string $current_from = '';
    public string $current_to = '';

    public string $from_date = '';
    public string $to_date = '';

    protected $listeners = ['openChangeDateDrawer'];

    public function openChangeDateDrawer(array $payload): void
    {
        $this->examinerId = $payload['examinerId'];
        $this->batchId    = $payload['batchId'];
        $this->rangeId    = $payload['rangeId'];

        $repo = app(AllocationRepository::class);

        $row = $repo->findAssignmentRow(
            $this->examinerId,
            $this->batchId,
            $this->rangeId
        );

        if (! $row) {
            session()->flash('error', 'Allocation row not found');
            return;
        }

        $this->current_from = $row->examiner_assigned_details_from_date;
        $this->current_to   = $row->examiner_assigned_details_to_date;

        $this->from_date = $this->current_from;
        $this->to_date   = $this->current_to;

        $this->open = true;
    }

    public function save(): void
    {
        $this->validate([
            'from_date' => ['required', 'date'],
            'to_date'   => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        app(AllocationRepository::class)->updateAssignmentDates(
            $this->examinerId,
            $this->batchId,
            $this->rangeId,
            $this->from_date,
            $this->to_date
        );

        $this->open = false;

        $this->dispatch('allocation-refresh');
        session()->flash('success', 'Dates updated successfully');
    }

    public function render()
    {
        return view('livewire.examiner.allocation.change-examiner-date-drawer');
    }
}
