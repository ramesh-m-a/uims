<?php

namespace App\Livewire\Examiner\AppointmentOrder;

use Livewire\Component;
use App\Models\AppointmentOrder;

class ViewOrders extends Component
{
    public $year;
    public $month;
    public $scheme_id;
    public $degree_id;

    public $orders = [];

    public function loadOrders()
    {
        $query = AppointmentOrder::with([
            'examiner',
            'allocation',
            'batch'
        ])->latest();

        if ($this->year) {
            $query->whereHas('batch', fn($q) => $q->where('year', $this->year));
        }

        if ($this->month) {
            $query->whereHas('batch', fn($q) => $q->where('month', $this->month));
        }

        if ($this->scheme_id) {
            $query->whereHas('batch', fn($q) => $q->where('scheme_id', $this->scheme_id));
        }

        if ($this->degree_id) {
            $query->whereHas('batch', fn($q) => $q->where('degree_id', $this->degree_id));
        }

        $this->orders = $query->get();
    }

    public function render()
    {
        return view('livewire.examiner.appointment-order.view-orders');
    }
}
