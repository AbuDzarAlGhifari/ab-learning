<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserProfile;

class ProfileController extends Controller
{
    public function show(Request $req)
    {
        $profile = $req->user()->profile;
        if (! $profile) {
            // jika belum ada, return minimal data
            return response()->json(null, 204);
        }
        return response()->json($profile);
    }

    public function update(Request $req)
    {
        $data = $req->validate([
            'photo'       => 'nullable|file|image|max:2048',
            'title'       => 'nullable|string|max:255',
            'bio'         => 'nullable|string',
            'social_link' => 'nullable|url|max:255',
        ]);

        $profile = $req->user()->profile
            ?? new UserProfile(['user_id' => $req->user()->id]);

        if ($req->hasFile('photo')) {
            $path = $req->file('photo')->store('profiles', 'public');
            $profile->photo_url = Storage::url($path);
        }
        $profile->title       = $data['title']       ?? $profile->title;
        $profile->bio         = $data['bio']         ?? $profile->bio;
        $profile->social_link = $data['social_link'] ?? $profile->social_link;
        $profile->save();

        return response()->json($profile);
    }
}
