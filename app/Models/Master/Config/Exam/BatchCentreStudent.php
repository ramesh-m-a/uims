<?php

namespace App\Models\Master\Config\Exam;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Config\Academic\College;

class BatchCentreStudent extends Model
{
    protected $table = 'mas_batch_centre_student';

    protected $fillable = [
        'mas_batch_id',
        'mas_centre_id',
        'student_count',
    ];

    public function college()
    {
        return $this->belongsTo(
            College::class,
            'mas_centre_id'
        );
    }

    public function batchCentre()
    {
        return $this->belongsTo(
            \App\Models\Master\Config\Exam\BatchCentre::class,
            'mas_centre_id',
            'mas_centre_id'
        )->whereColumn('mas_batch_id', 'mas_batch_id');
    }
}
