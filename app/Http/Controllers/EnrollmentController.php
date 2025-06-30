<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Enrollment;

class EnrollmentController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate(['course_id' => 'required|exists:courses,id']);
        $data['student_id'] = $r->user()->id;
        return Enrollment::create($data);
    }
    public function myCourses(Request $req)
    {
        return $req->user()->enrollments()
            ->whereHas('payments', fn($q) => $q->where('status', 'paid'))
            ->with('course')
            ->get()
            ->pluck('course');
    }
}
