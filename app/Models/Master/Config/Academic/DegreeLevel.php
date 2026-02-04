<?php

namespace App\Models\Master\Config\Academic;

use Illuminate\Database\Eloquent\Model;

class DegreeLevel extends Model
{
    /**
     * IMPORTANT
     */
    protected $table = 'mas_degree_level';

    protected $fillable = [
        'mas_degree_level_code',       // UG, PG, SS
        'mas_degree_level_name',       // Under Graduate, etc
        'mas_degree_level_order_no',
        'mas_degree_level_status_id',
    ];

    public $timestamps = true;
}
