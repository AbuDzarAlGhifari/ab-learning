<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Quiz extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'created_by'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
