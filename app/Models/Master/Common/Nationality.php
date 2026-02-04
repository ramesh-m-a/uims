<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class Nationality extends BaseMasterModel
{
    protected $table = 'mas_nationality';

    protected $fillable = [
        'mas_nationality_name',
        'mas_nationality_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_nationality_status_id', 1);
    }
}
