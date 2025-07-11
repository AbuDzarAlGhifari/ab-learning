<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'photo_url',
        'title',
        'bio',
        'social_link'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
