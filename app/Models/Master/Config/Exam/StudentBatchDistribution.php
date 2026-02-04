<?php

namespace App\Models\Master\Config\Exam;

use App\Models\Master\BaseMasterModel;
use App\Models\Master\Config\Academic\Stream;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;

class StudentBatchDistribution extends BaseMasterModel
{
    use SoftDeletes;

    protected $table = 'mas_student_per_batch';

    protected $fillable = [
        'mas_student_per_batch_subject_id',
        'mas_student_per_batch_degree_id',
        'mas_student_per_batch_total_number',
        'mas_student_per_batch_per_day',
        'mas_student_per_batch_status_id',
    ];

    protected $appends = [
        'stream',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_student_per_batch_status_id', 1);
    }

    /* =====================================================
     | RELATIONS
     ===================================================== */
    public function streams()
    {
        return $this->belongsToMany(
            Stream::class,
            'mas_examiner_scheme_distribution_stream',
            'mas_examiner_scheme_distribution_id',
            'mas_stream_id'
        )
            ->wherePivot('status_id', 1)
            ->whereNull('mas_examiner_scheme_distribution_stream.deleted_at');
    }

    public function scheme()
    {
        return $this->belongsTo(
            RevisedScheme::class,
            'mas_examiner_scheme_distribution_scheme_id'
        );
    }

    /**
     * IMPORTANT:
     * Must support config column:
     * stream.mas_stream_name
     *
     * So return REAL object with property
     */
    /* =====================================================
        | ACCESSORS
        ===================================================== */

    public function getStreamAttribute()
    {
        $streams = $this->relationLoaded('streams')
            ? $this->streams
            : $this->streams()->get();

        if ($streams->isEmpty()) return null;

        $html = '<div style="display:flex;flex-direction:column;gap:4px;">';

        foreach ($streams as $i => $stream) {

            $bg = $i % 2 === 0 ? '#e5e7eb' : '#f3f4f6';

            $html .= '<span style="
                background:' . $bg . ';
                padding:4px 8px;
                border-radius:6px;
                font-size:12px;
                display:inline-block;
                width:fit-content;
            ">' . e($stream->mas_stream_name) . '</span>';
        }

        $html .= '</div>';

        return (object)[
            'mas_stream_name' => new HtmlString($html)
        ];
    }

    public function getSchemeDisplayAttribute()
    {
        return $this->scheme?->mas_revised_scheme_name ?? '—';
    }
}
