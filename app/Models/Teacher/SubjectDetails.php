<?php

namespace App\Models\Teacher;

use App\Models\Master\BaseMasterModel;
use Illuminate\Database\Eloquent\Model;

class SubjectDetails extends BaseMasterModel
{
    protected $fillable = [
        'basic_details_id',
        'mas_subject_id',
        'mas_year_id',
    ];

    public function subject()
    {
        return $this->belongsTo(
            \App\Models\Master\Config\Academic\Subject::class,
            'mas_subject_id'
        );
    }

    public function year()
    {
        return $this->belongsTo(
            \App\Models\Master\Common\Year::class,
            'mas_year_id'
        );
    }
}
