<?php

namespace App\Models\Master\Config\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQualification extends Model
{
    protected $table = 'user_qualifications';

    protected $fillable = ['user_id', 'degree_id', 'specialisation_id', 'year_of_award', 'registration_number',];

    /* =========================
     | RELATIONS
     ========================= */

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }

    public function specialisation(): BelongsTo
    {
        return $this->belongsTo(Specialisation::class);
    }
}
