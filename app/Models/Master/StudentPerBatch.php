<?php

namespace App\Models\Master;

use App\Models\Master\Common\Degree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPerBatch extends Model
{
    use HasFactory;

    protected $table = 'mas_student_per_batch';

    protected $fillable = [
        'mas_student_per_batch_subject_id',
        'mas_student_per_batch_degree_id',
        'mas_student_per_batch_total_number',
        'mas_student_per_batch_per_day',
        'mas_student_per_batch_status_id',
        'created_by',
        'updated_by',];

    // cast small ints to integers
    protected $casts = ['mas_student_per_batch_subject_id' => 'integer', 'mas_student_per_batch_total_number' => 'integer', 'mas_student_per_batch_per_day' => 'integer', 'mas_student_per_batch_status_id' => 'integer',];

    // Relationships (assumes these models exist)
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'mas_student_per_batch_subject_id');
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class, 'mas_student_per_batch_degree_id', 'mas_degree_id');
        // if Degree primary key is 'mas_degree_id' adjust accordingly
    }
}
