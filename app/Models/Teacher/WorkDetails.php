<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Model;

class WorkDetails extends Model
{
    protected $table = 'work_details';

    protected $fillable = [
        'work_details_basic_details_id',
        'work_details_work_designation_id',
        'work_details_work_department_id',
        'work_details_last_institution_name',
        'work_details_date_of_appointment',
        'work_details_from_date',
        'work_details_to_date',
        'work_details_date_of_joining',
        'work_details_till_date',
    ];

    public $timestamps = true;
}
