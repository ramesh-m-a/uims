<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ChartDocument extends Model
{
    use HasFactory;

    protected $table = 'chart_documents';

    protected $fillable = [

        // Relations
        'allocation_id',
        'examiner_id',
        'batch_id',

        // Version
        'document_version',
        'is_latest',

        // File
        'pdf_path',
        'pdf_disk',
        'pdf_status',
        'pdf_generated_at',
        'pdf_last_error',

        // Integrity
        'document_hash',

        // Audit
        'generated_by',
        'generated_role',
        'generated_at',

        // Regeneration
        'regenerated_from_id',

        // Status
        'status',
    ];

    protected $casts = [
        'is_latest' => 'boolean',
        'generated_at' => 'datetime',
        'pdf_generated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function allocation()
    {
        return $this->belongsTo(TempExaminerAssignedDetail::class, 'allocation_id');
    }

    public function parentDocument()
    {
        return $this->belongsTo(self::class, 'regenerated_from_id');
    }

    public function childDocuments()
    {
        return $this->hasMany(self::class, 'regenerated_from_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeLatest($query)
    {
        return $query->where('is_latest', true);
    }

    public function scopeForAllocation($query, $allocationId)
    {
        return $query->where('allocation_id', $allocationId);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getPdfUrlAttribute()
    {
        if (!$this->pdf_path) return null;

        $disk = $this->pdf_disk ?? 'public';

        return Storage::disk($disk)->url($this->pdf_path);
    }

    public function isRegenerated()
    {
        return !is_null($this->regenerated_from_id);
    }
}
