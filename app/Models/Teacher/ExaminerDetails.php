<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Model;

class ExaminerDetails extends Model
{
    protected $table = 'examiner_details';

    protected $fillable = [
        'examiner_details_basic_details_id',


        'created_by',
        'updated_by',
    ];

    public $timestamps = true;
}
