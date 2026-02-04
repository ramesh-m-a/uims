<?php

namespace App\Models\Master\Config\Exam;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Common\Status;

class BatchRange extends Model
{
    protected $table = 'mas_batch_range';

    protected $fillable = [
        'mas_batch_range_batch_id',
        'mas_batch_range_from_date',
        'mas_batch_range_to_date',
        'mas_batch_range_students',
        'mas_batch_range_batch_name',
        'mas_batch_range_status_id',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'mas_batch_range_batch_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'mas_batch_range_status_id');
    }
    public function getMasBatchRangeFromDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function getMasBatchRangeToDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d-m-Y') : null;
    }

    /* =========================
    | SCOPES
    ========================= */

    public function scopeActive($query)
    {
        return $query->where('mas_batch_range_status_id', 1);
    }

}
