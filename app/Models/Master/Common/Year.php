<?php

namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class Year extends BaseMasterModel
{
    protected $table = 'mas_year';

    protected $fillable = [
        'mas_year_year',
        'mas_year_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_year_status_id', 1);
    }
}
