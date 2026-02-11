<?php

namespace App\Services\Examiner\Chart;

use App\Models\ChartDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChartDocumentService
{

    /**
     * PREVIEW DATA ONLY
     */
    public function previewFromAllocation($allocation)
    {
        return [
            'allocation' => $allocation,
            'examiner'   => $allocation->examiner ?? null,
            'batch'      => $allocation->batch ?? null,
            'generated_at' => now(),
        ];
    }


    /**
     * GENERATE NEW DOCUMENT
     */
    public function generate($allocation, array $extra = [])
    {
        return DB::transaction(function () use ($allocation, $extra) {

            /** 1️⃣ Mark old latest false */
            ChartDocument::where('allocation_id', $allocation->id)
                ->where('is_latest', true)
                ->update(['is_latest' => false]);

            /** 2️⃣ Version */
            $latestVersion = ChartDocument::where('allocation_id', $allocation->id)
                ->max('document_version');

            $version = ($latestVersion ?? 0) + 1;

            /** 3️⃣ Timestamp */
            $generatedAt = now();

            /** 4️⃣ Hash (Still good practice) */
            $hash = $this->generateHash([
                $allocation->id,
                $allocation->examiner_id,
                $allocation->batch_id,
                $generatedAt
            ]);

            /** 5️⃣ Create Record */
            $doc = ChartDocument::create([
                'allocation_id' => $allocation->id,
                'examiner_id'   => $allocation->examiner_id,
                'batch_id'      => $allocation->batch_id,

                'document_version' => $version,
                'is_latest'        => true,

                'generated_by'  => Auth::id() ?? 1,
                'generated_role'=> $extra['generated_role'] ?? 'SYSTEM',
                'generated_at'  => $generatedAt,

                'document_hash' => $hash,

                'status'        => 'GENERATED',
            ]);

            return $doc;
        });
    }


    /**
     * REGENERATE DOCUMENT
     */
    public function regenerate($existingDoc, array $extra = [])
    {
        return DB::transaction(function () use ($existingDoc, $extra) {

            $existingDoc->update([
                'is_latest' => false
            ]);

            $allocation = $existingDoc->allocation;

            $new = $this->generate($allocation, $extra);

            $new->update([
                'regenerated_from_id' => $existingDoc->id,
                'status' => 'REGENERATED'
            ]);

            return $new;
        });
    }


    /**
     * ATTACH PDF
     */
    public function attachPdf($doc, $path, $disk = 'public')
    {
        $doc->update([
            'pdf_path' => $path,
            'pdf_disk' => $disk,
        ]);

        return $doc;
    }


    /**
     * HASH
     */
    private function generateHash(array $parts)
    {
        return hash('sha256', implode('|', $parts));
    }


    /**
     * GENERATE IF MISSING + DISPATCH JOB
     */
    public function generateIfMissing($allocation, array $extra = [])
    {
        return DB::transaction(function () use ($allocation, $extra) {

            $latest = ChartDocument::where('allocation_id', $allocation->id)
                ->where('is_latest', true)
                ->lockForUpdate()
                ->first();

            if ($latest) {
                return $latest;
            }

            $doc = $this->generate($allocation, $extra);

            \App\Jobs\GenerateChartPdfJob::dispatch($doc->id);

            return $doc;
        });
    }
}
