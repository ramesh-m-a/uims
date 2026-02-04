<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Model;

class QualificationDetails extends Model
{
    protected $table = 'qualification_details';

    protected $fillable = [
        'qualification_details_basic_details_id',
        'qualification_details_stream_id',
        'qualification_details_degree_id',
        'qualification_details_specialisation_id',
        'qualification_details_university_name',
        'qualification_details_year_of_award',
        'qualification_details_year_of_exam',
        'qualification_details_state_registration_number',
        'qualification_details_registration_date',
    ];

    public $timestamps = true;
}
