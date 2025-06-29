<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['course_id', 'teacher_id', 'start_time', 'end_time'];
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
