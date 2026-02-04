<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;

class Category extends BaseMasterModel
{
    protected $table = 'mas_category';

    protected $fillable = [
        'mas_category_name',
        'mas_category_status_id',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_category_status_id', 1);
    }
}
