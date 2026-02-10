<?php

namespace App\Jobs;

use App\Models\AppointmentOrder;
use App\Services\Examiner\AppointmentOrderService;
use App\Services\Examiner\AppointmentOrderQrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

class GenerateAppointmentOrderPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(): void
    {
        try {

            /** =========================================
             * LOAD ORDER
             * ========================================= */
            $order = AppointmentOrder::with([
                'examiner',
                'allocation',
                'batch'
            ])->findOrFail($this->orderId);

            /** =========================================
             * STATUS → GENERATING
             * ========================================= */
            $order->update([
                'pdf_status' => 'GENERATING',
                'pdf_last_error' => null,
            ]);

            /** =========================================
             * QR GENERATION
             * ========================================= */

            $qrBase64 = null;
            Log::info('QR DEBUG', [
                'payload' => $order->qr_payload,
                'qrBase64_len' => strlen($qrBase64 ?? ''),
                'qrBase64_sample' => substr($qrBase64 ?? '', 0, 50),
            ]);

            try {

                $qrBase64 = app(AppointmentOrderQrService::class)
                    ->generateBase64($order->qr_payload);

            } catch (\Throwable $e) {

                Log::error('QR Generation Failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('QR DEBUG', [
                'payload' => $order->qr_payload,
                'qrBase64_len' => strlen($qrBase64 ?? ''),
                'qrBase64_sample' => substr($qrBase64 ?? '', 0, 50),
            ]);

            /** =========================================
             * HTML RENDER
             * ========================================= */
            $html = View::make('pdf.appointment-order', [
                'order' => $order,
                'qrBase64' => $qrBase64
            ])->render();

            /** =========================================
             * FILE NAME
             * ========================================= */
            $fileName = str_replace('/', '-', $order->order_number) . '.pdf';

            /** =========================================
             * STORAGE DISK (⭐ GOLDEN RULE)
             * ========================================= */
            $disk = 'orders';

            /** Ensure folder exists */
            Storage::disk($disk)->makeDirectory('/');

            /** Absolute path for Browsershot */
            $absolutePath = Storage::disk($disk)->path($fileName);

            /** =========================================
             * BROWSER PATH
             * ========================================= */
            $chromePath = config('services.chrome.path');

            /** =========================================
             * PDF GENERATION
             * ========================================= */
            Browsershot::html($html)
                ->setChromePath($chromePath)
                ->format('A4')
                ->margins(10, 10, 10, 10)
                ->timeout(120)
                ->save($absolutePath);

            /** =========================================
             * ATTACH PDF TO ORDER
             * (Store ONLY filename, NOT folder prefix)
             * ========================================= */
            app(AppointmentOrderService::class)
                ->attachPdf($order, $fileName, $disk);

            /** =========================================
             * SUCCESS STATUS
             * ========================================= */
            $order->update([
                'pdf_status' => 'GENERATED',
                'pdf_generated_at' => now(),
                'pdf_last_error' => null,
            ]);

        } catch (\Throwable $e) {

            Log::error('Appointment PDF Failed', [
                'order_id' => $this->orderId,
                'error' => $e->getMessage(),
            ]);

            AppointmentOrder::where('id', $this->orderId)->update([
                'pdf_status' => 'FAILED',
                'pdf_last_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
