<?php

namespace App\Models\Master\Common;

use Illuminate\Database\Eloquent\Model;

class BankBranch extends Model
{
    protected $table = 'mas_bank_branch';

    protected $fillable = [
        'mas_bank_branch_bank_id',
        'mas_bank_branch_branch_name',
        'mas_bank_branch_branch_city',
        'mas_bank_branch_branch_state',
        'mas_bank_branch_status_id',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
