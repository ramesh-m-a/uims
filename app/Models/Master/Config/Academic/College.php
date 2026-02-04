<?php

namespace App\Models\Master\Config\Academic;

use App\Models\Master\Common\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class College extends Model
{
    use SoftDeletes;

    protected $table = 'mas_college';

    protected $fillable = [
        'mas_college_stream_id',
        'mas_college_code',
        'mas_college_name',
        'mas_college_exam_centre',
        'mas_college_type',
        'mas_college_is_internal',
        'mas_college_status_id',
        'created_by',
        'updated_by',
    ];

    /* =========================
     | RELATIONS
     ========================= */

    public function stream()
    {
        return $this->belongsTo(
            Stream::class,
            'mas_college_stream_id'
        );
    }

    public function status()
    {
        return $this->belongsTo(
            Status::class,
            'mas_college_status_id'
        );
    }

    /* =========================
     | SCOPES (OPTIONAL BUT READY)
     ========================= */

    public function scopeActive($query)
    {
        return $query->where('mas_college_status_id', 1);
    }

    public function scopeInternal($query)
    {
        return $query->where('mas_college_is_internal', 1);
    }
}
