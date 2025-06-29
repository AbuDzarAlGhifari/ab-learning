<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'description'];
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
