<?php

namespace App\Models\Master\Config\Exam;

use App\Models\Master\Common\Status;
use Illuminate\Database\Eloquent\Model;

class BatchSplit extends Model
{
    protected $table = 'mas_batch_split';

    protected $fillable = [
        'mas_batch_split_batch_id',
        'mas_batch_split_from_date',
        'mas_batch_split_to_date',
        'mas_batch_split_students',
        'mas_batch_split_batch_name',
        'mas_batch_split_status_id',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'mas_batch_split_batch_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'mas_batch_split_status_id');
    }
}
