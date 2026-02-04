<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class State extends BaseMasterModel
{
    protected $table = 'mas_state';

    protected $fillable = [
        'mas_state_name',
        'mas_state_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_state_status_id', 1);
    }
}
