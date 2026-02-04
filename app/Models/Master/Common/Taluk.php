<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class Taluk extends BaseMasterModel
{
    protected $table = 'mas_taluk';

    protected $fillable = [
        'mas_taluk_name',
        'mas_taluk_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_taluk_status_id', 1);
    }
}
