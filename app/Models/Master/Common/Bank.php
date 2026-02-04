<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class Bank extends BaseMasterModel
{
    protected $table = 'mas_bank';

    protected $fillable = [
        'mas_bank_name',
        'mas_bank_status_id',
        'created_by',
        'updated_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_bank_status_id', 1);
    }
}
