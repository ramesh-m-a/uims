<?php

namespace app\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

abstract class MasterBase extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && $model->usesSoftDeletes()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where($this->getStatusColumn(), 50);
    }

    protected function getStatusColumn(): string
    {
        foreach ($this->getAttributes() as $key => $value) {
            if (str_ends_with($key, '_status_id') || str_ends_with($key, '_status')) {
                return $key;
            }
        }

        return 'Status';
    }
}
