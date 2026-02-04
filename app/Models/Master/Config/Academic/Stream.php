<?php


namespace App\Models\Master\Config\Academic;

use App\Models\Master\BaseMasterModel;

class Stream extends BaseMasterModel
{
    protected $table = 'mas_stream';

    protected $fillable = [
        'mas_stream_name',
        'mas_stream_short_code',
        'mas_stream_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_stream_status_id', 1);
    }

    public function degrees()
    {
        return $this->belongsToMany(
            \App\Models\Master\Config\Academic\Degree::class,
            'mas_degree_stream',
            'mas_stream_id',
            'mas_degree_id'
        );
    }

    public function designations()
    {
        return $this->belongsToMany(
            Designation::class,
            'mas_stream_designation',
            'stream_id',
            'designation_id'
        );
    }

}
