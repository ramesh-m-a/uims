<?php

namespace App\Jobs;

use App\Models\AppointmentOrder;
use App\Services\Examiner\AppointmentOrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateAppointmentOrderPdfJob implements ShouldQueue
{
    use Queueable;

    public $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $order = AppointmentOrder::with([
            'examiner',
            'allocation',
            'batch'
        ])->findOrFail($this->orderId);

        // 1️⃣ Render HTML
        $html = View::make('pdf.appointment-order', [
            'order' => $order
        ])->render();

        // 2️⃣ File Path
        $fileName = str_replace('/', '-', $order->order_number) . '.pdf';
        $path = storage_path('app/orders/' . $fileName);

        // 3️⃣ Generate PDF
        Browsershot::html($html)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->timeout(120)
            ->save($path);

        // 4️⃣ Attach PDF Path
        app(AppointmentOrderService::class)
            ->attachPdf($order, 'orders/' . $fileName);

        $qrBase64 = app(\App\Services\Examiner\AppointmentOrderQrService::class)
            ->generateBase64($order->qr_payload);

        $html = View::make('pdf.appointment-order', [
            'order' => $order,
            'qrBase64' => $qrBase64
        ])->render();
    }
}
