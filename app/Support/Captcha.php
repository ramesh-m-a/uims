<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;

class Captcha
{
    const KEY = 'login_captcha';

    public static function generate(): string
    {
        $code = (string) random_int(100000, 999999);
        Session::put(self::KEY, $code);
        return $code;
    }

    public static function get(): ?string
    {
        return Session::get(self::KEY);
    }

    public static function verify(?string $input): bool
    {
        return $input !== null && $input === Session::get(self::KEY);
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
    }

    public static function refresh(): string
    {
        self::clear();
        return self::generate();
    }
}
