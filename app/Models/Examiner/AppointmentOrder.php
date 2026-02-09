<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class AppointmentOrder extends Model
{
    use HasFactory;

    protected $table = 'appointment_orders';

    protected $fillable = [

        // Relations
        'allocation_id',
        'examiner_id',
        'batch_id',

        // Identity
        'order_number',
        'order_version',
        'is_latest',

        // File
        'pdf_path',
        'pdf_disk',

        // Integrity
        'document_hash',
        'qr_payload',

        // Audit
        'generated_by',
        'generated_role',
        'generated_at',

        // Regeneration
        'regenerated_from_id',
        'remarks',

        // Status
        'status',
    ];

    protected $casts = [
        'qr_payload'   => 'array',
        'is_latest'    => 'boolean',
        'generated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function allocation()
    {
        return $this->belongsTo(TempExaminerAllocation::class, 'allocation_id');
        // Change model if final allocation table is different
    }

    public function examiner()
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function parentOrder()
    {
        return $this->belongsTo(self::class, 'regenerated_from_id');
    }

    public function childOrders()
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
        if (!$this->pdf_path) {
            return null;
        }

        return Storage::disk($this->pdf_disk)->url($this->pdf_path);
    }

    public function isRegenerated()
    {
        return !is_null($this->regenerated_from_id);
    }
}
