<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class Gender extends BaseMasterModel
{
    protected $table = 'mas_gender';

    protected $fillable = [
        'mas_gender_name',
        'mas_gender_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_gender_status_id', 1);
    }
}
