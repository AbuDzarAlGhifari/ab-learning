<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\Event;
use App\Models\User;

class SearchController extends Controller
{
    public function index(Request $req)
    {
        $q = $req->validate([
            'query' => 'required|string',
            'type'  => 'nullable|string|in:courses,materials,quizzes,events,teachers,all'
        ])['query'];

        $type = $req->input('type', 'all');
        $results = [];

        if ($type === 'all' || $type === 'courses') {
            $results['courses'] = Course::where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->get();
        }

        if ($type === 'all' || $type === 'materials') {
            $results['materials'] = Material::where('title', 'like', "%{$q}%")
                ->orWhere('content', 'like', "%{$q}%")
                ->get();
        }

        if ($type === 'all' || $type === 'quizzes') {
            $results['quizzes'] = Quiz::where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->get();
        }

        if ($type === 'all' || $type === 'events') {
            $results['events'] = Event::where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->get();
        }

        if ($type === 'all' || $type === 'teachers') {
            $results['teachers'] = User::whereHas('role', fn($q2) => $q2->where('name', 'teacher'))
                ->where('name', 'like', "%{$q}%")
                ->get(['id', 'name', 'email']);
        }

        return response()->json($results);
    }
}
