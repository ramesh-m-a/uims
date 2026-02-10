<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempExaminerAllocation extends Model
{
    protected $table = 'temp_examiner_assigned_details';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'year_id',
        'month_id',
        'scheme_id',
        'degree_id',
        'batch_id',
        'batch_range_id',
        'examiner_id',
        'examiner_name',
        'examiner_type',
        'examiner_type_id',
        'mobile',
        'centre_id',
        'centre_name',
        'attached_id',
        'user_college_id',
        'subject_id',
        'subject_name',
        'user_stream_id',
        'status',
        'status_label',
        'batch_name',
        'from_date',
        'original_from_date',
        'is_rescheduled',
        'is_additional',
        'rescheduled_at',
        'rescheduled_by',
        'to_date',
        'label_colour',
    ];

    protected $casts = [
        'from_date' => 'date',
        'original_from_date' => 'date',
        'to_date' => 'date',
        'rescheduled_at' => 'datetime',
        'is_rescheduled' => 'boolean',
        'is_additional' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | OPTIONAL RELATIONS (Add Only If Needed Later)
    |--------------------------------------------------------------------------
    */

    public function appointmentOrders()
    {
        return $this->hasMany(AppointmentOrder::class, 'allocation_id');
    }
}
