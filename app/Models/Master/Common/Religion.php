<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class Religion extends BaseMasterModel
{
    protected $table = 'mas_religion';

    protected $fillable = [
        'mas_religion_name',
        'mas_religion_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_religion_status_id', 1);
    }
}
