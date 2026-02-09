<?php

namespace App\Services\Examiner;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class AppointmentOrderQrService
{
    public function generateBase64(array $payload): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_H,
            'scale'      => 5,
        ]);

        $qr = (new QRCode($options))->render(json_encode($payload));

        return base64_encode($qr);
    }
}
