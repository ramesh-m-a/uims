<?php

namespace App\Models\Master\Common;

use Illuminate\Database\Eloquent\Model;

class Ifsc extends Model
{
    protected $table = 'mas_ifsc';

    protected $fillable = [
        'mas_ifsc_number',
        'mas_ifsc_bank_id',
        'mas_ifsc_branch_id',
        'mas_ifsc_status_id',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'mas_ifsc_bank_id');
    }

    public function branch()
    {
        return $this->belongsTo(BankBranch::class, 'mas_ifsc_branch_id');
    }
}
