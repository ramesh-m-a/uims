<?php


namespace App\Models\Master\Config\Exam;

use App\Models\Master\BaseMasterModel;

class Subject extends BaseMasterModel
{
    protected $table = 'mas_subject';

    protected $fillable = [

    ];

    public function scopeActive($query)
    {
        return $query->where('mas_subject_status_id', 1);
    }
}
