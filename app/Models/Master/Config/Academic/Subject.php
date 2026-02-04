<?php

namespace App\Models\Master\Config\Academic;

use App\Models\Master\BaseMasterModel;
use App\Models\Master\Common\Status;

class Subject extends BaseMasterModel
{
    protected $table = 'mas_subject';

    protected $fillable = [
        'mas_subject_name',
        'mas_subject_stream_id',
        'mas_subject_degree_id',
        'mas_subject_department_id',
        'mas_subject_status_id',
    ];

    protected $appends = [
        'mas_stream_name',
        'mas_degree_name',
        'mas_department_name',
    ];

    /* =========================
     | SCOPES
     ========================= */

    public function scopeActive($query)
    {
        return $query->where('mas_subject_status_id', 1);
    }

    /* =========================
     | RELATIONS
     ========================= */

    public function stream()
    {
        return $this->belongsTo(Stream::class, 'mas_subject_stream_id');
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class, 'mas_subject_degree_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'mas_subject_department_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'mas_subject_status_id');
    }

    /* =========================
     | VIRTUAL COLUMNS FOR TABLE
     ========================= */

    public function getMasStreamNameAttribute(): string
    {
        return $this->stream?->mas_stream_name ?? '-';
    }

    public function getMasDegreeNameAttribute(): string
    {
        return $this->degree?->mas_degree_name ?? '-';
    }

    public function getMasDepartmentNameAttribute(): string
    {
        return $this->department?->mas_department_name ?? '-';
    }
}
