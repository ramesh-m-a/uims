<?php

namespace App\Support;

class UpperCase
{
    public static function upper($collection, string $field)
    {
        return $collection->map(function ($row) use ($field) {
            $row->{$field} = mb_strtoupper($row->{$field});
            return $row;
        });
    }
}
