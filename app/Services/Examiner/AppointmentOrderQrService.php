<?php

namespace App\Services\Examiner;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class AppointmentOrderQrService
{
    public function generateBase64($payload): ?string
    {
        try {

            if (empty($payload)) {
                return null;
            }

            if (is_array($payload)) {
                $payload = json_encode($payload);
            }

            /**
             * ⭐ FORCE PNG BINARY OUTPUT ONLY
             * DO NOT CHANGE THIS EVER
             */
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'   => QRCode::ECC_H,
                'scale'      => 6,
                'imageBase64'=> false, // ⭐ VERY IMPORTANT
            ]);

            /**
             * ⭐ RETURNS BINARY PNG
             */
            $pngBinary = (new QRCode($options))->render($payload);

            if (empty($pngBinary)) {
                return null;
            }

            /**
             * ⭐ RETURN RAW BASE64 ONLY
             */
            return base64_encode($pngBinary);

        } catch (\Throwable $e) {

            logger()->error('QR PNG Generation Failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
