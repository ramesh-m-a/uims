<?php

namespace App\Models\Master\Config\Academic;

use App\Models\Master\Common\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use SoftDeletes;

    protected $table = 'mas_designation';

    protected $fillable = [
        'mas_designation_name',
        'mas_designation_status_id',
        'created_by',
        'updated_by',
    ];

    /* =========================
     | RELATIONS
     ========================= */

    public function streams()
    {
        return $this->belongsToMany(
            Stream::class,
            'mas_stream_designation',
            'designation_id',
            'stream_id'
        )->withPivot('status_id')
            ->withTimestamps();
    }

    /**
     * Virtual relation for UI (keeps config untouched)
     * Allows: stream.mas_stream_name
     * Returns ALL streams as comma-separated string
     */
    public function getStreamAttribute()
    {
      //  dd('STREAM ACCESSOR CALLED', ['id' => $this->id]);

        // ✅ Force relationship query directly (bypasses accessor/attribute system completely)
        $streams = $this->streams()->get();

        if ($streams->isEmpty()) {
            return null;
        }

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

        return (object) [
            'mas_stream_name' => new \Illuminate\Support\HtmlString($html)
        ];
    }


    public function constituency()
    {
        return $this->belongsToMany(
            Constituency::class,
            'mas_designation_constituency',
            'designation_id',
            'constituency_id'
        )->limit(1);
    }

    public function status()
    {
        return $this->belongsTo(
            Status::class,
            'mas_designation_status_id'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('mas_designation_status_id', 1);
    }

}
