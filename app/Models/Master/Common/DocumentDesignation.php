<?php

namespace App\Models\Master\Common;

use App\Models\Master\BaseMasterModel;
use App\Models\Master\Config\Academic\Designation;
use Illuminate\Database\Eloquent\Model;

class MasDocumentDesignation extends BaseMasterModel
{
    protected $table = 'mas_document_designation';

    protected $fillable = [
        'mas_document_id',
        'mas_designation_id',
        'mas_document_is_required',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'mas_document_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'mas_designation_id');
    }
}
