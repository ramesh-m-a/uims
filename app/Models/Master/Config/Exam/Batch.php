<?php

namespace App\Models\Master\Config\Exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\Config\Academic\Stream;
use App\Models\Master\Config\Academic\College;
use App\Models\Master\Config\Exam\Subject;
use App\Models\Master\Config\Exam\RevisedScheme;
use App\Models\Master\Common\Year;
use App\Models\Master\Common\Month;
use App\Models\Master\Common\Status;

use App\Models\Master\Config\Exam\BatchCentreStudent;
use Illuminate\Support\Facades\DB;

// ✅ IMPORTANT

class Batch extends Model
{
    protected $table = 'mas_batch';

    protected $fillable = [
        'mas_batch_stream_id',
        'mas_batch_year_id',
        'mas_batch_subject_id',
        'mas_batch_month_id',
        'mas_batch_centre_id',
        'mas_batch_total_students',
        'mas_batch_status_id',
        'mas_batch_total_batches',
        'mas_batch_attached_centre_id',
        'mas_batch_start_date',
        'mas_batch_degree_id',
        'mas_batch_revised_scheme_id',
        'mas_batch_is_updated',
        'mas_batch_old_centre_id',
        'mas_batch_old_attached_centre_id',
        'created_by',
        'updated_by',
    ];

    /* =========================
     | RELATIONS
     ========================= */

    public function stream()
    {
        return $this->belongsTo(Stream::class, 'mas_batch_stream_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'mas_batch_year_id');
    }

    public function month()
    {
        return $this->belongsTo(Month::class, 'mas_batch_month_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'mas_batch_subject_id');
    }

    public function scheme()
    {
        return $this->belongsTo(RevisedScheme::class, 'mas_batch_revised_scheme_id');
    }

    public function centre()
    {
        return $this->belongsTo(College::class, 'mas_batch_centre_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'mas_batch_status_id');
    }

    /* =========================
     | NORMALIZED ALLOCATION RELATION (FIXED)
     ========================= */

    public function centreAllocations()
    {
        return $this->hasMany(
            BatchCentreStudent::class,
            'mas_batch_id',
            'id'
        )->whereHas('batchCentre', function ($q) {
            $q->where('status_id', 1);
        });
    }

    /* =========================
     | GRID ACCESSOR (PRODUCTION PARITY)
     ========================= */

    public function getMasBatchAttachedCentreIdAttribute()
    {
        $rows = DB::table('mas_batch_centre as bc')
            ->join('mas_batch_centre_student as bcs', function ($join) {
                $join->on('bc.mas_batch_id', '=', 'bcs.mas_batch_id')
                    ->on('bc.mas_centre_id', '=', 'bcs.mas_centre_id');
            })
            ->join('mas_college as c', 'c.id', '=', 'bc.mas_centre_id')
            ->where('bc.mas_batch_id', $this->id)
            ->where('bc.status_id', 1)
            ->select('c.mas_college_name', 'bcs.student_count')
            ->orderBy('c.mas_college_name')
            ->get();

        if ($rows->isEmpty()) {
            return collect(['—']);
        }

        return $rows->map(function ($row) {
            return $row->mas_college_name . ' (' . $row->student_count . ')';
        });
    }

    /* =========================
     | SCOPES
     ========================= */

    public function scopeActive($query)
    {
        return $query->where('mas_batch_status_id', 1);
    }

    /* =========================
     | AUDIT
     ========================= */

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function getMasBatchStartDateFormattedAttribute(): ?string
    {
        return $this->attributes['mas_batch_start_date']
            ? \Carbon\Carbon::parse($this->attributes['mas_batch_start_date'])->format('d-m-Y')
            : null;
    }

}
