<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\AppointmentOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AppointmentOrderDownloadController extends Controller
{
    /**
     * View PDF in browser
     */
    public function view(AppointmentOrder $order)
    {
        // Must be logged in
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // File must exist
        if (!$order->pdf_path || !Storage::disk($order->pdf_disk)->exists($order->pdf_path)) {
            abort(404, 'PDF not found');
        }

        return Storage::disk($order->pdf_disk)->response(
            $order->pdf_path,
            $order->order_number . '.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$order->order_number.'.pdf"',
            ]
        );
    }

    /**
     * Force Download
     */
    public function download(AppointmentOrder $order)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        if (!$order->pdf_path || !Storage::disk($order->pdf_disk)->exists($order->pdf_path)) {
            abort(404, 'PDF not found');
        }

        return Storage::disk($order->pdf_disk)->download(
            $order->pdf_path,
            $order->order_number . '.pdf'
        );
    }
}
