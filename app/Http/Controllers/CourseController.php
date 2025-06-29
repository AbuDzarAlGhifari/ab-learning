<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        return Course::all();
    }
    public function store(Request $r)
    {
        $data = $r->validate(['title' => 'required', 'description' => 'nullable']);
        return Course::create($data);
    }
    public function show(Course $course)
    {
        return $course;
    }
    public function update(Request $r, Course $course)
    {
        $data = $r->validate(['title' => 'required', 'description' => 'nullable']);
        $course->update($data);
        return $course;
    }
    public function destroy(Course $course)
    {
        $course->delete();
        return response()->noContent();
    }
    // List students enrolled for admin/teacher:
    public function students(Course $course)
    {
        return $course->enrollments()->with('student:id,name,email')->get()->pluck('student');
    }
}
