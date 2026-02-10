<?php

namespace App\Services\Examiner;

use App\Models\AppointmentOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppointmentOrderService
{

    /**
     * PREVIEW DATA ONLY (No DB Write)
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
     * GENERATE NEW ORDER
     */
    public function generate($allocation, array $extra = [])
    {
        return DB::transaction(function () use ($allocation, $extra) {

            // 1️⃣ Mark old latest false (if exists)
            AppointmentOrder::where('allocation_id', $allocation->id)
                ->where('is_latest', true)
                ->update(['is_latest' => false]);

            // 2️⃣ Determine Version
            $latestVersion = AppointmentOrder::where('allocation_id', $allocation->id)
                ->max('order_version');

            $version = ($latestVersion ?? 0) + 1;

            // 3️⃣ Generate Order Number (Hook — real logic Step 4)
            $orderNumber = $extra['order_number'] ?? $this->temporaryOrderNumber();

            // 4️⃣ Generate Timestamp
            $generatedAt = now();

            // 5️⃣ Hash
            $hash = $this->generateHash([
                $orderNumber,
                $allocation->examiner_id,
                $allocation->batch_id,
                $generatedAt
            ]);

            // 6️⃣ QR Payload
            $qrPayload = $this->buildQrPayload(
                $orderNumber,
                $version,
                $allocation->id,
                $hash
            );

            // 7️⃣ Create Record (PDF later update)
            $order = AppointmentOrder::create([
                'allocation_id' => $allocation->id,
                'examiner_id'   => $allocation->examiner_id,
                'batch_id'      => $allocation->batch_id,

                'order_number'  => $orderNumber,
                'order_version' => $version,
                'is_latest'     => true,

                'generated_by'  => Auth::id() ?? 1,
                'generated_role'=> $extra['generated_role'] ?? 'SYSTEM',
                'generated_at'  => $generatedAt,

                'document_hash' => $hash,
                'qr_payload'    => $qrPayload,

                'status'        => 'GENERATED',
            ]);

            return $order;
        });
    }


    /**
     * REGENERATE ORDER
     */
    public function regenerate($existingOrder, array $extra = [])
    {
        return DB::transaction(function () use ($existingOrder, $extra) {

            $existingOrder->update([
                'is_latest' => false
            ]);

            $allocation = $existingOrder->allocation;

            $new = $this->generate($allocation, $extra);

            $new->update([
                'regenerated_from_id' => $existingOrder->id,
                'status' => 'REGENERATED'
            ]);

            return $new;
        });
    }


    /**
     * UPDATE PDF AFTER GENERATION
     */
    public function attachPdf($order, $path, $disk = 'local')
    {
        $order->update([
            'pdf_path' => $path,
            'pdf_disk' => $disk,
        ]);

        return $order;
    }


    /**
     * HASH GENERATION
     */
    private function generateHash(array $parts)
    {
        return hash('sha256', implode('|', $parts));
    }


    /**
     * QR PAYLOAD BUILDER
     */
    private function buildQrPayload($orderNo, $version, $allocationId, $hash)
    {
        return [
            'order_no'      => $orderNo,
            'version'       => $version,
            'allocation_id' => $allocationId,
            'hash'          => $hash,
        ];
    }


    /**
     * TEMP ORDER NUMBER (Replace Step 4)
     */
    private function temporaryOrderNumber()
    {
        return 'TEMP-' . strtoupper(Str::random(10));
    }

    public function generateIfMissing($allocation, array $extra = [])
    {
        return DB::transaction(function () use ($allocation, $extra) {

            /** 🔒 LOCK EXISTING LATEST ROW */
            $latest = AppointmentOrder::where('allocation_id', $allocation->id)
                ->where('is_latest', true)
                ->lockForUpdate()
                ->first();

            if ($latest) {
                return $latest; // ✅ Already exists
            }

            /** ⭐ CREATE NEW ORDER */
            $order = $this->generate($allocation, $extra);

            /** ⭐ DISPATCH PDF JOB */
            \App\Jobs\GenerateAppointmentOrderPdfJob::dispatch($order->id);

            return $order;
        });
    }

}
