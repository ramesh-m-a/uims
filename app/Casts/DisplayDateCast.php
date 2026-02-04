<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Carbon\Carbon;

class DisplayDateCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if (!$value) return null;

        return [
            'db' => Carbon::parse($value)->format('Y-m-d'),
            'display' => Carbon::parse($value)->format('d/m/Y'),
        ];
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (!$value) return null;

        return Carbon::parse($value)->format('Y-m-d');
    }
}
