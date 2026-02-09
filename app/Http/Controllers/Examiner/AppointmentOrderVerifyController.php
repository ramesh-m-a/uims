<?php

namespace App\Http\Controllers\Examiner;

use App\Models\AppointmentOrder;

class AppointmentOrderVerifyController
{
    public function verify($orderNumber)
    {
        $order = AppointmentOrder::where('order_number', $orderNumber)
            ->where('is_latest', true)
            ->first();

        if (!$order) {
            abort(404);
        }

        return view('verification.appointment-order', compact('order'));
    }
}
