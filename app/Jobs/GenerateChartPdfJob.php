<?php

namespace App\Jobs;

use App\Models\ChartDocument;
use App\Services\Examiner\ChartDocumentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

class GenerateChartPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $documentId;

    public function __construct(int $documentId)
    {
        $this->documentId = $documentId;
    }

    public function handle(): void
    {
        try {

            /** =========================================
             * LOAD DOCUMENT
             * ========================================= */
            $doc = ChartDocument::with([
                'allocation',
            ])->findOrFail($this->documentId);

            /** =========================================
             * STATUS → GENERATING
             * ========================================= */
            $doc->update([
                'pdf_status' => 'GENERATING',
                'pdf_last_error' => null,
            ]);

            /** =========================================
             * HTML RENDER (NO QR)
             * ========================================= */
            $html = View::make('pdf.chart', [
                'document' => $doc,
                'allocation' => $doc->allocation,
            ])->render();

            /** =========================================
             * FILE NAME (Portal Rule)
             * ========================================= */
            $username = strtolower(
                preg_replace('/[^a-z0-9]/i', '_', $doc->allocation->username ?? 'user')
            );

            $fileName = $username . '_chart.pdf';

            /** =========================================
             * STORAGE DISK
             * ========================================= */
            $disk = 'charts';

            Storage::disk($disk)->makeDirectory('/');

            $absolutePath = Storage::disk($disk)->path($fileName);

            /** =========================================
             * CHROME PATH
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
             * ATTACH PDF
             * ========================================= */
            app(ChartDocumentService::class)
                ->attachPdf($doc, $fileName, $disk);

            /** =========================================
             * SUCCESS STATUS
             * ========================================= */
            $doc->update([
                'pdf_status' => 'GENERATED',
                'pdf_generated_at' => now(),
                'pdf_last_error' => null,
            ]);

        } catch (\Throwable $e) {

            Log::error('Chart PDF Failed', [
                'document_id' => $this->documentId,
                'error' => $e->getMessage(),
            ]);

            ChartDocument::where('id', $this->documentId)->update([
                'pdf_status' => 'FAILED',
                'pdf_last_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
