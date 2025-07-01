<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_url',
        'feedback',
        'score',
        'submitted_at',
        'graded_at',
        'graded_by'
    ];
    protected $casts = ['submitted_at' => 'datetime', 'graded_at' => 'datetime'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
