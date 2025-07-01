<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\User;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::whereHas('role', fn($q) => $q->where('name', 'teacher'))
            ->with('profile:id,user_id,photo_url,title,bio,social_link')
            ->get(['id', 'name', 'email', 'role_id']);

        // gabungkan data profil ke atribut
        return $teachers->map(fn($u) => [
            'id'          => $u->id,
            'name'        => $u->name,
            'photo_url'   => $u->profile?->photo_url,
            'title'       => $u->profile?->title,
            'bio'         => $u->profile?->bio,
            'social_link' => $u->profile?->social_link,
        ]);
    }
}
