<?php

namespace App\Models\Master\Config\Academic;

use App\Models\Master\Common\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;

class Degree extends Model
{
    use SoftDeletes;

    protected $table = 'mas_degree';

    protected $fillable = [
        'mas_degree_code',
        'mas_degree_name',
        'mas_degree_level_id',
        'mas_degree_specialisation_mode',
        'mas_degree_status_id',
    ];

    /* =========================
     | RELATIONS
     ========================= */

    public function specialisations()
    {
        return $this->belongsToMany(
            \App\Models\Master\Config\Academic\Specialisation::class,
            'mas_degree_specialisation',
            'degree_id',
            'specialisation_id'
        )->wherePivot('mas_degree_specialisation_status_id', 1);
    }

    public function streams()
    {
        return $this->belongsToMany(
            Stream::class,
            'mas_degree_stream',
            'mas_degree_id',
            'mas_stream_id'
        )->withPivot('mas_degree_stream_status_id')
            ->withTimestamps();
    }

    public function level()
    {
        return $this->belongsTo(
            DegreeLevel::class,
            'mas_degree_level_id'
        );
    }

    public function status()
    {
        return $this->belongsTo(
            Status::class,
            'mas_degree_status_id'
        );
    }

    /* =========================
     | SCOPES
     ========================= */

    public function scopeActive($query)
    {
        return $query->where('mas_degree_status_id', 1);
    }

    /* =========================
     | ACCESSORS (SAFE)
     ========================= */

    // Always keep integer for logic
    public function getMasDegreeSpecialisationModeAttribute($value)
    {
        return (int) $value;
    }

    // Label only for UI when explicitly needed
    public function getMasDegreeSpecialisationModeLabelAttribute(): string
    {
        return match ($this->mas_degree_specialisation_mode) {
            0 => 'None',
            1 => 'Optional',
            2 => 'Mandatory',
            default => '-',
        };
    }

    /**
     * Virtual UI-only accessor (keeps business clean)
     * Allows: $degree->stream->mas_stream_name
     */
    public function getStreamAttribute()
    {
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
            'mas_stream_name' => new HtmlString($html)
        ];
    }

    public function getSpecialisationAttribute()
    {
        $specialisations = $this->specialisations()->get();

        if ($specialisations->isEmpty()) {
            return null;
        }

        $html = '<div style="display:flex;flex-direction:column;gap:4px;">';

        foreach ($specialisations as $i => $spec) {
            $bg = $i % 2 === 0 ? '#e5e7eb' : '#f3f4f6';

            $html .= '<span style="
            background:' . $bg . ';
            padding:4px 8px;
            border-radius:6px;
            font-size:12px;
            display:inline-block;
            width:fit-content;
        ">' . e($spec->mas_specialisation_name) . '</span>';
        }

        $html .= '</div>';

        return (object) [
            'mas_specialisation_name' => new HtmlString($html)
        ];
    }

}
