<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Model;

class DocumentDetails extends Model
{
    protected $table = 'document_details';

    protected $fillable = [
        'document_details_basic_details_id',
        'document_details_document_id',
        'document_details_file_path',
        'document_details_status',
    ];

    public $timestamps = true;
}
