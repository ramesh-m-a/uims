<?php

namespace App\Models\Master;

use App\Models\Traits\HasAuditTrail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseMasterModel extends Model
{
    use SoftDeletes;
    use HasAuditTrail; // created_by / updated_by
    use Auditable;     // audit_logs table

    protected $guarded = [];

    protected $dates = ['deleted_at'];
}
