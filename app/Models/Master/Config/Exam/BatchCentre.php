<?php

namespace App\Models\Master\Config\Exam;

use Illuminate\Database\Eloquent\Model;

class BatchCentre extends Model
{
    protected $table = 'mas_batch_centre';

    protected $fillable = [
        'mas_batch_id',
        'mas_centre_id',
        'is_attached',
        'status_id',
    ];
}
