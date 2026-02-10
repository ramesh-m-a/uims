<?php

namespace App\Models;

use App\Models\Admin\User;
use App\Models\Master\Config\Exam\Batch;
use App\Models\TempExaminerAllocation;
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
        'pdf_status',
        'pdf_generated_at',
        'pdf_last_error',

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
        'qr_payload'         => 'array',
        'is_latest'          => 'boolean',
        'generated_at'       => 'datetime',
        'pdf_generated_at'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function allocation()
    {
        return $this->belongsTo(TempExaminerAllocation::class, 'allocation_id');
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

        $disk = $this->pdf_disk ?? 'local';

        return Storage::disk($disk)->url($this->pdf_path);
    }

    public function isRegenerated()
    {
        return !is_null($this->regenerated_from_id);
    }
}
