<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Model;

class BasicDetails extends Model
{
    protected $table = 'basic_details';

    protected $fillable = [
        'basic_details_user_id',
        'basic_details_gender_id',
        'basic_details_dob',
        'basic_details_father_name',
        'basic_details_department_id',
        'basic_details_religion_id',
        'basic_details_category_id',
    ];

    public $timestamps = true;

    public function department()
    {
        return $this->belongsTo(
            \App\Models\Master\Config\Academic\Department::class,
            'basic_details_department_id'
        );
    }

    public function examinerDetails()
    {
        return $this->hasOne(
            \App\Models\Teacher\ExaminerDetails::class,
            'examiner_details_basic_details_id'
        );
    }
}
