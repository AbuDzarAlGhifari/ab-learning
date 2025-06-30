<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['attempt_id', 'question_id', 'answer_text', 'answer_image', 'is_correct'];

    protected $casts = [
        'answer_text' => 'array',
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
