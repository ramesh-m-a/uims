<?php

namespace App\Models\Master\Config\Exam;

use App\Models\Master\BaseMasterModel;
use App\Models\Master\Config\Academic\Stream;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;

class RevisedScheme extends BaseMasterModel
{
    use SoftDeletes;

    protected $table = 'mas_revised_scheme';

    protected $fillable = [
        'mas_revised_scheme_name',
        'mas_revised_scheme_short_name',
        'mas_revised_scheme_status_id',
    ];

    // ✅ ADD THIS
    protected $appends = ['stream'];

    public function scopeActive($query)
    {
        return $query->where('mas_revised_scheme_status_id', 1);
    }

    public function streams()
    {
        return $this->belongsToMany(
            Stream::class,
            'mas_revised_scheme_stream',
            'mas_revised_scheme_id',
            'mas_stream_id'
        )->wherePivot('mas_revised_scheme_stream_status_id', 1)
            ->whereNull('mas_revised_scheme_stream.deleted_at');
    }

    // ✅ ADD THIS ACCESSOR (FOR CRUD TABLE COLUMN: stream.mas_stream_name)
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
}
