<?php

namespace App\Models\Master\Config\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialisation extends Model
{
    protected $table = 'mas_specialisation';

    protected $fillable = [
        'mas_specialisation_code',
        'mas_specialisation_name',
        'mas_specialisation_is_active',
    ];

    public function degrees(): BelongsToMany
    {
        return $this->belongsToMany(
            Degree::class,
            'degree_specialisations'
        )->withTimestamps();
    }
}
