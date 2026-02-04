<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class Status extends BaseMasterModel
{
    protected $table = 'mas_status';

    protected $fillable = [
        'mas_status_code',
        'mas_status_name',
        'is_active',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
