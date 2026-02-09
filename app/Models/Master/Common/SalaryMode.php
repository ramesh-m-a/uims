<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class SalaryMode extends BaseMasterModel
{
    protected $table = 'mas_salary_mode';

    protected $fillable = [
        'mas_salary_mode_name',
        'mas_salary_mode_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_salary_mode_status_id', 1);
    }
}
