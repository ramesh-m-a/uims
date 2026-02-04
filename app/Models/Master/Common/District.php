<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class District extends BaseMasterModel
{
    protected $table = 'mas_district';

    protected $fillable = [
        'mas_district_name',
        'mas_district_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_district_status_id', 1);
    }
}
