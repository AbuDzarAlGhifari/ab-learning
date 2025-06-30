<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'student_id', 'started_at', 'completed_at', 'score'];

    protected $dates = ['started_at', 'completed_at'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'attempt_id');
    }
}
