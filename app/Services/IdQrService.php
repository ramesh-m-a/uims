<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class IdQrService
{
    public static function generate(array $user): string
    {
        $payload = [
            'sub'   => $user['id'],
            'name'  => $user['name'],
            'tin'   => $user['tin'],
            'iss'   => 'RGUHS',
            'iat'   => time(),
            'exp'   => time() + (60 * 60 * 24 * 365 * 3), // 3 years
            'nonce' => bin2hex(random_bytes(8)),
        ];

        $jwt = JWT::encode($payload, config('app.key'), 'HS256');

        $verifyUrl = url('/verify?token=' . $jwt);

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale'      => 6,
            'imageBase64'=> true,
        ]);

        return (new QRCode($options))->render($verifyUrl);
    }
}
