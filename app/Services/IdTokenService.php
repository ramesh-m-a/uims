<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class IdTokenService
{
    private static string $secret = 'RGUHS_SUPER_SECRET_2026_CHANGE_THIS';

    public static function generate(int $userId): string
    {
        $payload = [
            'uid'    => $userId,
            'exp'    => now()->addDays(30)->timestamp,
            'device' => sha1(request()->userAgent() ?? ''),
        ];

        $json = json_encode($payload);
        $sig  = hash_hmac('sha256', $json, self::$secret);

        return base64_encode($json . '.' . $sig);
    }

    public static function verify(string $token): ?array
    {
        $decoded = base64_decode($token);
        if (!str_contains($decoded, '.')) return null;

        [$json, $sig] = explode('.', $decoded, 2);

        if (!hash_equals(hash_hmac('sha256', $json, self::$secret), $sig)) {
            return null;
        }

        $data = json_decode($json, true);

        if (!$data || now()->timestamp > $data['exp']) {
            return null;
        }

        return $data;
    }
}
