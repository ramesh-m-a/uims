<?php

namespace App\Models\Master\Config\Academic;

use Illuminate\Database\Eloquent\Model;

class DegreeStream extends Model
{
    protected $table = 'mas_degree_stream';

    protected $fillable = [
        'mas_degree_id',
        'mas_stream_id',
        'mas_degree_stream_status_id',
    ];

    public $timestamps = false;
}
