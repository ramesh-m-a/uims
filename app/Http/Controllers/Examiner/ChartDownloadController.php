<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\TempExaminerAllocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChartDownloadController extends Controller
{
    /**
     * View PDF in browser
     */
    public function view(TempExaminerAllocation $allocation)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        if (
            !$allocation->pdf_path ||
            !Storage::disk($allocation->pdf_disk)->exists($allocation->pdf_path)
        ) {
            abort(404, 'PDF not found');
        }

        return Storage::disk($allocation->pdf_disk)->response(
            $allocation->pdf_path,
            $allocation->username . '_chart.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' =>
                    'inline; filename="'.$allocation->username.'_chart.pdf"',
            ]
        );
    }

    /**
     * Force Download
     */
    public function download(TempExaminerAllocation $allocation)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        if (
            !$allocation->pdf_path ||
            !Storage::disk($allocation->pdf_disk)->exists($allocation->pdf_path)
        ) {
            abort(404, 'PDF not found');
        }

        return Storage::disk($allocation->pdf_disk)->download(
            $allocation->pdf_path,
            $allocation->username . '_chart.pdf'
        );
    }
}
