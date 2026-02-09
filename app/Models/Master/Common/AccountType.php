<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class AccountType extends BaseMasterModel
{
    protected $table = 'mas_account_type';

    protected $fillable = [
        'mas_account_type_name',
        'mas_account_type_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_account_type_status_id', 1);
    }
}
