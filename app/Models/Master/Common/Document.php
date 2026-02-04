<?php


namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;
use App\Models\Master\Config\Academic\Designation;

class Document extends BaseMasterModel
{
    protected $table = 'mas_document';

    protected $fillable = [
        'mas_document_name',
        'mas_document_description',
        'mas_document_status_id',
        'mas_document_sort_order',
        'mas_document_type',
        'mas_document_is_required_global',
    ];

    public function scopeActive($query)
    {
        return $query->where('mas_document_status_id', 1);
    }

    protected $casts = [
        'mas_document_is_required_global' => 'boolean',
    ];

    public function designations()
    {
        return $this->belongsToMany(
            Designation::class,
            'mas_document_designation',
            'mas_document_id',
            'mas_designation_id'
        )->withPivot('mas_document_is_required')
            ->withTimestamps();
    }
}
