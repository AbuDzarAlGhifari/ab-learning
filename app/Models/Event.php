<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'description', 'start_at', 'end_at', 'image_url', 'link', 'order'];
    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime'];
}
