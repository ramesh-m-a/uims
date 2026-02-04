<?php

namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class Month extends BaseMasterModel
{
    protected $table = 'mas_month';

    protected $fillable = [
        'mas_month_name',
        'mas_month_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_month_status_id', 1);
    }
}
