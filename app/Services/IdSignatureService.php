<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class IdSignatureService
{
    public function sign(array $payload): string
    {
        return JWT::encode($payload, config('app.key'), 'HS256');
    }

    public function verify(string $token): object
    {
        return JWT::decode($token, new Key(config('app.key'), 'HS256'));
    }
}
